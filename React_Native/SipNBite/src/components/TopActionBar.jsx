import React from 'react';
import { View, Text, Pressable, StyleSheet, Platform, StatusBar as RNStatusBar } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { useCart } from '../context/CartContext';
import { colors } from '../theme/colors';

export default function TopActionBar({
  navigation,
  title = 'Sip & Bite',
  subtitle = '— CAFE & FOOD —',
  onMenuPress,
}) {
  const insets = useSafeAreaInsets();
  const topInset = Math.max(
    insets.top,
    Platform.OS === 'android' ? (RNStatusBar.currentHeight || 28) : 12
  );
  const { getItemCount } = useCart();
  const cartCount = getItemCount();

  const handleMenuPress = () => {
    if (onMenuPress) {
      onMenuPress();
    } else if (navigation) {
      navigation.navigate('MenuTab');
    }
  };

  const handleNotificationsPress = () => {
    if (navigation) {
      navigation.navigate('OrdersTab');
    }
  };

  const handleCartPress = () => {
    if (navigation) {
      navigation.navigate('Cart');
    }
  };

  return (
    <View style={[styles.container, { paddingTop: topInset + 4 }]}>
      {/* Left: Drawer / Menu Icon */}
      <Pressable
        style={styles.iconButton}
        onPress={handleMenuPress}
        accessibilityRole="button"
        accessibilityLabel="Menu drawer"
      >
        <Ionicons name="menu-outline" size={26} color={colors.primaryDark} />
      </Pressable>

      {/* Middle: Cafe Logo & Branding */}
      <View style={styles.centerBranding}>
        <View style={styles.logoRow}>
          <Ionicons name="restaurant" size={17} color={colors.primary} style={{ marginRight: 6 }} />
          <Text style={styles.brandTitle}>{title}</Text>
        </View>
        <Text style={styles.brandSubtitle}>{subtitle}</Text>
      </View>

      {/* Right: Notifications & Cart with Badges */}
      <View style={styles.rightActions}>
        <Pressable
          style={styles.iconButton}
          onPress={handleNotificationsPress}
          accessibilityRole="button"
          accessibilityLabel="Notifications"
        >
          <Ionicons name="notifications-outline" size={24} color={colors.primaryDark} />
          <View style={styles.notifBadge}>
            <Text style={styles.notifBadgeText}>3</Text>
          </View>
        </Pressable>

        <Pressable
          style={styles.iconButton}
          onPress={handleCartPress}
          accessibilityRole="button"
          accessibilityLabel="Shopping cart"
        >
          <Ionicons name="cart-outline" size={25} color={colors.primaryDark} />
          {cartCount > 0 && (
            <View style={styles.cartBadge}>
              <Text style={styles.cartBadgeText}>{cartCount > 99 ? '99+' : cartCount}</Text>
            </View>
          )}
        </Pressable>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? 12 : 8,
    paddingBottom: 10,
    backgroundColor: colors.background,
  },
  iconButton: {
    width: 42,
    height: 42,
    borderRadius: 21,
    alignItems: 'center',
    justifyContent: 'center',
    position: 'relative',
  },
  centerBranding: {
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  brandTitle: {
    fontSize: 20,
    fontWeight: '900',
    color: colors.primaryDark,
    letterSpacing: 0.5,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  brandSubtitle: {
    fontSize: 8.5,
    fontWeight: '800',
    color: colors.textMuted,
    letterSpacing: 1.8,
    marginTop: 2,
  },
  rightActions: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  notifBadge: {
    position: 'absolute',
    top: 5,
    right: 5,
    backgroundColor: colors.primary,
    borderRadius: 8,
    minWidth: 16,
    height: 16,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 3,
  },
  notifBadgeText: {
    color: '#FFFFFF',
    fontSize: 9,
    fontWeight: '900',
  },
  cartBadge: {
    position: 'absolute',
    top: 4,
    right: 4,
    backgroundColor: colors.primary,
    borderRadius: 9,
    minWidth: 18,
    height: 18,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 4,
    borderWidth: 1.5,
    borderColor: colors.background,
  },
  cartBadgeText: {
    color: '#FFFFFF',
    fontSize: 9,
    fontWeight: '900',
  },
});
