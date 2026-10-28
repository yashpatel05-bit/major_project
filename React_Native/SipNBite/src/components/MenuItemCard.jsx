import React, { useRef } from 'react';
import { View, Text, Pressable, Image, Animated, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { SERVER_IP } from '../config/AppConfig';
import { colors } from '../theme/colors';

export default function MenuItemCard({ item, onAddToCart, onPress, variant = 'row' }) {
  const imageUri = item.image
    ? (item.image.startsWith('http') ? item.image : `http://${SERVER_IP}:8000/storage/${item.image}`)
    : null;

  const scaleAnim = useRef(new Animated.Value(1)).current;

  const handlePressIn = () => {
    Animated.spring(scaleAnim, { toValue: 0.97, useNativeDriver: true, speed: 50 }).start();
  };
  const handlePressOut = () => {
    Animated.spring(scaleAnim, { toValue: 1, useNativeDriver: true, speed: 50 }).start();
  };

  const handleAdd = () => {
    Animated.sequence([
      Animated.timing(scaleAnim, { toValue: 0.94, duration: 60, useNativeDriver: true }),
      Animated.timing(scaleAnim, { toValue: 1.04, duration: 80, useNativeDriver: true }),
      Animated.timing(scaleAnim, { toValue: 1, duration: 60, useNativeDriver: true }),
    ]).start();
    onAddToCart(item);
  };

  const badgeText = item.badge || (item.is_veg ? 'POPULAR' : 'CHEF’S PICK');

  if (variant === 'card') {
    // ─── Vertical/Portrait Card (Used in "Popular Picks" carousel) ────
    return (
      <Animated.View style={[styles.portraitWrapper, { transform: [{ scale: scaleAnim }] }]}>
        <Pressable
          style={styles.portraitCard}
          onPress={onPress}
          onPressIn={handlePressIn}
          onPressOut={handlePressOut}
          accessibilityLabel={`View ${item.name} details`}
        >
          {/* Image & Badge */}
          <View style={styles.portraitImageWrap}>
            {imageUri ? (
              <Image source={{ uri: imageUri }} style={styles.portraitImage} resizeMode="cover" />
            ) : (
              <View style={styles.portraitPlaceholder}>
                <Ionicons name="fast-food-outline" size={34} color={colors.primary} />
              </View>
            )}
            <View style={styles.portraitBadge}>
              <Text style={styles.portraitBadgeText}>{badgeText}</Text>
            </View>
          </View>

          {/* Details */}
          <View style={styles.portraitContent}>
            <Text style={styles.portraitName} numberOfLines={1}>{item.name}</Text>
            <Text style={styles.portraitDesc} numberOfLines={2}>
              {item.description || 'Deliciously prepared with fresh ingredients'}
            </Text>

            <View style={styles.portraitRatingRow}>
              <Ionicons name="star" size={12} color={colors.accentGold} style={{ marginRight: 3 }} />
              <Text style={styles.portraitRatingText}>
                {item.rating > 0 ? item.rating.toFixed(1) : '4.8'}
              </Text>
              <Text style={styles.portraitRatingCount}>(1.2k+)</Text>
            </View>

            {/* Price & Add */}
            <View style={styles.portraitBottomRow}>
              <Text style={styles.portraitPrice}>₹{Math.round(item.price)}</Text>
              <Pressable
                style={styles.portraitAddBtn}
                onPress={(e) => {
                  e.stopPropagation?.();
                  handleAdd();
                }}
                accessibilityLabel={`Add ${item.name}`}
              >
                <Ionicons name="add" size={16} color="#FFFFFF" />
              </Pressable>
            </View>
          </View>
        </Pressable>
      </Animated.View>
    );
  }

  // ─── Row Card (Used in Menu List) ─────────────────────────────
  return (
    <Animated.View style={[styles.cardWrapper, { transform: [{ scale: scaleAnim }] }]}>
      <Pressable
        style={styles.card}
        onPress={onPress}
        onPressIn={handlePressIn}
        onPressOut={handlePressOut}
        accessibilityLabel={`View ${item.name} details`}
      >
        <View style={styles.imageWrap}>
          {imageUri ? (
            <Image source={{ uri: imageUri }} style={styles.image} resizeMode="cover" />
          ) : (
            <View style={styles.placeholderImage}>
              <Ionicons name="cafe-outline" size={28} color={colors.primary} />
            </View>
          )}
          <View style={[styles.vegBadge, { backgroundColor: item.is_veg ? colors.success : colors.error }]}>
            <Text style={styles.vegBadgeText}>{item.is_veg ? 'VEG' : 'NON'}</Text>
          </View>
        </View>

        <View style={styles.infoWrap}>
          <Text style={styles.name} numberOfLines={1}>{item.name}</Text>
          {item.description ? (
            <Text style={styles.description} numberOfLines={2}>{item.description}</Text>
          ) : null}
          <View style={styles.bottomRow}>
            <Text style={styles.price}>₹{Math.round(item.price)}</Text>
            {item.rating > 0 && (
              <View style={{ flexDirection: 'row', alignItems: 'center', marginLeft: 10 }}>
                <Ionicons name="star" size={11} color={colors.accentGold} style={{ marginRight: 2 }} />
                <Text style={styles.rating}>{item.rating.toFixed(1)}</Text>
              </View>
            )}
          </View>
        </View>

        <Pressable
          style={styles.addButton}
          onPress={(e) => {
            e.stopPropagation?.();
            handleAdd();
          }}
          accessibilityLabel={`Add ${item.name} to cart`}
        >
          <Ionicons name="add" size={18} color="#FFFFFF" />
        </Pressable>
      </Pressable>
    </Animated.View>
  );
}

const styles = StyleSheet.create({
  // ─── Portrait Variant (Matches Reference Screenshot) ──
  portraitWrapper: {
    marginRight: 14,
    width: 160,
  },
  portraitCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 18,
    borderWidth: 1,
    borderColor: colors.border,
    padding: 10,
    elevation: 3,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.08,
    shadowRadius: 6,
  },
  portraitImageWrap: {
    width: '100%',
    height: 110,
    borderRadius: 14,
    overflow: 'hidden',
    position: 'relative',
    backgroundColor: colors.surfaceAlt,
  },
  portraitImage: {
    width: '100%',
    height: '100%',
  },
  portraitPlaceholder: {
    width: '100%',
    height: '100%',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.accentLight,
  },
  portraitBadge: {
    position: 'absolute',
    top: 6,
    left: 6,
    backgroundColor: colors.badgeDark,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
  },
  portraitBadgeText: {
    color: colors.badgeText,
    fontSize: 8.5,
    fontWeight: '800',
    letterSpacing: 0.4,
  },
  portraitContent: {
    paddingTop: 8,
  },
  portraitName: {
    fontSize: 14,
    fontWeight: '800',
    color: colors.textDark,
  },
  portraitDesc: {
    fontSize: 10,
    color: colors.textMuted,
    lineHeight: 14,
    marginTop: 2,
  },
  portraitRatingRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 6,
  },
  portraitRatingText: {
    fontSize: 11,
    fontWeight: '800',
    color: colors.textDark,
  },
  portraitRatingCount: {
    fontSize: 10,
    color: colors.textMuted,
    marginLeft: 3,
  },
  portraitBottomRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 8,
  },
  portraitPrice: {
    fontSize: 15,
    fontWeight: '900',
    color: colors.textDark,
  },
  portraitAddBtn: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 2,
  },

  // ─── Row Variant (Used in Menu List) ───────────────────
  cardWrapper: {
    marginBottom: 12,
  },
  card: {
    backgroundColor: '#FFFFFF',
    borderRadius: 18,
    borderWidth: 1,
    borderColor: colors.border,
    elevation: 2,
    flexDirection: 'row',
    overflow: 'hidden',
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 5,
  },
  imageWrap: {
    height: 96,
    position: 'relative',
    width: 96,
  },
  image: {
    borderRadius: 14,
    height: 80,
    margin: 8,
    width: 80,
  },
  placeholderImage: {
    alignItems: 'center',
    backgroundColor: colors.accentLight,
    borderRadius: 14,
    height: 80,
    justifyContent: 'center',
    margin: 8,
    width: 80,
  },
  vegBadge: {
    borderRadius: 6,
    left: 12,
    paddingHorizontal: 6,
    paddingVertical: 2,
    position: 'absolute',
    top: 12,
  },
  vegBadgeText: {
    color: '#FFF',
    fontSize: 8.5,
    fontWeight: '900',
  },
  infoWrap: {
    flex: 1,
    justifyContent: 'center',
    paddingVertical: 12,
    paddingHorizontal: 4,
  },
  name: {
    color: colors.textDark,
    fontSize: 15,
    fontWeight: '800',
  },
  description: {
    color: colors.textMuted,
    fontSize: 11,
    lineHeight: 15,
    marginTop: 3,
    fontWeight: '500',
  },
  bottomRow: {
    alignItems: 'center',
    flexDirection: 'row',
    marginTop: 6,
  },
  price: {
    color: colors.primaryDark,
    fontSize: 15,
    fontWeight: '900',
  },
  rating: {
    color: colors.textDark,
    fontSize: 11,
    fontWeight: '800',
  },
  addButton: {
    alignItems: 'center',
    alignSelf: 'center',
    backgroundColor: colors.primary,
    borderRadius: 14,
    height: 34,
    justifyContent: 'center',
    marginRight: 14,
    width: 34,
    elevation: 2,
  },
});
