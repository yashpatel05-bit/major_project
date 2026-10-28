import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  Pressable,
  Image,
  Alert,
  ActivityIndicator,
  StyleSheet,
  Platform,
  StatusBar,
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { updateProfilePhoto } from '../api/ApiClient';
import { SERVER_IP } from '../config/AppConfig';
import { colors } from '../theme/colors';

export default function ProfileScreen({ navigation }) {
  const { user, logout, updateUser } = useAuth();
  const [uploading, setUploading] = useState(false);

  const handleLogout = async () => {
    Alert.alert('Logout', 'Are you sure you want to log out?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Logout',
        style: 'destructive',
        onPress: async () => {
          await logout();
          navigation.reset({ index: 0, routes: [{ name: 'Login' }] });
        },
      },
    ]);
  };

  const handlePickProfileImage = async () => {
    try {
      const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
      if (status !== 'granted') {
        Alert.alert(
          'Permission Required',
          'Sorry, we need photo gallery access to let you choose a profile picture.'
        );
        return;
      }

      const result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ['images'],
        allowsEditing: true,
        aspect: [1, 1],
        quality: 0.8,
      });

      if (!result.canceled && result.assets && result.assets[0]) {
        const imageAsset = result.assets[0];
        setUploading(true);

        const formData = new FormData();
        const filename = imageAsset.uri.split('/').pop() || 'avatar.jpg';
        const match = /\.(\w+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : 'image/jpeg';

        formData.append('profile_photo', {
          uri: imageAsset.uri,
          name: filename,
          type,
        });

        const res = await updateProfilePhoto(formData);

        if (res.data?.success && res.data?.data) {
          updateUser(res.data.data);
          Alert.alert('Success 🎉', 'Profile picture updated successfully!');
        } else {
          Alert.alert('Upload Failed', res.data?.message || 'Could not update profile photo');
        }
      }
    } catch (err) {
      Alert.alert('Error', 'An error occurred while uploading your photo.');
    } finally {
      setUploading(false);
    }
  };

  const avatarUri = user?.profile_photo
    ? (user.profile_photo.startsWith('http')
        ? user.profile_photo
        : `http://${SERVER_IP}:8000/storage/${user.profile_photo}`)
    : null;

  return (
    <View style={styles.container}>
      {/* Header Bar */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>My Profile</Text>
        <View style={{ width: 36 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* User Card */}
        <View style={styles.profileCard}>
          <Pressable style={styles.avatarContainer} onPress={handlePickProfileImage}>
            {uploading ? (
              <View style={[styles.avatar, styles.loadingAvatar]}>
                <ActivityIndicator color="#FFFFFF" size="small" />
              </View>
            ) : avatarUri ? (
              <Image source={{ uri: avatarUri }} style={styles.avatarImage} />
            ) : (
              <View style={styles.avatarCircle}>
                <Text style={styles.avatarInitial}>
                  {user ? user.name.charAt(0).toUpperCase() : '?'}
                </Text>
              </View>
            )}
            <View style={styles.cameraBadge}>
              <Ionicons name="camera" size={13} color={colors.primaryDark} />
            </View>
          </Pressable>

          <Text style={styles.userName}>{user?.name || 'Guest User'}</Text>
          <Text style={styles.userEmail}>{user?.email || 'Not logged in'}</Text>
          
          <Pressable style={styles.changePhotoBtn} onPress={handlePickProfileImage}>
            <Ionicons name="camera-outline" size={15} color={colors.primary} style={{ marginRight: 6 }} />
            <Text style={styles.changePhotoText}>Upload Photo</Text>
          </Pressable>
        </View>

        {/* Info Items */}
        <View style={styles.infoSection}>
          <Text style={styles.sectionLabel}>Account Information</Text>
          <InfoRow iconName="mail-outline" label="Email Address" value={user?.email || 'N/A'} />
          <InfoRow iconName="call-outline" label="Phone Number" value={user?.phone || 'Not added'} />
          <InfoRow iconName="location-outline" label="Default Address" value={user?.address || 'Not added'} />
        </View>

        {/* Quick Links */}
        <View style={styles.linksSection}>
          <Text style={styles.sectionLabel}>Cafe Features</Text>
          <LinkRow
            iconName="receipt-outline"
            label="My Order History & Live Track"
            onPress={() => navigation.navigate('OrderHistory')}
          />
          <LinkRow
            iconName="restaurant-outline"
            label="My Table Reservations & Status"
            onPress={() => navigation.navigate('TableBookingHistory')}
          />
          <LinkRow
            iconName="calendar-outline"
            label="Reserve a New Table"
            onPress={() => navigation.navigate('TableBooking')}
          />
          <LinkRow
            iconName="location-outline"
            label="Saved Delivery Addresses"
            onPress={() => navigation.navigate('Address')}
          />
          <LinkRow
            iconName="chatbubble-ellipses-outline"
            label="Ratings & Feedback"
            onPress={() => navigation.navigate('Feedback', { orderId: null })}
          />
          <LinkRow
            iconName="information-circle-outline"
            label="About Sip & Bite Cafe & Food"
            onPress={() => navigation.navigate('About')}
          />
        </View>

        {/* Logout Button */}
        <Pressable style={styles.logoutButton} onPress={handleLogout}>
          <Ionicons name="log-out-outline" size={18} color="#C62828" style={{ marginRight: 8 }} />
          <Text style={styles.logoutText}>Logout Account</Text>
        </Pressable>

        <Text style={styles.versionText}>Sip & Bite Cafe & Food • Mobile App</Text>
      </ScrollView>
    </View>
  );
}

function InfoRow({ iconName, label, value }) {
  return (
    <View style={styles.infoRow}>
      <Ionicons name={iconName} size={19} color={colors.primary} style={{ marginRight: 12 }} />
      <View style={styles.infoContent}>
        <Text style={styles.infoLabel}>{label}</Text>
        <Text style={styles.infoValue}>{value}</Text>
      </View>
    </View>
  );
}

function LinkRow({ iconName, label, onPress }) {
  return (
    <Pressable style={styles.linkRow} onPress={onPress}>
      <Ionicons name={iconName} size={19} color={colors.primary} style={{ marginRight: 12 }} />
      <Text style={styles.linkLabel}>{label}</Text>
      <Ionicons name="chevron-forward" size={16} color={colors.textPlaceholder} />
    </Pressable>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  headerBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? ((StatusBar.currentHeight || 28) + 8) : 12,
    paddingBottom: 14,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  backButton: { width: 36, height: 36, justifyContent: 'center' },
  headerTitle: {
    fontSize: 18,
    fontWeight: '900',
    color: colors.primaryDark,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  scrollContent: { padding: 16, paddingBottom: 40 },
  profileCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 22,
    padding: 22,
    alignItems: 'center',
    marginBottom: 20,
    borderWidth: 1,
    borderColor: colors.border,
    elevation: 2,
  },
  avatarContainer: { position: 'relative', marginBottom: 12 },
  avatarImage: { width: 84, height: 84, borderRadius: 42, borderWidth: 2.5, borderColor: colors.primary },
  avatarCircle: {
    width: 84,
    height: 84,
    borderRadius: 42,
    backgroundColor: colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingAvatar: { width: 84, height: 84, borderRadius: 42, backgroundColor: colors.primary, justifyContent: 'center', alignItems: 'center' },
  avatarInitial: { fontSize: 34, fontWeight: '900', color: '#FFFFFF' },
  cameraBadge: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    backgroundColor: '#FFFFFF',
    borderRadius: 13,
    width: 26,
    height: 26,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 3,
    borderWidth: 1,
    borderColor: colors.border,
  },
  userName: { fontSize: 19, fontWeight: '900', color: colors.textDark },
  userEmail: { fontSize: 13, color: colors.textMuted, marginTop: 2, marginBottom: 12, fontWeight: '500' },
  changePhotoBtn: {
    backgroundColor: colors.surfaceAlt,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingVertical: 7,
    flexDirection: 'row',
    alignItems: 'center',
  },
  changePhotoText: { color: colors.primaryDark, fontSize: 12, fontWeight: '800' },

  infoSection: { marginBottom: 20 },
  linksSection: { marginBottom: 20 },
  sectionLabel: { fontSize: 11.5, fontWeight: '800', color: colors.textMuted, textTransform: 'uppercase', marginBottom: 10, letterSpacing: 0.8 },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 14,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: colors.border,
  },
  infoContent: { flex: 1 },
  infoLabel: { fontSize: 10.5, color: colors.textMuted, fontWeight: '600' },
  infoValue: { fontSize: 13.5, fontWeight: '800', color: colors.textDark, marginTop: 2 },
  linkRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 15,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: colors.border,
  },
  linkLabel: { flex: 1, fontSize: 13.5, fontWeight: '700', color: colors.textDark },

  logoutButton: {
    backgroundColor: '#FEE2E2',
    borderRadius: 16,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    flexDirection: 'row',
    marginTop: 10,
  },
  logoutText: { color: '#DC2626', fontSize: 14, fontWeight: '800' },
  versionText: { textAlign: 'center', color: colors.textPlaceholder, fontSize: 11.5, marginTop: 20, fontWeight: '600' },
});
