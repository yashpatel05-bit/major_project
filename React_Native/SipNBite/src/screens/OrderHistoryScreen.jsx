import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  Pressable,
  ActivityIndicator,
  RefreshControl,
  StyleSheet,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { getUserOrders } from '../api/ApiClient';
import OrderCard from '../components/OrderCard';
import { colors } from '../theme/colors';

export default function OrderHistoryScreen({ navigation }) {
  const { user } = useAuth();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [activeFilter, setActiveFilter] = useState('All');

  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async (isManualRefresh = false) => {
    if (!user) return;
    if (isManualRefresh) setRefreshing(true);
    else setLoading(true);

    try {
      const res = await getUserOrders(user.id);
      if (res.data?.success) {
        setOrders(res.data.data || []);
      }
    } catch (_) {
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = useCallback(() => {
    fetchOrders(true);
  }, [user]);

  const filteredOrders = orders.filter((o) => {
    const status = (o.order_status || '').toLowerCase();
    if (activeFilter === 'Active') {
      return status === 'pending' || status === 'preparing' || status === 'out for delivery' || status === 'confirmed';
    }
    if (activeFilter === 'Delivered') return status === 'delivered';
    if (activeFilter === 'Cancelled') return status === 'cancelled';
    return true;
  });

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>My Orders</Text>
        <Pressable onPress={() => fetchOrders(true)} style={{ padding: 4 }}>
          <Ionicons name="refresh" size={20} color={colors.primary} />
        </Pressable>
      </View>

      {/* Filter Chips Bar */}
      <View style={styles.filterRow}>
        {['All', 'Active', 'Delivered', 'Cancelled'].map((f) => (
          <Pressable
            key={f}
            style={[styles.filterChip, activeFilter === f && styles.filterChipActive]}
            onPress={() => setActiveFilter(f)}
          >
            <Text style={[styles.filterChipText, activeFilter === f && styles.filterChipTextActive]}>
              {f}
            </Text>
          </Pressable>
        ))}
      </View>

      {loading && !refreshing ? (
        <View style={styles.loadingState}>
          <ActivityIndicator size="large" color={colors.primary} />
          <Text style={styles.loadingText}>Fetching order history...</Text>
        </View>
      ) : filteredOrders.length === 0 ? (
        <View style={styles.emptyState}>
          <Ionicons name="receipt-outline" size={50} color={colors.textMuted} style={{ marginBottom: 12 }} />
          <Text style={styles.emptyTitle}>No {activeFilter !== 'All' ? activeFilter : ''} Orders Found</Text>
          <Text style={styles.emptySubtitle}>Your order history will show here with real status tracking.</Text>
        </View>
      ) : (
        <FlatList
          data={filteredOrders}
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
          renderItem={({ item }) => (
            <OrderCard
              order={item}
              onPress={() =>
                navigation.navigate('OrderTracking', { orderId: item.id })
              }
            />
          )}
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
  filterRow: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    paddingVertical: 12,
    gap: 8,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  filterChip: {
    paddingHorizontal: 14,
    paddingVertical: 7,
    borderRadius: 20,
    backgroundColor: colors.surfaceAlt,
    borderWidth: 1,
    borderColor: colors.border,
  },
  filterChipActive: { backgroundColor: colors.primary, borderColor: colors.primary },
  filterChipText: { fontSize: 12, fontWeight: '700', color: colors.textMuted },
  filterChipTextActive: { color: '#FFFFFF', fontWeight: '800' },
  loadingState: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  loadingText: { color: colors.textMuted, fontSize: 13, marginTop: 8 },
  emptyState: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 24 },
  emptyTitle: { fontSize: 18, fontWeight: '800', color: colors.textDark },
  emptySubtitle: { fontSize: 13, color: colors.textMuted, marginTop: 4, textAlign: 'center' },
  listContent: { padding: 16 },
});
