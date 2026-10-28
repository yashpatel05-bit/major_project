import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  ActivityIndicator,
  ScrollView,
  FlatList,
  StyleSheet,
  Alert,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { createBooking, getUserBookings, cancelBooking } from '../api/ApiClient';
import { colors } from '../theme/colors';

export default function TableBookingScreen({ navigation }) {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('BOOK'); // 'BOOK' or 'STATUS'

  // Booking Form State
  const [date, setDate] = useState('');
  const [time, setTime] = useState('');
  const [guests, setGuests] = useState('2');
  const [specialRequest, setSpecialRequest] = useState('');
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});

  // History State
  const [userBookings, setUserBookings] = useState([]);
  const [fetchingHistory, setFetchingHistory] = useState(false);

  useEffect(() => {
    if (activeTab === 'STATUS') {
      fetchHistory();
    }
  }, [activeTab, user]);

  const fetchHistory = async () => {
    if (!user) return;
    setFetchingHistory(true);
    try {
      const res = await getUserBookings(user.id);
      if (res.data?.success) {
        setUserBookings(res.data.data || []);
      }
    } catch (_) {
    } finally {
      setFetchingHistory(false);
    }
  };

  const handleSubmitBooking = async () => {
    if (!user) {
      Alert.alert('Login Required', 'Please login to reserve a table.');
      navigation.navigate('Login');
      return;
    }

    const newErrors = {};
    if (!date.trim()) {
      newErrors.date = 'Please enter Date of Visit';
    }
    if (!time.trim()) {
      newErrors.time = 'Please enter Preferred Time';
    }
    if (!guests || !guests.trim()) {
      newErrors.guests = 'Please select Number of Guests';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      const firstError = Object.values(newErrors)[0];
      Alert.alert('Please Fill Required Field', firstError);
      return;
    }
    setErrors({});

    setLoading(true);
    try {
      const req = {
        user_id: user.id,
        table_id: null,
        booking_date: date.trim(),
        booking_time: time.trim(),
        guests_count: parseInt(guests, 10) || 2,
        special_request: specialRequest.trim() || null,
      };

      const res = await createBooking(req);
      if (res.data?.success) {
        Alert.alert(
          '🎉 Table Reservation Requested!',
          'Your table booking has been submitted. Check the "My Status" tab for live confirmation.',
          [
            {
              text: 'View Status ->',
              onPress: () => setActiveTab('STATUS'),
            },
          ]
        );
        setDate('');
        setTime('');
        setSpecialRequest('');
      } else {
        Alert.alert('Booking Error', res.data?.message || 'Failed to request table booking');
      }
    } catch (err) {
      Alert.alert('Network Error', err.message || 'Could not connect to server.');
    } finally {
      setLoading(false);
    }
  };

  const handleCancelBooking = (bookingId) => {
    Alert.alert('Cancel Table', 'Are you sure you want to cancel this booking?', [
      { text: 'Keep Reservation', style: 'cancel' },
      {
        text: 'Yes, Cancel',
        style: 'destructive',
        onPress: async () => {
          try {
            const res = await cancelBooking(bookingId);
            if (res.data?.success) {
              Alert.alert('Cancelled', 'Your reservation was cancelled.');
              fetchHistory();
            }
          } catch (err) {
            Alert.alert('Error', err.message || 'Could not cancel.');
          }
        },
      },
    ]);
  };

  const renderStatusBadge = (status) => {
    const s = (status || 'pending').toLowerCase();
    if (s === 'confirmed' || s === 'approved') {
      return (
        <View style={[styles.statusBadge, { backgroundColor: '#DCFCE7', borderColor: '#86EFAC' }]}>
          <Text style={[styles.statusBadgeText, { color: '#15803D' }]}>✅ CONFIRMED BY CAFE</Text>
        </View>
      );
    }
    if (s === 'cancelled' || s === 'rejected') {
      return (
        <View style={[styles.statusBadge, { backgroundColor: '#FEE2E2', borderColor: '#FCA5A5' }]}>
          <Text style={[styles.statusBadgeText, { color: '#B91C1C' }]}>❌ CANCELLED</Text>
        </View>
      );
    }
    return (
      <View style={[styles.statusBadge, { backgroundColor: '#FEF3C7', borderColor: '#FDE68A' }]}>
        <Text style={[styles.statusBadgeText, { color: '#B45309' }]}>⏳ PENDING CONFIRMATION</Text>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      {/* Header Bar */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>Table Reservations</Text>
        <Pressable onPress={() => navigation.navigate('TableBookingHistory')} style={{ padding: 4 }}>
          <Ionicons name="list-outline" size={22} color={colors.primaryDark} />
        </Pressable>
      </View>

      {/* Segmented Tab Switcher */}
      <View style={styles.tabSwitcher}>
        <Pressable
          style={[styles.tabBtn, activeTab === 'BOOK' && styles.tabBtnActive]}
          onPress={() => setActiveTab('BOOK')}
        >
          <Ionicons
            name="calendar-outline"
            size={16}
            color={activeTab === 'BOOK' ? '#FFFFFF' : colors.textMuted}
            style={{ marginRight: 6 }}
          />
          <Text style={[styles.tabBtnText, activeTab === 'BOOK' && styles.tabBtnTextActive]}>
            Book a Table
          </Text>
        </Pressable>
        <Pressable
          style={[styles.tabBtn, activeTab === 'STATUS' && styles.tabBtnActive]}
          onPress={() => setActiveTab('STATUS')}
        >
          <Ionicons
            name="bookmark-outline"
            size={16}
            color={activeTab === 'STATUS' ? '#FFFFFF' : colors.textMuted}
            style={{ marginRight: 6 }}
          />
          <Text style={[styles.tabBtnText, activeTab === 'STATUS' && styles.tabBtnTextActive]}>
            My Status
          </Text>
        </Pressable>
      </View>

      {activeTab === 'BOOK' ? (
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          {/* Banner */}
          <View style={styles.heroCard}>
            <Ionicons name="restaurant" size={32} color={colors.accent} style={{ marginBottom: 6 }} />
            <Text style={styles.heroTitle}>Reserve Your Cozy Table</Text>
            <Text style={styles.heroSubtitle}>
              Experience gourmet food & comfortable cafe dining at Sip & Bite
            </Text>
          </View>

          {/* Form */}
          <View style={styles.formSection}>
            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Date of Visit *</Text>
              <TextInput
                style={[styles.input, errors.date && styles.inputError]}
                value={date}
                onChangeText={(text) => {
                  setDate(text);
                  if (errors.date) setErrors((prev) => ({ ...prev, date: null }));
                }}
              />
              {errors.date && <Text style={styles.errorText}>{errors.date}</Text>}
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Preferred Time *</Text>
              <TextInput
                style={[styles.input, errors.time && styles.inputError]}
                value={time}
                onChangeText={(text) => {
                  setTime(text);
                  if (errors.time) setErrors((prev) => ({ ...prev, time: null }));
                }}
              />
              {errors.time && <Text style={styles.errorText}>{errors.time}</Text>}
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Number of Guests *</Text>
              <View style={styles.guestSelector}>
                {['1', '2', '4', '6', '8+'].map((num) => (
                  <Pressable
                    key={num}
                    style={[styles.guestChip, guests === num && styles.guestChipActive]}
                    onPress={() => {
                      setGuests(num);
                      if (errors.guests) setErrors((prev) => ({ ...prev, guests: null }));
                    }}
                  >
                    <Text style={[styles.guestChipText, guests === num && styles.guestChipTextActive]}>
                      {num} {num === '1' ? 'Guest' : 'Guests'}
                    </Text>
                  </Pressable>
                ))}
              </View>
              {errors.guests && <Text style={styles.errorText}>{errors.guests}</Text>}
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Special Requests (Optional)</Text>
              <TextInput
                style={[styles.input, styles.textArea]}
                value={specialRequest}
                onChangeText={setSpecialRequest}
                multiline
                numberOfLines={3}
              />
            </View>

            <Pressable
              style={[styles.submitButton, loading && styles.buttonDisabled]}
              onPress={handleSubmitBooking}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color="#FFF" />
              ) : (
                <Text style={styles.submitButtonText}>Confirm Reservation Request</Text>
              )}
            </Pressable>
          </View>
        </ScrollView>
      ) : (
        /* Status Tab */
        <View style={{ flex: 1 }}>
          {fetchingHistory ? (
            <View style={styles.loadingState}>
              <ActivityIndicator size="large" color={colors.primary} />
              <Text style={{ color: colors.textMuted, marginTop: 8, fontWeight: '700' }}>
                Fetching your table status...
              </Text>
            </View>
          ) : userBookings.length === 0 ? (
            <View style={styles.emptyState}>
              <Ionicons name="calendar-outline" size={54} color={colors.textMuted} style={{ marginBottom: 12 }} />
              <Text style={styles.emptyTitle}>No Table Booked Yet</Text>
              <Text style={styles.emptySubtitle}>
                Book a table now and check live café confirmation status here!
              </Text>
              <Pressable style={styles.bookNowBtn} onPress={() => setActiveTab('BOOK')}>
                <Text style={styles.bookNowText}>Reserve a Table</Text>
              </Pressable>
            </View>
          ) : (
            <FlatList
              data={userBookings}
              keyExtractor={(item) => item.id.toString()}
              contentContainerStyle={styles.historyList}
              renderItem={({ item }) => (
                <View style={styles.historyCard}>
                  <View style={styles.cardHeader}>
                    <Text style={styles.cardTableTitle}>
                      {item.table ? `Table #${item.table.table_number}` : `Reservation #${item.id}`}
                    </Text>
                    <Text style={styles.cardGuests}>👥 {item.guests_count} Guests</Text>
                  </View>

                  {renderStatusBadge(item.status)}

                  <View style={styles.cardDetailsRow}>
                    <View>
                      <Text style={styles.cardDetailLabel}>DATE</Text>
                      <Text style={styles.cardDetailVal}>{item.booking_date}</Text>
                    </View>
                    <View>
                      <Text style={styles.cardDetailLabel}>TIME</Text>
                      <Text style={styles.cardDetailVal}>{item.booking_time}</Text>
                    </View>
                  </View>

                  {(item.status || 'pending').toLowerCase() === 'pending' && (
                    <Pressable
                      style={styles.cancelBtn}
                      onPress={() => handleCancelBooking(item.id)}
                    >
                      <Text style={styles.cancelBtnText}>Cancel Booking</Text>
                    </Pressable>
                  )}
                </View>
              )}
            />
          )}
        </View>
      )}
    </View>
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

  tabSwitcher: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    paddingVertical: 10,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    gap: 10,
  },
  tabBtn: {
    flex: 1,
    paddingVertical: 10,
    borderRadius: 14,
    backgroundColor: colors.surfaceAlt,
    alignItems: 'center',
    justifyContent: 'center',
    flexDirection: 'row',
    borderWidth: 1,
    borderColor: colors.border,
  },
  tabBtnActive: { backgroundColor: colors.primary, borderColor: colors.primary },
  tabBtnText: { fontSize: 13, fontWeight: '700', color: colors.textMuted },
  tabBtnTextActive: { color: '#FFFFFF', fontWeight: '800' },

  scrollContent: { padding: 16, paddingBottom: 40 },
  heroCard: {
    backgroundColor: '#1E140F',
    borderRadius: 20,
    padding: 20,
    alignItems: 'center',
    marginBottom: 20,
    elevation: 3,
  },
  heroTitle: {
    fontSize: 19,
    fontWeight: '900',
    color: '#FFFFFF',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  heroSubtitle: { fontSize: 12, color: '#D4C4B5', marginTop: 4, textAlign: 'center', fontWeight: '500' },

  formSection: { gap: 16 },
  inputGroup: {},
  inputLabel: { fontSize: 13, fontWeight: '800', color: colors.textDark, marginBottom: 8 },
  input: {
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 14,
    paddingHorizontal: 16,
    height: 48,
    fontSize: 14,
    color: colors.textDark,
    fontWeight: '600',
  },
  inputError: {
    borderColor: colors.error,
    borderWidth: 1.5,
    backgroundColor: '#FFFBFB',
  },
  errorText: {
    color: colors.error,
    fontSize: 12,
    fontWeight: '700',
    marginTop: 4,
    marginLeft: 4,
  },
  textArea: { height: 90, textAlignVertical: 'top', paddingTop: 12 },
  guestSelector: { flexDirection: 'row', gap: 8, flexWrap: 'wrap' },
  guestChip: {
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 12,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
  },
  guestChipActive: { backgroundColor: colors.primary, borderColor: colors.primary },
  guestChipText: { fontSize: 12.5, fontWeight: '700', color: colors.textMuted },
  guestChipTextActive: { color: '#FFFFFF', fontWeight: '800' },

  submitButton: {
    backgroundColor: colors.primary,
    borderRadius: 16,
    paddingVertical: 15,
    alignItems: 'center',
    marginTop: 10,
    elevation: 3,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 6,
  },
  buttonDisabled: { opacity: 0.6 },
  submitButtonText: { color: '#FFFFFF', fontSize: 15, fontWeight: '800' },

  loadingState: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  emptyState: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 24 },
  emptyTitle: { fontSize: 18, fontWeight: '900', color: colors.textDark },
  emptySubtitle: { fontSize: 13, color: colors.textMuted, marginTop: 4, textAlign: 'center' },
  bookNowBtn: { marginTop: 16, backgroundColor: colors.primary, borderRadius: 14, paddingHorizontal: 20, paddingVertical: 12 },
  bookNowText: { color: '#FFF', fontWeight: '800', fontSize: 13 },

  historyList: { padding: 16 },
  historyCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 18,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: colors.border,
    elevation: 2,
  },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 10 },
  cardTableTitle: { fontSize: 15, fontWeight: '900', color: colors.textDark },
  cardGuests: { fontSize: 13, fontWeight: '800', color: colors.primary },
  statusBadge: { borderRadius: 10, paddingHorizontal: 10, paddingVertical: 6, borderWidth: 1, marginBottom: 10 },
  statusBadgeText: { fontSize: 11, fontWeight: '800' },
  cardDetailsRow: { flexDirection: 'row', justifyContent: 'space-between', backgroundColor: colors.surfaceAlt, padding: 10, borderRadius: 10 },
  cardDetailLabel: { fontSize: 9, fontWeight: '800', color: colors.textMuted },
  cardDetailVal: { fontSize: 13, fontWeight: '800', color: colors.textDark, marginTop: 2 },
  cancelBtn: { marginTop: 10, backgroundColor: colors.errorBg, borderRadius: 10, paddingVertical: 8, alignItems: 'center' },
  cancelBtnText: { color: colors.error, fontSize: 12, fontWeight: '800' },
});
