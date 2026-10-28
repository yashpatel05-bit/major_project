import React from 'react';
import { Pressable, View, Text, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';

export default function CategoryCard({ category, isActive, isSelected, onPress }) {
  const active = isActive || isSelected;
  const iconConfig = getCategoryIcon(category.name);

  return (
    <Pressable
      style={styles.container}
      onPress={onPress}
      accessibilityRole="button"
      accessibilityLabel={category.name}
    >
      <View style={[styles.circle, active ? styles.circleActive : styles.circleInactive]}>
        <Ionicons
          name={active ? iconConfig.activeName : iconConfig.name}
          size={24}
          color={active ? '#FFFFFF' : colors.primary}
        />
      </View>
      <Text
        style={[styles.label, active && styles.labelActive]}
        numberOfLines={1}
      >
        {category.name}
      </Text>
    </Pressable>
  );
}

function getCategoryIcon(name) {
  const lower = (name || '').toLowerCase();
  if (lower.includes('coffee') || lower.includes('hot') || lower.includes('brew')) {
    return { name: 'cafe-outline', activeName: 'cafe' };
  }
  if (lower.includes('pastry') || lower.includes('bakery') || lower.includes('croissant') || lower.includes('bread')) {
    return { name: 'nutrition-outline', activeName: 'nutrition' };
  }
  if (lower.includes('sandwich') || lower.includes('burger') || lower.includes('snack') || lower.includes('bite')) {
    return { name: 'fast-food-outline', activeName: 'fast-food' };
  }
  if (lower.includes('dessert') || lower.includes('sweet') || lower.includes('cake') || lower.includes('muffin')) {
    return { name: 'ice-cream-outline', activeName: 'ice-cream' };
  }
  if (lower.includes('drink') || lower.includes('beverage') || lower.includes('shake') || lower.includes('juice')) {
    return { name: 'wine-outline', activeName: 'wine' };
  }
  if (lower.includes('breakfast') || lower.includes('meal') || lower.includes('special')) {
    return { name: 'restaurant-outline', activeName: 'restaurant' };
  }
  return { name: 'cafe-outline', activeName: 'cafe' };
}

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
    marginRight: 14,
    width: 68,
  },
  circle: {
    width: 58,
    height: 58,
    borderRadius: 29,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 2,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
  },
  circleInactive: {
    backgroundColor: '#FFFFFF',
    borderWidth: 1.5,
    borderColor: colors.border,
  },
  circleActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primary,
    transform: [{ scale: 1.04 }],
  },
  label: {
    color: colors.textMuted,
    fontSize: 11.5,
    fontWeight: '600',
    marginTop: 6,
    textAlign: 'center',
  },
  labelActive: {
    color: colors.primaryDark,
    fontWeight: '800',
  },
});
