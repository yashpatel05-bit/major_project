import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  Pressable,
  ActivityIndicator,
  RefreshControl,
  StyleSheet,
  Alert,
  ToastAndroid,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { getUserBookings, cancelBooking } from '../api/ApiClient';
import { colors } from '../theme/colors';

export default function TableBookingHistoryScreen({ navigation }) {
  const { user } = useAuth();
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [lastSynced, setLastSynced] = useState(new Date().toLocaleTimeString());

  useEffect(() => {
    fetchBookings();
  }, [user]);

  const fetchBookings = async (isManualRefresh = false) => {
    if (!user) return;
    if (isManualRefresh) setRefreshing(true);
    else setLoading(true);

    try {
      const res = await getUserBookings(user.id);
      if (res.data?.success) {
        setBookings(res.data.data || []);
        setLastSynced(new Date().toLocaleTimeString());
        if (isManualRefresh && Platform.OS === 'android') {
          ToastAndroid.show('Table booking status updated!', ToastAndroid.SHORT);
        }
      }
    } catch (_) {
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = useCallback(() => {
    fetchBookings(true);
  }, [user]);

  const handleCancelBooking = (bookingId) => {
    Alert.alert(
      'Cancel Table Reservation',
      'Are you sure you want to cancel this table booking?',
      [
        { text: 'No, Keep It', style: 'cancel' },
        {
          text: 'Yes, Cancel',
          style: 'destructive',
          onPress: async () => {
            try {
              const res = await cancelBooking(bookingId);
              if (res.data?.success) {
                Alert.alert('Reservation Cancelled', 'Your table reservation has been cancelled.');
                fetchBookings(true);
              }
            } catch (err) {
              Alert.alert('Error', err.message || 'Could not cancel booking.');
            }
          },
        },
      ]
    );
  };

  const getStatusBadge = (status) => {
    const s = (status || 'pending').toLowerCase();
    if (s === 'confirmed' || s === 'approved') {
      return {
        label: 'CONFIRMED BY CAFE',
        bgColor: '#DCFCE7',
        textColor: '#15803D',
        borderColor: '#86EFAC',
        desc: 'Your table is officially reserved for you!',
      };
    }
    if (s === 'cancelled' || s === 'rejected') {
      return {
        label: 'CANCELLED',
        bgColor: '#FEE2E2',
        textColor: '#B91C1C',
        borderColor: '#FCA5A5',
        desc: 'This table reservation was cancelled.',
      };
    }
    return {
      label: 'PENDING APPROVAL',
      bgColor: '#FEF3C7',
      textColor: '#B45309',
      borderColor: '#FDE68A',
      desc: 'Cafe staff is reviewing your reservation request.',
    };
  };

  return (
    <View style={styles.container}>
      {/* Header Bar */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <View style={{ alignItems: 'center' }}>
          <Text style={styles.headerTitle}>My Table Status</Text>
          <Text style={styles.syncText}>Last synced {lastSynced}</Text>
        </View>
        <Pressable onPress={() => fetchBookings(true)} style={styles.refreshBtn}>
          <Ionicons name="refresh" size={20} color={colors.primary} />
        </Pressable>
      </View>

      {loading && !refreshing ? (
        <View style={styles.loadingState}>
          <ActivityIndicator size="large" color={colors.primary} />
          <Text style={styles.loadingText}>Fetching reservation status...</Text>
        </View>
      ) : bookings.length === 0 ? (
        <View style={styles.emptyState}>
          <Ionicons name="calendar-outline" size={54} color={colors.textMuted} style={{ marginBottom: 12 }} />
          <Text style={styles.emptyTitle}>No Table Reservations Yet</Text>
          <Text style={styles.emptySubtitle}>Book a table at Sip & Bite and track approval live here!</Text>
          <Pressable
            style={styles.bookNowBtn}
            onPress={() => navigation.navigate('TableBooking')}
          >
            <Text style={styles.bookNowText}>Reserve a Table Now</Text>
          </Pressable>
        </View>
      ) : (
        <FlatList
          data={bookings}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={onRefresh}
              colors={[colors.primary]}
              tintColor={colors.primary}
            />
          }
          renderItem={({ item }) => {
            const badge = getStatusBadge(item.status);
            return (
              <View style={styles.card}>
                <View style={styles.cardHeader}>
                  <View style={styles.tableBadge}>
                    <Text style={styles.tableBadgeText}>
                      🪑 {item.table ? `Table #${item.table.table_number}` : `Reservation #${item.id}`}
                    </Text>
                  </View>
                  <Text style={styles.guestsBadge}>👥 {item.guests_count} Guests</Text>
                </View>

                {/* Status Box */}
                <View style={[styles.statusBox, { backgroundColor: badge.bgColor, borderColor: badge.borderColor }]}>
                  <Text style={[styles.statusTitle, { color: badge.textColor }]}>{badge.label}</Text>
                  <Text style={[styles.statusDesc, { color: badge.textColor }]}>{badge.desc}</Text>
                </View>

                {/* Details */}
                <View style={styles.detailsRow}>
                  <View style={styles.detailItem}>
                    <Text style={styles.detailLabel}>DATE</Text>
                    <Text style={styles.detailValue}>{item.booking_date}</Text>
                  </View>
                  <View style={styles.detailItem}>
                    <Text style={styles.detailLabel}>TIME</Text>
                    <Text style={styles.detailValue}>{item.booking_time}</Text>
                  </View>
                </View>

                {item.special_request ? (
                  <View style={styles.notesBox}>
                    <Text style={styles.notesLabel}>Note: {item.special_request}</Text>
                  </View>
                ) : null}

                {/* Action button if pending */}
                {(item.status || 'pending').toLowerCase() === 'pending' && (
                  <Pressable
                    style={styles.cancelBtn}
                    onPress={() => handleCancelBooking(item.id)}
                  >
                    <Text style={styles.cancelBtnText}>Cancel Reservation</Text>
                  </Pressable>
                )}
              </View>
            );
          }}
        />
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
  syncText: { fontSize: 10, color: colors.textMuted, fontWeight: '700', marginTop: 1 },
  refreshBtn: { padding: 4 },
  loadingState: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  loadingText: { color: colors.textMuted, fontSize: 13, marginTop: 10, fontWeight: '700' },
  emptyState: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 24 },
  emptyTitle: { fontSize: 18, fontWeight: '900', color: colors.textDark },
  emptySubtitle: { fontSize: 13, color: colors.textMuted, marginTop: 6, marginBottom: 20, textAlign: 'center' },
  bookNowBtn: {
    backgroundColor: colors.primary,
    paddingHorizontal: 22,
    paddingVertical: 12,
    borderRadius: 16,
    elevation: 3,
  },
  bookNowText: { color: '#FFFFFF', fontSize: 13, fontWeight: '800' },
  listContent: { padding: 16, paddingBottom: 40 },
  card: {
    backgroundColor: '#FFFFFF',
    borderRadius: 18,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: colors.border,
    elevation: 2,
  },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 },
  tableBadge: { backgroundColor: colors.surfaceAlt, borderRadius: 12, paddingHorizontal: 12, paddingVertical: 6 },
  tableBadgeText: { color: colors.primaryDark, fontWeight: '900', fontSize: 13.5 },
  guestsBadge: { fontSize: 13, fontWeight: '800', color: colors.textDark },
  statusBox: {
    borderRadius: 12,
    padding: 12,
    borderWidth: 1,
    marginBottom: 12,
  },
  statusTitle: { fontSize: 12.5, fontWeight: '900' },
  statusDesc: { fontSize: 11, fontWeight: '600', marginTop: 2 },
  detailsRow: { flexDirection: 'row', justifyContent: 'space-between', backgroundColor: colors.surfaceAlt, padding: 12, borderRadius: 12 },
  detailItem: { flex: 1 },
  detailLabel: { fontSize: 9.5, fontWeight: '800', color: colors.textMuted },
  detailValue: { fontSize: 13.5, fontWeight: '800', color: colors.textDark, marginTop: 2 },
  notesBox: { marginTop: 10, backgroundColor: colors.surfaceAlt, borderRadius: 10, padding: 10 },
  notesLabel: { fontSize: 12, color: colors.textMuted, fontWeight: '600' },
  cancelBtn: { marginTop: 12, backgroundColor: colors.errorBg, borderRadius: 10, paddingVertical: 8, alignItems: 'center' },
  cancelBtnText: { color: colors.error, fontSize: 12, fontWeight: '800' },
});
