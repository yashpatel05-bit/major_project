import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  Pressable,
  ActivityIndicator,
  ScrollView,
  RefreshControl,
  Linking,
  StyleSheet,
  ToastAndroid,
  Alert,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { getOrderDetails, cancelOrder } from '../api/ApiClient';
import RazorpayPaymentModal from '../components/RazorpayPaymentModal';
import { colors } from '../theme/colors';

export default function OrderTrackingScreen({ route, navigation }) {
  const { orderId } = route.params;
  const { user } = useAuth();
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [cancellingOrder, setCancellingOrder] = useState(false);
  const [showCancellationRazorpay, setShowCancellationRazorpay] = useState(false);
  const [lastSynced, setLastSynced] = useState(new Date().toLocaleTimeString());

  useEffect(() => {
    fetchOrder();
  }, [orderId]);

  const fetchOrder = async (isManualRefresh = false) => {
    if (isManualRefresh) setRefreshing(true);
    else setLoading(true);

    try {
      const res = await getOrderDetails(orderId);
      if (res.data?.success) {
        setOrder(res.data.data);
        setLastSynced(new Date().toLocaleTimeString());
        if (isManualRefresh && Platform.OS === 'android') {
          ToastAndroid.show('Live order status updated!', ToastAndroid.SHORT);
        }
      }
    } catch (_) {}
    setLoading(false);
    setRefreshing(false);
  };

  const onRefresh = useCallback(() => {
    fetchOrder(true);
  }, [orderId]);

  const callDriver = (phone) => {
    Linking.openURL(`tel:${phone}`);
  };

  // Two-step Alert cancellation flow
  const handleCancelOrderClick = () => {
    // 1st Alert
    Alert.alert(
      '⚠️ Cancel Order',
      'order cancellation is a wastage of food.',
      [
        {
          text: '1. no cancel',
          style: 'cancel',
        },
        {
          text: '2. cancel anyway',
          style: 'destructive',
          onPress: () => {
            // 2nd Alert
            setTimeout(() => {
              Alert.alert(
                '💳 Cancellation Fee Required',
                'cancellation fees you have to pay.\n\nfees :- 50 rs. online payment mode only.',
                [
                  {
                    text: 'Cancel',
                    style: 'cancel',
                  },
                  {
                    text: 'Pay ₹50 Online',
                    onPress: () => {
                      setShowCancellationRazorpay(true);
                    },
                  },
                ]
              );
            }, 150);
          },
        },
      ]
    );
  };

  const handleCancellationPaymentSuccess = async (paymentId) => {
    setShowCancellationRazorpay(false);
    setCancellingOrder(true);
    try {
      const userId = user?.id || order?.user_id;
      const res = await cancelOrder(order.id, {
        user_id: userId,
        razorpay_payment_id: paymentId,
        cancellation_fee: 50,
      });

      if (res.data?.success) {
        setOrder(res.data.data);
        Alert.alert(
          'Order Cancelled',
          'Your order has been cancelled successfully. Cancellation fee of ₹50 was paid online.',
        );
        fetchOrder(true);
      } else {
        Alert.alert('Cancellation Error', res.data?.message || 'Could not cancel order.');
      }
    } catch (err) {
      const msg = err.response?.data?.message || err.message || 'Failed to cancel order.';
      Alert.alert('Error', msg);
    } finally {
      setCancellingOrder(false);
    }
  };

  if (loading && !order) {
    return (
      <View style={styles.loadingState}>
        <ActivityIndicator size="large" color={colors.primary} />
        <Text style={{ marginTop: 12, color: colors.textMuted, fontWeight: '600' }}>Fetching live order status...</Text>
      </View>
    );
  }

  if (!order) {
    return (
      <View style={styles.loadingState}>
        <Text style={styles.errorText}>Order not found</Text>
      </View>
    );
  }

  const isCancelled = (order.order_status || '').toLowerCase() === 'cancelled';
  const isDelivered = (order.order_status || '').toLowerCase() === 'delivered';
  const canCancel = !isCancelled && !isDelivered;

  const statusSteps = ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered'];
  const currentIndex = statusSteps.findIndex(
    (s) => s.toLowerCase() === (order.order_status || '').toLowerCase(),
  );

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <View style={{ alignItems: 'center' }}>
          <Text style={styles.headerTitle}>Order Tracking</Text>
          <Text style={{ fontSize: 10, color: colors.textMuted }}>Synced at {lastSynced}</Text>
        </View>
        <Pressable onPress={() => fetchOrder(true)} style={{ padding: 6 }}>
          <Ionicons name="refresh" size={20} color={colors.primary} />
        </Pressable>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            colors={[colors.primary]}
            tintColor={colors.primary}
          />
        }
      >
        {/* Order Info Card */}
        <View style={styles.infoCard}>
          <Text style={styles.orderNumber}>#{order.order_number}</Text>
          <View style={[styles.statusBadge, { backgroundColor: getStatusColor(order.order_status) + '22' }]}>
            <Text style={[styles.statusText, { color: getStatusColor(order.order_status) }]}>
              {(order.order_status || 'Pending').toUpperCase()}
            </Text>
          </View>
        </View>

        {/* Cancelled Banner if Order is Cancelled */}
        {isCancelled && (
          <View style={styles.cancelledBanner}>
            <Ionicons name="ban" size={24} color="#DC2626" style={{ marginRight: 12 }} />
            <View style={{ flex: 1 }}>
              <Text style={styles.cancelledTitle}>This order has been cancelled.</Text>
              {Number(order.cancellation_fee || 0) > 0 && (
                <Text style={styles.cancelledSubtitle}>
                  Cancellation fee: ₹{Math.round(order.cancellation_fee)} paid online
                  {order.cancellation_payment_id ? `\nRazorpay: ${order.cancellation_payment_id}` : ''}
                </Text>
              )}
            </View>
          </View>
        )}

        {/* Progress Steps (only if not cancelled) */}
        {!isCancelled && (
          <View style={styles.progressCard}>
            <View style={styles.progressCardHeader}>
              <Text style={styles.cardTitle}>Order Progress</Text>
              {canCancel && (
                <Pressable
                  style={styles.cancelTopButton}
                  onPress={handleCancelOrderClick}
                  disabled={cancellingOrder}
                >
                  <Ionicons name="close-circle-outline" size={14} color="#DC2626" style={{ marginRight: 4 }} />
                  <Text style={styles.cancelTopText}>Cancel Order</Text>
                </Pressable>
              )}
            </View>

            {statusSteps.map((step, index) => {
              const isCompleted = index <= currentIndex;
              const isCurrent = index === currentIndex;
              return (
                <View key={step} style={styles.stepRow}>
                  <View style={styles.stepIndicator}>
                    <View
                      style={[
                        styles.stepDot,
                        isCompleted && styles.stepDotCompleted,
                        isCurrent && styles.stepDotCurrent,
                      ]}
                    >
                      {isCompleted && <Text style={styles.stepCheck}>✓</Text>}
                    </View>
                    {index < statusSteps.length - 1 && (
                      <View
                        style={[styles.stepLine, isCompleted && styles.stepLineCompleted]}
                      />
                    )}
                  </View>
                  <Text
                    style={[
                      styles.stepLabel,
                      isCompleted && styles.stepLabelCompleted,
                      isCurrent && styles.stepLabelCurrent,
                    ]}
                  >
                    {step}
                  </Text>
                </View>
              );
            })}
          </View>
        )}

        {/* Items */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Order Items</Text>
          {order.items?.map((item, index) => (
            <View key={index} style={styles.orderItemRow}>
              <Text style={styles.orderItemName}>
                {item.quantity}x {item.item_name}
              </Text>
              <Text style={styles.orderItemPrice}>₹{Math.round(item.subtotal)}</Text>
            </View>
          ))}
          <View style={styles.divider} />

          {Number(order.cancellation_fee || 0) > 0 && (
            <View style={styles.orderItemRow}>
              <Text style={[styles.orderItemName, { color: '#DC2626', fontWeight: '700' }]}>
                Cancellation Fee (Paid Online)
              </Text>
              <Text style={[styles.orderItemPrice, { color: '#DC2626' }]}>
                +₹{Math.round(order.cancellation_fee)}
              </Text>
            </View>
          )}

          <View style={styles.orderItemRow}>
            <Text style={styles.totalLabel}>
              {isCancelled ? 'Original Order Total' : 'Total Amount'}
            </Text>
            <Text style={styles.totalValue}>₹{Math.round(order.total_amount)}</Text>
          </View>
          <Text style={styles.paymentInfo}>
            {order.payment_method} • {order.payment_status}
          </Text>
        </View>

        {/* Delivery Person (if not cancelled) */}
        {!isCancelled && (
          <View style={styles.card}>
            <Text style={styles.cardTitle}>Delivery Partner</Text>
            {order.delivery_person ? (
              <View style={styles.driverRow}>
                <View style={styles.driverAvatar}>
                  <Ionicons name="bicycle" size={24} color={colors.primary} />
                </View>
                <View style={styles.driverInfo}>
                  <Text style={styles.driverName}>{order.delivery_person.name}</Text>
                  <Text style={styles.driverPhone}>{order.delivery_person.phone}</Text>
                </View>
                <Pressable
                  style={styles.callButton}
                  onPress={() => callDriver(order.delivery_person.phone)}
                >
                  <Ionicons name="call" size={14} color="#FFFFFF" style={{ marginRight: 4 }} />
                  <Text style={styles.callButtonText}>Call</Text>
                </Pressable>
              </View>
            ) : (
              <View style={styles.noDriveRow}>
                <Ionicons name="hourglass-outline" size={22} color={colors.textMuted} style={{ marginRight: 10 }} />
                <Text style={styles.noDriverText}>Assigning nearby delivery partner...</Text>
              </View>
            )}
          </View>
        )}

        {/* Prominent Cancel Order Button if active */}
        {canCancel && (
          <Pressable
            style={[styles.cancelMainButton, cancellingOrder && { opacity: 0.7 }]}
            onPress={handleCancelOrderClick}
            disabled={cancellingOrder}
          >
            {cancellingOrder ? (
              <ActivityIndicator size="small" color="#DC2626" />
            ) : (
              <>
                <Ionicons name="close-circle-outline" size={18} color="#DC2626" style={{ marginRight: 6 }} />
                <Text style={styles.cancelMainText}>Cancel Order</Text>
              </>
            )}
          </Pressable>
        )}

        {/* Action Buttons */}
        <View style={styles.actionsRow}>
          {isDelivered && (
            <Pressable
              style={styles.feedbackButton}
              onPress={() => navigation.navigate('Feedback', { orderId: order.id })}
            >
              <Text style={styles.feedbackButtonText}>Give Feedback</Text>
            </Pressable>
          )}
          <Pressable
            style={styles.homeButton}
            onPress={() => navigation.popToTop()}
          >
            <Text style={styles.homeButtonText}>Home</Text>
          </Pressable>
        </View>
      </ScrollView>

      {/* Razorpay Online Payment for Cancellation Fee (₹50) */}
      <RazorpayPaymentModal
        visible={showCancellationRazorpay}
        amount={50}
        description={`Cancellation Fee for Order #${order.order_number}`}
        userInfo={{
          name: user?.name,
          email: user?.email,
          phone: user?.phone,
        }}
        orderId={order.id}
        onSuccess={handleCancellationPaymentSuccess}
        onCancel={() => {
          setShowCancellationRazorpay(false);
        }}
        onClose={() => setShowCancellationRazorpay(false)}
      />
    </View>
  );
}

function getStatusColor(status) {
  switch ((status || '').toLowerCase()) {
    case 'pending': return '#B45309';
    case 'confirmed':
    case 'preparing': return '#1D4ED8';
    case 'out for delivery': return '#C2410C';
    case 'delivered': return '#15803D';
    case 'cancelled': return '#B91C1C';
    default: return '#6B7280';
  }
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

  loadingState: { alignItems: 'center', flex: 1, justifyContent: 'center', backgroundColor: colors.background },
  errorText: { color: colors.textMuted, fontSize: 14 },

  scrollContent: { paddingHorizontal: 16, paddingTop: 16, paddingBottom: 30 },

  // Info card
  infoCard: {
    alignItems: 'center',
    backgroundColor: '#1E140F',
    borderRadius: 18,
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 14,
    paddingHorizontal: 20,
    paddingVertical: 18,
  },
  orderNumber: { color: '#FFFFFF', fontSize: 17, fontWeight: '900' },
  statusBadge: { borderRadius: 8, paddingHorizontal: 12, paddingVertical: 5 },
  statusText: { fontSize: 10.5, fontWeight: '800', letterSpacing: 0.6 },

  // Cancelled Banner
  cancelledBanner: {
    backgroundColor: '#FEE2E2',
    borderWidth: 1,
    borderColor: '#FCA5A5',
    borderRadius: 16,
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 14,
  },
  cancelledTitle: {
    fontSize: 14,
    fontWeight: '800',
    color: '#DC2626',
  },
  cancelledSubtitle: {
    fontSize: 12,
    color: '#991B1B',
    marginTop: 2,
    fontWeight: '600',
  },

  // Cards
  card: { backgroundColor: '#FFFFFF', borderRadius: 16, elevation: 1, marginBottom: 14, padding: 18, borderWidth: 1, borderColor: colors.border },
  progressCard: { backgroundColor: '#FFFFFF', borderRadius: 16, elevation: 1, marginBottom: 14, padding: 18, borderWidth: 1, borderColor: colors.border },
  progressCardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 14 },
  cardTitle: { color: colors.textDark, fontSize: 14.5, fontWeight: '800', marginBottom: 0 },
  cancelTopButton: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12, backgroundColor: '#FEE2E2' },
  cancelTopText: { color: '#DC2626', fontSize: 11, fontWeight: '800' },

  // Progress steps
  stepRow: { flexDirection: 'row', alignItems: 'flex-start' },
  stepIndicator: { alignItems: 'center', width: 28 },
  stepDot: { alignItems: 'center', backgroundColor: colors.surfaceAlt, borderRadius: 12, height: 24, justifyContent: 'center', width: 24 },
  stepDotCompleted: { backgroundColor: colors.success },
  stepDotCurrent: { backgroundColor: colors.primary },
  stepCheck: { color: '#FFF', fontSize: 12, fontWeight: '800' },
  stepLine: { backgroundColor: colors.border, height: 20, width: 2 },
  stepLineCompleted: { backgroundColor: colors.success },
  stepLabel: { color: colors.textMuted, flex: 1, fontSize: 13, marginLeft: 12, paddingTop: 3, paddingBottom: 12 },
  stepLabelCompleted: { color: colors.textDark, fontWeight: '600' },
  stepLabelCurrent: { color: colors.primaryDark, fontWeight: '800' },

  // Order items
  orderItemRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 6 },
  orderItemName: { color: colors.textDark, flex: 1, fontSize: 13 },
  orderItemPrice: { color: colors.textDark, fontSize: 13, fontWeight: '700' },
  divider: { backgroundColor: colors.border, height: 1, marginVertical: 10 },
  totalLabel: { color: colors.textDark, fontSize: 15, fontWeight: '800' },
  totalValue: { color: colors.primaryDark, fontSize: 17, fontWeight: '900' },
  paymentInfo: { color: colors.textMuted, fontSize: 11, marginTop: 6 },

  // Driver
  driverRow: { alignItems: 'center', flexDirection: 'row' },
  driverAvatar: { alignItems: 'center', backgroundColor: colors.surfaceAlt, borderRadius: 22, height: 44, justifyContent: 'center', width: 44 },
  driverInfo: { flex: 1, marginLeft: 12 },
  driverName: { color: colors.textDark, fontSize: 14, fontWeight: '700' },
  driverPhone: { color: colors.textMuted, fontSize: 12, marginTop: 2 },
  callButton: { backgroundColor: colors.primary, borderRadius: 10, paddingHorizontal: 14, paddingVertical: 8, flexDirection: 'row', alignItems: 'center' },
  callButtonText: { color: '#FFF', fontSize: 12, fontWeight: '700' },
  noDriveRow: { alignItems: 'center', flexDirection: 'row' },
  noDriverText: { color: colors.textMuted, fontSize: 13 },

  // Cancel Main Button
  cancelMainButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FFFFFF',
    borderWidth: 1.5,
    borderColor: '#DC2626',
    borderRadius: 14,
    height: 48,
    marginBottom: 14,
  },
  cancelMainText: {
    color: '#DC2626',
    fontSize: 13.5,
    fontWeight: '800',
  },

  // Actions
  actionsRow: { flexDirection: 'row', gap: 10, marginTop: 4 },
  feedbackButton: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 14,
    flex: 1,
    height: 48,
    justifyContent: 'center',
  },
  feedbackButtonText: { color: '#FFFFFF', fontSize: 13, fontWeight: '800' },
  homeButton: {
    alignItems: 'center',
    backgroundColor: colors.surfaceAlt,
    borderRadius: 14,
    flex: 1,
    height: 48,
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: colors.border,
  },
  homeButtonText: { color: colors.textDark, fontSize: 13, fontWeight: '800' },
});
