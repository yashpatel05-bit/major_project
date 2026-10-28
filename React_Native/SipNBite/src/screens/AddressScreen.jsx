import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  FlatList,
  ActivityIndicator,
  StyleSheet,
  Alert,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { getUserAddresses, storeAddress, deleteAddress } from '../api/ApiClient';
import { colors } from '../theme/colors';

export default function AddressScreen({ navigation }) {
  const { user } = useAuth();
  const [addresses, setAddresses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);

  // Form fields
  const [title, setTitle] = useState('');
  const [addressLine, setAddressLine] = useState('');
  const [city, setCity] = useState('');
  const [pincode, setPincode] = useState('');
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    fetchAddresses();
  }, []);

  const fetchAddresses = async () => {
    if (!user) return;
    setLoading(true);
    try {
      const res = await getUserAddresses(user.id);
      if (res.data?.success) setAddresses(res.data.data || []);
    } catch (_) {}
    setLoading(false);
  };

  const handleSave = async () => {
    const newErrors = {};
    if (!title.trim()) {
      newErrors.title = 'Please enter Address Label';
    }
    if (!addressLine.trim()) {
      newErrors.addressLine = 'Please enter Address Line';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      const firstError = Object.values(newErrors)[0];
      Alert.alert('Please Fill Required Field', firstError);
      return;
    }
    setErrors({});

    setSaving(true);
    try {
      const res = await storeAddress({
        user_id: user.id,
        title: title.trim(),
        address_line: addressLine.trim(),
        city: city.trim() || null,
        pincode: pincode.trim() || null,
      });
      if (res.data?.success) {
        resetForm();
        fetchAddresses();
      } else {
        Alert.alert('Failed', res.data?.message || 'Could not save address');
      }
    } catch (err) {
      Alert.alert('Error', err.message || 'Network error');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = (id) => {
    Alert.alert('Delete Address', 'Are you sure?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Delete',
        style: 'destructive',
        onPress: async () => {
          try {
            await deleteAddress(id);
            fetchAddresses();
          } catch (_) {
            Alert.alert('Error', 'Could not delete address');
          }
        },
      },
    ]);
  };

  const resetForm = () => {
    setShowForm(false);
    setTitle('');
    setAddressLine('');
    setCity('');
    setPincode('');
    setErrors({});
  };

  const getAddressIconName = (t) => {
    const lower = (t || '').toLowerCase();
    if (lower.includes('home')) return 'home-outline';
    if (lower.includes('work') || lower.includes('office')) return 'business-outline';
    return 'location-outline';
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>My Addresses</Text>
        <Pressable
          onPress={() => setShowForm(!showForm)}
          style={styles.addHeaderButton}
        >
          <Ionicons name={showForm ? 'close' : 'add'} size={22} color="#FFFFFF" />
        </Pressable>
      </View>

      {/* Add Form */}
      {showForm && (
        <View style={styles.formCard}>
          <Text style={styles.formTitle}>Add New Address</Text>
          
          <Text style={styles.fieldLabel}>Address Label *</Text>
          <TextInput
            style={[styles.input, errors.title && styles.inputError]}
            value={title}
            onChangeText={(text) => {
              setTitle(text);
              if (errors.title) setErrors((prev) => ({ ...prev, title: null }));
            }}
          />
          {errors.title && <Text style={styles.errorText}>{errors.title}</Text>}

          <Text style={styles.fieldLabel}>Address Line *</Text>
          <TextInput
            style={[styles.input, errors.addressLine && styles.inputError]}
            value={addressLine}
            onChangeText={(text) => {
              setAddressLine(text);
              if (errors.addressLine) setErrors((prev) => ({ ...prev, addressLine: null }));
            }}
          />
          {errors.addressLine && <Text style={styles.errorText}>{errors.addressLine}</Text>}

          <View style={styles.inputRow}>
            <View style={styles.flexInput}>
              <Text style={styles.fieldLabel}>City</Text>
              <TextInput
                style={styles.input}
                value={city}
                onChangeText={setCity}
              />
            </View>
            <View style={styles.flexInput}>
              <Text style={styles.fieldLabel}>Pincode</Text>
              <TextInput
                style={styles.input}
                value={pincode}
                onChangeText={setPincode}
                keyboardType="number-pad"
              />
            </View>
          </View>
          <Pressable
            style={[styles.saveButton, saving && styles.disabledButton]}
            onPress={handleSave}
            disabled={saving}
          >
            {saving ? (
              <ActivityIndicator color="#FFF" />
            ) : (
              <Text style={styles.saveText}>Save Address</Text>
            )}
          </Pressable>
        </View>
      )}

      {/* Address List */}
      {loading ? (
        <View style={styles.loadingState}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      ) : addresses.length === 0 ? (
        <View style={styles.emptyState}>
          <Ionicons name="location-outline" size={50} color={colors.textMuted} style={{ marginBottom: 12 }} />
          <Text style={styles.emptyTitle}>No Addresses Saved</Text>
          <Text style={styles.emptySub}>Add your home or office address for faster delivery.</Text>
        </View>
      ) : (
        <FlatList
          data={addresses}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          renderItem={({ item }) => (
            <View style={styles.addressCard}>
              <View style={styles.iconBox}>
                <Ionicons name={getAddressIconName(item.title)} size={22} color={colors.primary} />
              </View>
              <View style={styles.addressInfo}>
                <Text style={styles.addressTitle}>{item.title}</Text>
                <Text style={styles.addressText}>{item.address_line}</Text>
                {(item.city || item.pincode) && (
                  <Text style={styles.addressSub}>
                    {[item.city, item.pincode].filter(Boolean).join(' - ')}
                  </Text>
                )}
              </View>
              <Pressable
                onPress={() => handleDelete(item.id)}
                style={styles.deleteButton}
              >
                <Ionicons name="trash-outline" size={18} color={colors.error} />
              </Pressable>
            </View>
          )}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  headerBar: {
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderBottomColor: colors.border,
    borderBottomWidth: 1,
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? ((StatusBar.currentHeight || 28) + 8) : 12,
    paddingBottom: 14,
  },
  backButton: { width: 36, height: 36, justifyContent: 'center' },
  headerTitle: {
    color: colors.primaryDark,
    fontSize: 18,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  addHeaderButton: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 18,
    height: 36,
    justifyContent: 'center',
    width: 36,
  },

  // Form
  formCard: {
    backgroundColor: '#FFFFFF',
    borderBottomColor: colors.border,
    borderBottomWidth: 1,
    paddingHorizontal: 16,
    paddingVertical: 18,
  },
  formTitle: { color: colors.textDark, fontSize: 15, fontWeight: '800', marginBottom: 12 },
  fieldLabel: { fontSize: 12, fontWeight: '800', color: colors.textDark, marginBottom: 4 },
  input: {
    backgroundColor: colors.surfaceAlt,
    borderRadius: 12,
    color: colors.textDark,
    fontSize: 13.5,
    height: 46,
    marginBottom: 8,
    paddingHorizontal: 14,
    borderWidth: 1,
    borderColor: colors.border,
  },
  inputError: {
    borderColor: colors.error,
    borderWidth: 1.5,
    backgroundColor: '#FFFBFB',
  },
  errorText: {
    color: colors.error,
    fontSize: 11.5,
    fontWeight: '700',
    marginTop: -4,
    marginBottom: 8,
    marginLeft: 4,
  },
  inputRow: { flexDirection: 'row', gap: 10 },
  flexInput: { flex: 1 },
  saveButton: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 14,
    height: 46,
    justifyContent: 'center',
    marginTop: 4,
  },
  disabledButton: { opacity: 0.6 },
  saveText: { color: '#FFFFFF', fontSize: 14, fontWeight: '800' },

  // List
  listContent: { paddingHorizontal: 16, paddingTop: 16, paddingBottom: 30 },
  loadingState: { alignItems: 'center', flex: 1, justifyContent: 'center' },

  emptyState: { alignItems: 'center', paddingVertical: 60 },
  emptyTitle: { color: colors.textDark, fontSize: 18, fontWeight: '800' },
  emptySub: { color: colors.textMuted, fontSize: 13, marginTop: 4, textAlign: 'center' },

  // Address card
  addressCard: {
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    elevation: 1,
    flexDirection: 'row',
    marginBottom: 12,
    padding: 16,
    borderWidth: 1,
    borderColor: colors.border,
  },
  iconBox: {
    alignItems: 'center',
    backgroundColor: colors.surfaceAlt,
    borderRadius: 12,
    height: 42,
    justifyContent: 'center',
    width: 42,
  },
  addressInfo: { flex: 1, marginLeft: 14 },
  addressTitle: { color: colors.textDark, fontSize: 14, fontWeight: '800' },
  addressText: { color: colors.textMuted, fontSize: 12.5, marginTop: 2 },
  addressSub: { color: colors.textPlaceholder, fontSize: 11, marginTop: 2 },
  deleteButton: { padding: 8 },
});
