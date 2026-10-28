import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';

export default function CartItemRow({
  cartItem,
  item,
  onIncrease,
  onDecrease,
  onIncrement,
  onDecrement,
  onDelete,
}) {
  const target = cartItem || item;
  if (!target || !target.menuItem) return null;

  const { menuItem, quantity } = target;
  const handleIncrease = onIncrease || onIncrement || (() => {});
  const handleDecrease = onDecrease || onDecrement || (() => {});

  return (
    <View style={styles.row}>
      <View style={styles.info}>
        <Text style={styles.name} numberOfLines={1}>{menuItem.name}</Text>
        <Text style={styles.price}>₹{Math.round(menuItem.price)} each</Text>
      </View>

      <View style={styles.controls}>
        <Pressable
          style={styles.controlButton}
          onPress={() => handleDecrease(target)}
          accessibilityLabel="Decrease quantity"
        >
          <Ionicons name="remove" size={14} color={colors.primary} />
        </Pressable>

        <Text style={styles.quantity}>{quantity}</Text>

        <Pressable
          style={styles.controlButton}
          onPress={() => handleIncrease(target)}
          accessibilityLabel="Increase quantity"
        >
          <Ionicons name="add" size={14} color={colors.primary} />
        </Pressable>
      </View>

      <Text style={styles.subtotal}>₹{Math.round(menuItem.price * quantity)}</Text>

      <Pressable
        style={styles.deleteButton}
        onPress={() => onDelete && onDelete(target)}
        accessibilityLabel={`Remove ${menuItem.name}`}
      >
        <Ionicons name="trash-outline" size={16} color={colors.error} />
      </Pressable>
    </View>
  );
}

const styles = StyleSheet.create({
  row: {
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: colors.border,
    elevation: 2,
    flexDirection: 'row',
    marginBottom: 10,
    paddingHorizontal: 14,
    paddingVertical: 14,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
  },
  info: {
    flex: 1,
  },
  name: {
    color: colors.textDark,
    fontSize: 14,
    fontWeight: '800',
  },
  price: {
    color: colors.textMuted,
    fontSize: 11.5,
    marginTop: 2,
    fontWeight: '600',
  },
  controls: {
    alignItems: 'center',
    flexDirection: 'row',
    marginHorizontal: 8,
  },
  controlButton: {
    alignItems: 'center',
    backgroundColor: colors.surfaceAlt,
    borderRadius: 8,
    height: 30,
    justifyContent: 'center',
    width: 30,
  },
  quantity: {
    color: colors.textDark,
    fontSize: 14,
    fontWeight: '800',
    marginHorizontal: 10,
    minWidth: 18,
    textAlign: 'center',
  },
  subtotal: {
    color: colors.primaryDark,
    fontSize: 14,
    fontWeight: '900',
    marginHorizontal: 8,
    minWidth: 50,
    textAlign: 'right',
  },
  deleteButton: {
    alignItems: 'center',
    height: 30,
    justifyContent: 'center',
    width: 30,
    marginLeft: 4,
  },
});
