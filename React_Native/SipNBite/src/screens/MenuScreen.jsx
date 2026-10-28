import React, { useState, useEffect, useCallback, useRef } from 'react';
import {
  View,
  Text,
  TextInput,
  FlatList,
  Pressable,
  Switch,
  ActivityIndicator,
  Animated,
  StyleSheet,
  Keyboard,
  RefreshControl,
  Platform,
  StatusBar as RNStatusBar,
} from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useFocusEffect } from '@react-navigation/native';
import { Ionicons } from '@expo/vector-icons';
import { useCart } from '../context/CartContext';
import { getCategories, getMenuItems } from '../api/ApiClient';
import CategoryCard from '../components/CategoryCard';
import MenuItemCard from '../components/MenuItemCard';
import { colors } from '../theme/colors';

export default function MenuScreen({ navigation }) {
  const insets = useSafeAreaInsets();
  const topInset = Math.max(
    insets.top,
    Platform.OS === 'android' ? (RNStatusBar.currentHeight || 28) : 12
  );
  const { addItem, getItemCount } = useCart();
  const cartCount = getItemCount();

  const [categories, setCategories] = useState([]);
  const [menuItems, setMenuItems] = useState([]);
  const [selectedCategoryId, setSelectedCategoryId] = useState(null);
  const [searchText, setSearchText] = useState('');
  const [isVegOnly, setIsVegOnly] = useState(false);
  const [loading, setLoading] = useState(false);
  const [initialLoad, setInitialLoad] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const debounceRef = useRef(null);
  const toastAnim = useRef(new Animated.Value(0)).current;
  const [toastMessage, setToastMessage] = useState('');

  const showToast = (msg) => {
    setToastMessage(msg);
    Animated.sequence([
      Animated.timing(toastAnim, { toValue: 1, duration: 200, useNativeDriver: true }),
      Animated.delay(1200),
      Animated.timing(toastAnim, { toValue: 0, duration: 200, useNativeDriver: true }),
    ]).start();
  };

  const fetchCategories = useCallback(async () => {
    try {
      const res = await getCategories();
      if (res.data?.success) setCategories(res.data.data || []);
    } catch (_) {}
  }, []);

  const fetchMenu = useCallback(async () => {
    setLoading(true);
    try {
      const params = {};
      if (selectedCategoryId) params.category_id = selectedCategoryId;
      if (searchText.trim()) params.search = searchText.trim();
      if (isVegOnly) params.is_veg = true;

      const res = await getMenuItems(params);
      if (res.data?.success) setMenuItems(res.data.data || []);
    } catch (_) {}
    setLoading(false);
    setInitialLoad(false);
  }, [selectedCategoryId, searchText, isVegOnly]);

  useFocusEffect(
    useCallback(() => {
      fetchCategories();
      fetchMenu();
    }, [fetchCategories, fetchMenu])
  );

  useEffect(() => {
    if (debounceRef.current) clearTimeout(debounceRef.current);
    debounceRef.current = setTimeout(fetchMenu, 300);
    return () => clearTimeout(debounceRef.current);
  }, [fetchMenu]);

  const onRefresh = async () => {
    setRefreshing(true);
    await Promise.all([fetchCategories(), fetchMenu()]);
    setRefreshing(false);
  };

  const handleCategoryPress = (cat) => {
    setSelectedCategoryId((prev) => (prev === cat.id ? null : cat.id));
  };

  const handleAddToCart = (item) => {
    addItem(item);
    showToast(`${item.name} added to cart!`);
  };

  const renderItem = ({ item }) => (
    <MenuItemCard
      item={item}
      variant="row"
      onAddToCart={handleAddToCart}
      onPress={() => navigation.navigate('MenuItemDetail', { item })}
    />
  );

  const renderHeader = () => (
    <View>
      {/* Search Bar */}
      <View style={styles.searchSection}>
        <View style={styles.searchBox}>
          <Ionicons name="search" size={18} color={colors.textMuted} style={{ marginRight: 10 }} />
          <TextInput
            placeholder="Search burgers, pizzas, snacks, meals..."
            placeholderTextColor={colors.textPlaceholder}
            style={styles.searchInput}
            value={searchText}
            onChangeText={setSearchText}
            returnKeyType="search"
          />
          {searchText.length > 0 && (
            <Pressable onPress={() => { setSearchText(''); Keyboard.dismiss(); }} style={styles.clearButton}>
              <Ionicons name="close-circle" size={18} color={colors.textMuted} />
            </Pressable>
          )}
        </View>
      </View>

      {/* Filters Row */}
      <View style={styles.filterRow}>
        <View style={styles.vegToggle}>
          <Text style={styles.vegLabel}>🌱 Veg Only</Text>
          <Switch
            value={isVegOnly}
            onValueChange={setIsVegOnly}
            trackColor={{ false: '#E2D8CE', true: '#A5D6A7' }}
            thumbColor={isVegOnly ? colors.success : '#B5A698'}
          />
        </View>
        {(selectedCategoryId || searchText || isVegOnly) && (
          <Pressable
            style={styles.clearFilters}
            onPress={() => {
              setSelectedCategoryId(null);
              setSearchText('');
              setIsVegOnly(false);
            }}
          >
            <Text style={styles.clearFiltersText}>Clear Filters ✕</Text>
          </Pressable>
        )}
      </View>

      {/* Category Chips */}
      {categories.length > 0 && (
        <FlatList
          horizontal
          data={categories}
          keyExtractor={(c) => c.id.toString()}
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.categoryList}
          renderItem={({ item: cat }) => (
            <CategoryCard
              category={cat}
              isActive={selectedCategoryId === cat.id}
              onPress={() => handleCategoryPress(cat)}
            />
          )}
        />
      )}

      {/* Results Header */}
      <View style={styles.resultsRow}>
        <Text style={styles.resultsText}>
          {loading ? 'Searching...' : `${menuItems.length} items`}
        </Text>
        {selectedCategoryId && (
          <Pressable onPress={() => setSelectedCategoryId(null)}>
            <Text style={styles.activeFilter}>
              {categories.find((c) => c.id === selectedCategoryId)?.name || 'Category'} ✕
            </Text>
          </Pressable>
        )}
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      {/* Title Bar */}
      <View style={[styles.titleBar, { paddingTop: topInset + 4 }]}>
        <View>
          <Text style={styles.titleText}>Cafe Menu</Text>
          <Text style={styles.titleSubtext}>Gourmet food, snacks & refreshing beverages</Text>
        </View>
        <Pressable
          style={styles.cartHeaderBtn}
          onPress={() => navigation.navigate('Cart')}
        >
          <Ionicons name="cart-outline" size={24} color={colors.primaryDark} />
          {cartCount > 0 && (
            <View style={styles.cartBadge}>
              <Text style={styles.cartBadgeText}>{cartCount}</Text>
            </View>
          )}
        </Pressable>
      </View>

      {initialLoad ? (
        <View style={styles.loadingState}>
          <ActivityIndicator size="large" color={colors.primary} />
          <Text style={styles.loadingText}>Loading menu...</Text>
        </View>
      ) : (
        <FlatList
          data={menuItems}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderItem}
          ListHeaderComponent={renderHeader()}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={onRefresh}
              colors={[colors.primary]}
              tintColor={colors.primary}
            />
          }
          ListEmptyComponent={
            !loading && (
              <View style={styles.emptyState}>
                <Ionicons name="cafe-outline" size={48} color={colors.textMuted} style={{ marginBottom: 10 }} />
                <Text style={styles.emptyTitle}>No items found</Text>
                <Text style={styles.emptySubtext}>Try a different search or category</Text>
                <Pressable style={styles.retryButton} onPress={onRefresh}>
                  <Ionicons name="refresh" size={16} color="#FFFFFF" style={{ marginRight: 6 }} />
                  <Text style={styles.retryButtonText}>Refresh Menu</Text>
                </Pressable>
              </View>
            )
          }
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          keyboardShouldPersistTaps="handled"
        />
      )}

      {/* Toast Notification */}
      <Animated.View
        style={[
          styles.toast,
          {
            opacity: toastAnim,
            transform: [{ translateY: toastAnim.interpolate({ inputRange: [0, 1], outputRange: [50, 0] }) }],
          },
        ]}
        pointerEvents="none"
      >
        <Text style={styles.toastText}>{toastMessage}</Text>
      </Animated.View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },

  titleBar: {
    backgroundColor: colors.background,
    paddingHorizontal: 20,
    paddingTop: Platform.OS === 'android' ? 16 : 10,
    paddingBottom: 10,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  titleText: {
    color: colors.primaryDark,
    fontSize: 26,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  titleSubtext: { color: colors.textMuted, fontSize: 12.5, marginTop: 2, fontWeight: '500' },
  cartHeaderBtn: {
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    alignItems: 'center',
    justifyContent: 'center',
    position: 'relative',
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
  },
  cartBadgeText: { color: '#FFFFFF', fontSize: 9, fontWeight: '900' },

  listContent: { paddingHorizontal: 16, paddingBottom: 100 },

  // Search
  searchSection: { marginTop: 6, marginBottom: 10 },
  searchBox: {
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 24,
    borderWidth: 1,
    borderColor: colors.border,
    flexDirection: 'row',
    height: 48,
    paddingHorizontal: 16,
  },
  searchInput: { color: colors.textDark, flex: 1, fontSize: 13.5 },
  clearButton: { alignItems: 'center', height: 32, justifyContent: 'center', width: 32 },

  // Filters
  filterRow: {
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  vegToggle: { alignItems: 'center', flexDirection: 'row', gap: 6 },
  vegLabel: { color: colors.textDark, fontSize: 12.5, fontWeight: '700' },
  clearFilters: {
    backgroundColor: colors.surfaceAlt,
    borderRadius: 12,
    paddingHorizontal: 12,
    paddingVertical: 6,
  },
  clearFiltersText: { color: colors.primary, fontSize: 11, fontWeight: '700' },

  // Categories
  categoryList: { paddingVertical: 6, marginBottom: 10 },

  // Results
  resultsRow: {
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
    marginTop: 2,
  },
  resultsText: { color: colors.textMuted, fontSize: 12, fontWeight: '700' },
  activeFilter: {
    backgroundColor: colors.surfaceAlt,
    borderRadius: 8,
    color: colors.primary,
    fontSize: 11,
    fontWeight: '700',
    paddingHorizontal: 10,
    paddingVertical: 4,
  },

  // Loading / Empty
  loadingState: { alignItems: 'center', flex: 1, justifyContent: 'center' },
  loadingText: { color: colors.textMuted, fontSize: 13, marginTop: 10 },
  emptyState: { alignItems: 'center', paddingVertical: 60 },
  emptyTitle: { color: colors.textDark, fontSize: 18, fontWeight: '800' },
  emptySubtext: { color: colors.textMuted, fontSize: 13, marginTop: 4, marginBottom: 16 },
  retryButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.primary,
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 20,
    marginTop: 10,
  },
  retryButtonText: { color: '#FFFFFF', fontSize: 13, fontWeight: '800' },

  // Toast
  toast: {
    alignItems: 'center',
    backgroundColor: colors.primaryDark,
    borderRadius: 14,
    bottom: 90,
    elevation: 10,
    left: 40,
    paddingHorizontal: 20,
    paddingVertical: 12,
    position: 'absolute',
    right: 40,
  },
  toastText: { color: '#FFFFFF', fontSize: 13, fontWeight: '800' },
});
