import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';
import { colors } from '../theme/colors';

export default function OrderCard({ order, onPress }) {
  const statusColor = getStatusColor(order.order_status);

  return (
    <Pressable style={styles.card} onPress={() => onPress(order)} accessibilityRole="button">
      <View style={styles.topRow}>
        <Text style={styles.orderNumber}>#{order.order_number}</Text>
        <View style={[styles.statusBadge, { backgroundColor: statusColor + '18' }]}>
          <Text style={[styles.statusText, { color: statusColor }]}>
            {(order.order_status || 'Pending').toUpperCase()}
          </Text>
        </View>
      </View>

      {order.items && order.items.length > 0 && (
        <Text style={styles.items} numberOfLines={2}>
          {order.items.map((i) => `${i.quantity}x ${i.item_name}`).join(', ')}
        </Text>
      )}

      <View style={styles.bottomRow}>
        <Text style={styles.total}>₹{Math.round(order.total_amount)}</Text>
        <Text style={styles.payment}>
          {order.payment_method} • {order.payment_status}
        </Text>
      </View>

      {Number(order.cancellation_fee || 0) > 0 && (
        <Text style={styles.cancellationFeeTag}>
          Cancellation Fee: ₹{Math.round(order.cancellation_fee)} Paid (Online)
        </Text>
      )}

      {order.created_at && (
        <Text style={styles.date}>
          {new Date(order.created_at).toLocaleDateString('en-IN', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
          })}
        </Text>
      )}
    </Pressable>
  );
}

function getStatusColor(status) {
  switch ((status || '').toLowerCase()) {
    case 'pending':
      return '#B45309';
    case 'confirmed':
    case 'preparing':
      return '#1D4ED8';
    case 'out for delivery':
      return '#C2410C';
    case 'delivered':
      return '#15803D';
    case 'cancelled':
      return '#B91C1C';
    default:
      return '#6B7280';
  }
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#FFFFFF',
    borderRadius: 18,
    borderWidth: 1,
    borderColor: colors.border,
    elevation: 2,
    marginBottom: 12,
    padding: 16,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 5,
  },
  topRow: {
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  orderNumber: {
    color: colors.textDark,
    fontSize: 15,
    fontWeight: '900',
  },
  statusBadge: {
    borderRadius: 8,
    paddingHorizontal: 10,
    paddingVertical: 4,
  },
  statusText: {
    fontSize: 10.5,
    fontWeight: '800',
    letterSpacing: 0.5,
  },
  items: {
    color: colors.textMuted,
    fontSize: 12,
    fontWeight: '600',
    lineHeight: 17,
    marginTop: 8,
  },
  bottomRow: {
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 10,
  },
  total: {
    color: colors.primaryDark,
    fontSize: 16,
    fontWeight: '900',
  },
  payment: {
    color: colors.textMuted,
    fontSize: 11,
    fontWeight: '600',
  },
  date: {
    color: colors.textPlaceholder,
    fontSize: 10,
    fontWeight: '500',
    marginTop: 6,
  },
  cancellationFeeTag: {
    color: '#DC2626',
    fontSize: 11,
    fontWeight: '700',
    marginTop: 4,
  },
});
