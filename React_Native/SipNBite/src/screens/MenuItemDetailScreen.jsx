import React, { useState, useRef } from 'react';
import {
  View,
  Text,
  Image,
  Pressable,
  ScrollView,
  Animated,
  StyleSheet,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useCart } from '../context/CartContext';
import { SERVER_IP } from '../config/AppConfig';
import { colors } from '../theme/colors';

export default function MenuItemDetailScreen({ route, navigation }) {
  const { item } = route.params;
  const { addItem, getCartItems } = useCart();
  const [quantity, setQuantity] = useState(1);

  const scaleAnim = useRef(new Animated.Value(1)).current;
  const fadeAnim = useRef(new Animated.Value(0)).current;

  React.useEffect(() => {
    Animated.timing(fadeAnim, { toValue: 1, duration: 300, useNativeDriver: true }).start();
  }, []);

  const imageUri = item.image
    ? (item.image.startsWith('http') ? item.image : `http://${SERVER_IP}:8000/storage/${item.image}`)
    : null;

  const handleAddToCart = () => {
    Animated.sequence([
      Animated.timing(scaleAnim, { toValue: 0.94, duration: 80, useNativeDriver: true }),
      Animated.timing(scaleAnim, { toValue: 1.04, duration: 100, useNativeDriver: true }),
      Animated.timing(scaleAnim, { toValue: 1, duration: 80, useNativeDriver: true }),
    ]).start();

    for (let i = 0; i < quantity; i++) {
      addItem(item);
    }
    setTimeout(() => navigation.goBack(), 300);
  };

  const existingInCart = getCartItems().find((ci) => ci.menuItem.id === item.id);

  return (
    <Animated.View style={[styles.container, { opacity: fadeAnim }]}>
      {/* Header with back button */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>Item Details</Text>
        <Pressable
          style={styles.cartIconBtn}
          onPress={() => navigation.navigate('Cart')}
        >
          <Ionicons name="cart-outline" size={22} color={colors.primaryDark} />
        </Pressable>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Image */}
        <View style={styles.imageContainer}>
          {imageUri ? (
            <Image source={{ uri: imageUri }} style={styles.image} resizeMode="cover" />
          ) : (
            <View style={styles.placeholderImage}>
              <Ionicons name="fast-food-outline" size={64} color={colors.primary} />
            </View>
          )}
          {/* Veg badge */}
          <View style={[styles.vegBadge, { backgroundColor: item.is_veg ? colors.success : colors.error }]}>
            <View style={[styles.vegDot, { borderColor: item.is_veg ? colors.success : colors.error }]} />
            <Text style={styles.vegText}>{item.is_veg ? 'Vegetarian' : 'Non-Vegetarian'}</Text>
          </View>
        </View>

        {/* Info */}
        <View style={styles.infoSection}>
          <Text style={styles.itemName}>{item.name}</Text>

          <View style={styles.metaRow}>
            <Text style={styles.price}>₹{Math.round(item.price)}</Text>
            {item.rating > 0 && (
              <View style={styles.ratingBadge}>
                <Ionicons name="star" size={13} color={colors.accentGold} style={{ marginRight: 4 }} />
                <Text style={styles.ratingText}>{item.rating.toFixed(1)}</Text>
              </View>
            )}
            {item.category && (
              <View style={styles.categoryBadge}>
                <Text style={styles.categoryText}>{item.category.name}</Text>
              </View>
            )}
          </View>

          {item.description ? (
            <View style={styles.descriptionCard}>
              <Text style={styles.descriptionLabel}>About this creation</Text>
              <Text style={styles.descriptionText}>{item.description}</Text>
            </View>
          ) : null}

          {/* Highlights / Badges */}
          <View style={styles.infoCards}>
            <View style={styles.infoCard}>
              <Ionicons name="time-outline" size={20} color={colors.primary} style={{ marginBottom: 4 }} />
              <Text style={styles.infoCardLabel}>Prep Time</Text>
              <Text style={styles.infoCardValue}>10-15 min</Text>
            </View>
            <View style={styles.infoCard}>
              <Ionicons name="flame-outline" size={20} color={colors.primary} style={{ marginBottom: 4 }} />
              <Text style={styles.infoCardLabel}>Calories</Text>
              <Text style={styles.infoCardValue}>~220 kcal</Text>
            </View>
            <View style={styles.infoCard}>
              <Ionicons name="star-outline" size={20} color={colors.primary} style={{ marginBottom: 4 }} />
              <Text style={styles.infoCardLabel}>Rating</Text>
              <Text style={styles.infoCardValue}>{item.rating > 0 ? item.rating.toFixed(1) : '4.8'}</Text>
            </View>
          </View>

          {existingInCart && (
            <View style={styles.inCartBanner}>
              <Text style={styles.inCartText}>
                ✓ Already {existingInCart.quantity} in your cart
              </Text>
            </View>
          )}
        </View>
      </ScrollView>

      {/* Bottom bar */}
      <View style={styles.bottomBar}>
        <View style={styles.quantityControls}>
          <Pressable
            style={styles.qtyButton}
            onPress={() => setQuantity((q) => Math.max(1, q - 1))}
          >
            <Ionicons name="remove" size={16} color={colors.primaryDark} />
          </Pressable>
          <Text style={styles.qtyValue}>{quantity}</Text>
          <Pressable
            style={styles.qtyButton}
            onPress={() => setQuantity((q) => q + 1)}
          >
            <Ionicons name="add" size={16} color={colors.primaryDark} />
          </Pressable>
        </View>

        <Animated.View style={{ flex: 1, transform: [{ scale: scaleAnim }] }}>
          <Pressable style={styles.addButton} onPress={handleAddToCart}>
            <Text style={styles.addButtonText}>
              Add to Cart • ₹{Math.round(item.price * quantity)}
            </Text>
          </Pressable>
        </Animated.View>
      </View>
    </Animated.View>
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
  cartIconBtn: { width: 36, height: 36, justifyContent: 'center', alignItems: 'flex-end' },
  headerTitle: {
    color: colors.primaryDark,
    fontSize: 18,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },

  scrollContent: { paddingBottom: 30 },

  // Image
  imageContainer: { height: 250, position: 'relative', width: '100%', backgroundColor: colors.surfaceAlt },
  image: { height: '100%', width: '100%' },
  placeholderImage: {
    alignItems: 'center',
    backgroundColor: colors.surfaceAlt,
    height: '100%',
    justifyContent: 'center',
    width: '100%',
  },
  vegBadge: {
    alignItems: 'center',
    borderRadius: 8,
    bottom: 14,
    flexDirection: 'row',
    left: 16,
    paddingHorizontal: 10,
    paddingVertical: 5,
    position: 'absolute',
  },
  vegDot: {
    borderRadius: 4,
    borderWidth: 2,
    height: 8,
    marginRight: 6,
    width: 8,
    backgroundColor: '#FFF',
  },
  vegText: { color: '#FFF', fontSize: 10.5, fontWeight: '800' },

  // Info
  infoSection: { paddingHorizontal: 20, paddingTop: 20 },
  itemName: {
    color: colors.textDark,
    fontSize: 24,
    fontWeight: '900',
    lineHeight: 30,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  metaRow: { alignItems: 'center', flexDirection: 'row', gap: 12, marginTop: 12 },
  price: { color: colors.primaryDark, fontSize: 24, fontWeight: '900' },
  ratingBadge: {
    backgroundColor: '#FFF8E1',
    borderRadius: 8,
    paddingHorizontal: 10,
    paddingVertical: 4,
    flexDirection: 'row',
    alignItems: 'center',
  },
  ratingText: { color: '#B45309', fontSize: 12, fontWeight: '800' },
  categoryBadge: {
    backgroundColor: colors.surfaceAlt,
    borderRadius: 8,
    paddingHorizontal: 10,
    paddingVertical: 4,
  },
  categoryText: { color: colors.primary, fontSize: 11, fontWeight: '700' },

  // Description
  descriptionCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: colors.border,
    marginTop: 20,
    padding: 18,
    elevation: 1,
  },
  descriptionLabel: { color: colors.textMuted, fontSize: 11, fontWeight: '700', letterSpacing: 0.6, marginBottom: 8, textTransform: 'uppercase' },
  descriptionText: { color: colors.textDark, fontSize: 13.5, lineHeight: 21 },

  // Info cards
  infoCards: { flexDirection: 'row', gap: 10, marginTop: 16 },
  infoCard: {
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
    flex: 1,
    paddingVertical: 14,
    elevation: 1,
  },
  infoCardLabel: { color: colors.textMuted, fontSize: 10, fontWeight: '600' },
  infoCardValue: { color: colors.textDark, fontSize: 13.5, fontWeight: '800', marginTop: 2 },

  // In cart banner
  inCartBanner: {
    backgroundColor: '#DCFCE7',
    borderRadius: 12,
    marginTop: 16,
    padding: 12,
    alignItems: 'center',
  },
  inCartText: { color: '#15803D', fontSize: 13, fontWeight: '700' },

  // Bottom bar
  bottomBar: {
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderTopColor: colors.border,
    borderTopWidth: 1,
    flexDirection: 'row',
    gap: 14,
    paddingBottom: Platform.OS === 'ios' ? 28 : 16,
    paddingHorizontal: 20,
    paddingTop: 14,
  },
  quantityControls: { alignItems: 'center', flexDirection: 'row', gap: 6 },
  qtyButton: {
    alignItems: 'center',
    backgroundColor: colors.surfaceAlt,
    borderRadius: 10,
    height: 40,
    justifyContent: 'center',
    width: 40,
  },
  qtyValue: { color: colors.textDark, fontSize: 17, fontWeight: '800', minWidth: 28, textAlign: 'center' },

  addButton: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 14,
    height: 48,
    justifyContent: 'center',
  },
  addButtonText: { color: '#FFFFFF', fontSize: 14, fontWeight: '800' },
});
