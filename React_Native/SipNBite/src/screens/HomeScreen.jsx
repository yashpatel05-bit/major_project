import React, { useState, useEffect, useCallback, useRef } from 'react';
import {
  View,
  Text,
  TextInput,
  ScrollView,
  Pressable,
  RefreshControl,
  Animated,
  StyleSheet,
  Image,
  Modal,
  Switch,
  Keyboard,
  Platform,
} from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { useFocusEffect } from '@react-navigation/native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { getCategories, getMenuItems } from '../api/ApiClient';
import TopActionBar from '../components/TopActionBar';
import CategoryCard from '../components/CategoryCard';
import MenuItemCard from '../components/MenuItemCard';
import { colors } from '../theme/colors';

export default function HomeScreen({ navigation }) {
  const { user } = useAuth();
  const { addItem, getItemCount } = useCart();

  const [categories, setCategories] = useState([]);
  const [menuItems, setMenuItems] = useState([]);
  const [selectedCategoryId, setSelectedCategoryId] = useState(null);
  const [searchText, setSearchText] = useState('');
  const [isVegOnly, setIsVegOnly] = useState(false);
  const [sortBy, setSortBy] = useState('popular'); // 'popular', 'price_asc', 'price_desc', 'rating'
  const [showFilterModal, setShowFilterModal] = useState(false);
  const [showDrawerModal, setShowDrawerModal] = useState(false);
  const [refreshing, setRefreshing] = useState(false);

  // Animations
  const heroAnim = useRef(new Animated.Value(0)).current;
  const toastAnim = useRef(new Animated.Value(0)).current;
  const [toastMessage, setToastMessage] = useState('');

  useEffect(() => {
    Animated.timing(heroAnim, { toValue: 1, duration: 500, useNativeDriver: true }).start();
  }, []);

  const showToast = (msg) => {
    setToastMessage(msg);
    Animated.sequence([
      Animated.timing(toastAnim, { toValue: 1, duration: 200, useNativeDriver: true }),
      Animated.delay(1200),
      Animated.timing(toastAnim, { toValue: 0, duration: 200, useNativeDriver: true }),
    ]).start();
  };

  const fetchData = useCallback(async () => {
    try {
      const params = {};
      if (selectedCategoryId) params.category_id = selectedCategoryId;
      if (searchText.trim()) params.search = searchText.trim();
      if (isVegOnly) params.is_veg = true;

      const [catRes, menuRes] = await Promise.all([
        getCategories(),
        getMenuItems(params),
      ]);
      if (catRes.data?.success) setCategories(catRes.data.data || []);
      if (menuRes.data?.success) setMenuItems(menuRes.data.data || []);
    } catch (_) {}
  }, [selectedCategoryId, searchText, isVegOnly]);

  useFocusEffect(
    useCallback(() => {
      fetchData();
    }, [fetchData])
  );

  useEffect(() => {
    const timer = setTimeout(() => {
      fetchData();
    }, 200);
    return () => clearTimeout(timer);
  }, [fetchData]);

  const onRefresh = async () => {
    setRefreshing(true);
    await fetchData();
    setRefreshing(false);
  };

  const handleCategoryPress = (cat) => {
    setSelectedCategoryId((prev) => (prev === cat.id ? null : cat.id));
  };

  const handleAddToCart = (item) => {
    addItem(item);
    showToast(`${item.name} added to cart!`);
  };

  let sortedItems = [...menuItems];
  if (sortBy === 'price_asc') {
    sortedItems.sort((a, b) => a.price - b.price);
  } else if (sortBy === 'price_desc') {
    sortedItems.sort((a, b) => b.price - a.price);
  } else if (sortBy === 'rating') {
    sortedItems.sort((a, b) => (b.rating || 0) - (a.rating || 0));
  }
  const popularPicks = sortedItems.slice(0, 10);

  return (
    <View style={styles.container}>
      <StatusBar style="dark" backgroundColor="transparent" translucent={true} />

      {/* Top Action Bar with side drawer toggle */}
      <TopActionBar
        navigation={navigation}
        onMenuPress={() => setShowDrawerModal(true)}
      />

      <ScrollView
        style={styles.scrollView}
        contentContainerStyle={styles.content}
        showsVerticalScrollIndicator={false}
        keyboardShouldPersistTaps="handled"
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            colors={[colors.primary]}
            tintColor={colors.primary}
          />
        }
      >
        {/* Real In-Place Search Bar with Filter Button */}
        <View style={styles.searchRow}>
          <View style={styles.searchBox}>
            <Ionicons name="search" size={19} color={colors.textMuted} style={{ marginRight: 8 }} />
            <TextInput
              style={styles.searchInput}
              placeholder="Search burgers, pizzas, snacks & drinks..."
              placeholderTextColor={colors.textPlaceholder}
              value={searchText}
              onChangeText={setSearchText}
              returnKeyType="search"
            />
            {searchText.length > 0 && (
              <Pressable
                onPress={() => {
                  setSearchText('');
                  Keyboard.dismiss();
                }}
                style={styles.clearSearchBtn}
              >
                <Ionicons name="close-circle" size={18} color={colors.textMuted} />
              </Pressable>
            )}
          </View>
          <Pressable
            style={[
              styles.filterButton,
              (isVegOnly || sortBy !== 'popular') && styles.filterButtonActive,
            ]}
            onPress={() => setShowFilterModal(true)}
            accessibilityLabel="Filter options"
          >
            <Ionicons
              name="options-outline"
              size={20}
              color={(isVegOnly || sortBy !== 'popular') ? '#FFFFFF' : colors.primaryDark}
            />
            {(isVegOnly || sortBy !== 'popular') && <View style={styles.filterActiveDot} />}
          </Pressable>
        </View>

        {/* Active Filter Badges */}
        {(isVegOnly || sortBy !== 'popular' || searchText) ? (
          <View style={styles.activeFiltersRow}>
            {isVegOnly && (
              <Pressable
                style={styles.filterChip}
                onPress={() => setIsVegOnly(false)}
              >
                <Text style={styles.filterChipText}>🌱 Veg Only ✕</Text>
              </Pressable>
            )}
            {sortBy === 'price_asc' && (
              <Pressable
                style={styles.filterChip}
                onPress={() => setSortBy('popular')}
              >
                <Text style={styles.filterChipText}>₹ Low to High ✕</Text>
              </Pressable>
            )}
            {sortBy === 'price_desc' && (
              <Pressable
                style={styles.filterChip}
                onPress={() => setSortBy('popular')}
              >
                <Text style={styles.filterChipText}>₹ High to Low ✕</Text>
              </Pressable>
            )}
            {sortBy === 'rating' && (
              <Pressable
                style={styles.filterChip}
                onPress={() => setSortBy('popular')}
              >
                <Text style={styles.filterChipText}>⭐ Top Rated ✕</Text>
              </Pressable>
            )}
            {searchText ? (
              <Pressable
                style={styles.filterChip}
                onPress={() => setSearchText('')}
              >
                <Text style={styles.filterChipText}>"{searchText}" ✕</Text>
              </Pressable>
            ) : null}
            <Pressable
              onPress={() => {
                setIsVegOnly(false);
                setSortBy('popular');
                setSearchText('');
                setSelectedCategoryId(null);
              }}
              style={styles.resetAllBtn}
            >
              <Text style={styles.resetAllText}>Reset</Text>
            </Pressable>
          </View>
        ) : null}

        {/* Hero Banner / Cafe Food Card */}
        <Animated.View
          style={[
            styles.heroCard,
            {
              opacity: heroAnim,
              transform: [
                {
                  translateY: heroAnim.interpolate({
                    inputRange: [0, 1],
                    outputRange: [24, 0],
                  }),
                },
              ],
            },
          ]}
        >
          <View style={styles.heroContent}>
            <Text style={styles.heroScript}>Fresh & Tasty</Text>
            <Text style={styles.heroHeading}>Savor Every Bite</Text>
            <Text style={styles.heroSubtitle}>
              Crispy wraps, gourmet burgers, artisan pizzas & refreshing drinks.
            </Text>
            <Pressable
              style={styles.orderNowBtn}
              onPress={() => navigation.navigate('MenuTab')}
            >
              <Text style={styles.orderNowText}>ORDER NOW</Text>
              <View style={styles.orderNowArrowCircle}>
                <Ionicons name="arrow-forward" size={12} color="#FFFFFF" />
              </View>
            </Pressable>
          </View>

          <View style={styles.heroVisualWrap}>
            <View style={styles.coffeeCupDisc}>
              <Ionicons name="fast-food" size={48} color="#F3E9DF" />
            </View>
          </View>
        </Animated.View>

        {/* Carousel Pagination Dots */}
        <View style={styles.dotsRow}>
          <View style={[styles.dot, styles.dotActive]} />
          <View style={styles.dot} />
          <View style={styles.dot} />
          <View style={styles.dot} />
        </View>

        {/* Horizontal Category Circular Icons */}
        <View style={styles.categoriesSection}>
          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.categoriesScroll}
          >
            {categories.map((cat) => (
              <CategoryCard
                key={cat.id}
                category={cat}
                isSelected={selectedCategoryId === cat.id}
                onPress={() => handleCategoryPress(cat)}
              />
            ))}
          </ScrollView>
        </View>

        {/* Popular Picks Section */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Popular Picks</Text>
          <Pressable
            style={styles.viewAllRow}
            onPress={() => navigation.navigate('MenuTab')}
          >
            <Text style={styles.viewAllText}>View All</Text>
            <Ionicons name="arrow-forward" size={14} color={colors.textDark} style={{ marginLeft: 3 }} />
          </Pressable>
        </View>

        {/* Popular Picks Horizontal Scroll */}
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.popularScroll}
        >
          {popularPicks.length === 0 ? (
            <View style={styles.emptyPicks}>
              <Ionicons name="cafe-outline" size={32} color={colors.textMuted} />
              <Text style={styles.emptyPicksText}>No items found</Text>
            </View>
          ) : (
            popularPicks.map((item, index) => (
              <MenuItemCard
                key={item.id}
                item={{
                  ...item,
                  badge: index === 0 ? 'BESTSELLER' : index === 1 ? 'CHEF’S PICK' : 'POPULAR',
                }}
                variant="card"
                onAddToCart={() => handleAddToCart(item)}
                onPress={() => navigation.navigate('MenuItemDetail', { item })}
              />
            ))
          )}
        </ScrollView>

        {/* Weekend Special / Promo Banner */}
        <View style={styles.promoBanner}>
          <View style={styles.promoContent}>
            <Text style={styles.promoKicker}>WEEKEND SPECIAL</Text>
            <Text style={styles.promoPercent}>20% OFF</Text>
            <Text style={styles.promoSub}>On all food & drink orders</Text>
            <Pressable
              style={styles.promoBtn}
              onPress={() => navigation.navigate('MenuTab')}
            >
              <Text style={styles.promoBtnText}>ORDER NOW</Text>
              <Ionicons name="arrow-forward" size={12} color="#FFFFFF" style={{ marginLeft: 4 }} />
            </Pressable>
          </View>

          <View style={styles.promoVisual}>
            <View style={styles.discountBadge}>
              <Text style={styles.discountBadgeTop}>20%</Text>
              <Text style={styles.discountBadgeBottom}>OFF</Text>
            </View>
            <Ionicons name="fast-food-outline" size={54} color={colors.primary} style={{ transform: [{ rotate: '5deg' }] }} />
          </View>
        </View>

        {/* Extra Bottom Space */}
        <View style={{ height: 40 }} />
      </ScrollView>

      {/* Floating AI Chatbot Button */}
      <Pressable
        style={styles.aiFab}
        onPress={() => navigation.navigate('ChatBot')}
        accessibilityLabel="Sip & Bite AI Assistant"
      >
        <Ionicons name="sparkles" size={24} color="#FFFFFF" />
      </Pressable>

      {/* Toast Notification */}
      <Animated.View
        style={[
          styles.toast,
          {
            opacity: toastAnim,
            transform: [
              {
                translateY: toastAnim.interpolate({
                  inputRange: [0, 1],
                  outputRange: [50, 0],
                }),
              },
            ],
          },
        ]}
        pointerEvents="none"
      >
        <Text style={styles.toastText}>{toastMessage}</Text>
      </Animated.View>

      {/* Quick In-Place Filter Modal */}
      <Modal
        visible={showFilterModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowFilterModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.filterModalCard}>
            <View style={styles.filterModalHeader}>
              <Text style={styles.filterModalTitle}>Filter & Sort Items</Text>
              <Pressable onPress={() => setShowFilterModal(false)} style={{ padding: 4 }}>
                <Ionicons name="close" size={24} color={colors.textDark} />
              </Pressable>
            </View>

            {/* Veg Switch */}
            <View style={styles.filterModalRow}>
              <View>
                <Text style={styles.filterSectionTitle}>Vegetarian Only 🌱</Text>
                <Text style={styles.filterSectionSub}>Show strictly vegetarian items</Text>
              </View>
              <Switch
                value={isVegOnly}
                onValueChange={setIsVegOnly}
                trackColor={{ false: '#E2D8CE', true: '#A5D6A7' }}
                thumbColor={isVegOnly ? colors.success : '#B5A698'}
              />
            </View>

            {/* Sort Options */}
            <Text style={[styles.filterSectionTitle, { marginTop: 16, marginBottom: 10 }]}>
              Sort By
            </Text>
            <View style={styles.sortOptionGrid}>
              {[
                { id: 'popular', label: '🔥 Popular Picks' },
                { id: 'price_asc', label: '₹ Price: Low to High' },
                { id: 'price_desc', label: '₹ Price: High to Low' },
                { id: 'rating', label: '⭐ Highest Rated' },
              ].map((opt) => (
                <Pressable
                  key={opt.id}
                  style={[
                    styles.sortChip,
                    sortBy === opt.id && styles.sortChipActive,
                  ]}
                  onPress={() => setSortBy(opt.id)}
                >
                  <Text
                    style={[
                      styles.sortChipText,
                      sortBy === opt.id && styles.sortChipTextActive,
                    ]}
                  >
                    {opt.label}
                  </Text>
                </Pressable>
              ))}
            </View>

            {/* Action Buttons */}
            <View style={styles.filterModalActions}>
              <Pressable
                style={styles.modalResetBtn}
                onPress={() => {
                  setIsVegOnly(false);
                  setSortBy('popular');
                }}
              >
                <Text style={styles.modalResetText}>Reset</Text>
              </Pressable>
              <Pressable
                style={styles.modalApplyBtn}
                onPress={() => setShowFilterModal(false)}
              >
                <Text style={styles.modalApplyText}>Apply Filters</Text>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>

      {/* Side Navigation Drawer Modal (Hamburger) */}
      <Modal
        visible={showDrawerModal}
        animationType="fade"
        transparent={true}
        onRequestClose={() => setShowDrawerModal(false)}
      >
        <View style={styles.drawerOverlay}>
          <Pressable
            style={styles.drawerBackdrop}
            onPress={() => setShowDrawerModal(false)}
          />
          <View style={styles.drawerContainer}>
            {/* Drawer Header */}
            <View style={styles.drawerHeader}>
              <View style={styles.drawerBrandRow}>
                <Ionicons name="restaurant" size={22} color={colors.accentGold} style={{ marginRight: 8 }} />
                <Text style={styles.drawerBrandTitle}>Sip & Bite</Text>
              </View>
              <Text style={styles.drawerBrandSub}>CAFE & FOOD</Text>
              <Text style={styles.drawerGreeting}>
                Hello, {user ? user.name : 'Food Lover'} 👋
              </Text>
            </View>

            {/* Nav Links */}
            <ScrollView style={styles.drawerNavList} showsVerticalScrollIndicator={false}>
              {[
                { icon: 'home-outline', label: 'Home Screen', screen: 'HomeTab' },
                { icon: 'book-outline', label: 'Explore Full Menu', screen: 'MenuTab' },
                { icon: 'receipt-outline', label: 'My Orders & Live Track', screen: 'OrdersTab' },
                { icon: 'calendar-outline', label: 'Reserve Table', screen: 'TableBookingTab' },
                { icon: 'location-outline', label: 'Saved Addresses', screen: 'Address' },
                { icon: 'chatbubble-ellipses-outline', label: 'Ratings & Feedback', screen: 'Feedback' },
                { icon: 'information-circle-outline', label: 'About Sip & Bite', screen: 'About' },
                { icon: 'person-outline', label: 'My Profile', screen: 'ProfileTab' },
              ].map((item) => (
                <Pressable
                  key={item.label}
                  style={styles.drawerItem}
                  onPress={() => {
                    setShowDrawerModal(false);
                    navigation.navigate(item.screen);
                  }}
                >
                  <Ionicons name={item.icon} size={20} color={colors.primaryDark} style={{ marginRight: 14 }} />
                  <Text style={styles.drawerItemText}>{item.label}</Text>
                  <Ionicons name="chevron-forward" size={16} color={colors.textMuted} style={{ marginLeft: 'auto' }} />
                </Pressable>
              ))}
            </ScrollView>

            <Pressable
              style={styles.drawerCloseBtn}
              onPress={() => setShowDrawerModal(false)}
            >
              <Text style={styles.drawerCloseText}>Close Menu ✕</Text>
            </Pressable>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  scrollView: {
    flex: 1,
  },
  content: {
    paddingHorizontal: 16,
    paddingTop: 4,
    paddingBottom: 20,
  },

  // Search Row
  searchRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    marginTop: 8,
    marginBottom: 16,
  },
  searchBox: {
    flex: 1,
    height: 48,
    borderRadius: 24,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    elevation: 1,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 4,
  },
  searchInput: {
    flex: 1,
    fontSize: 13,
    color: colors.textDark,
    fontWeight: '600',
    height: 44,
  },
  clearSearchBtn: {
    padding: 4,
  },
  filterButton: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 1,
    position: 'relative',
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 4,
  },
  filterButtonActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primary,
  },
  filterActiveDot: {
    position: 'absolute',
    top: 9,
    right: 9,
    width: 7,
    height: 7,
    borderRadius: 3.5,
    backgroundColor: colors.accentGold,
  },
  activeFiltersRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
    marginBottom: 14,
    alignItems: 'center',
  },
  filterChip: {
    backgroundColor: colors.surfaceAlt,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: colors.border,
  },
  filterChipText: {
    fontSize: 11,
    fontWeight: '700',
    color: colors.textDark,
  },
  resetAllBtn: {
    paddingHorizontal: 8,
    paddingVertical: 6,
  },
  resetAllText: {
    fontSize: 11,
    fontWeight: '800',
    color: colors.primary,
  },

  // Hero Card
  heroCard: {
    backgroundColor: '#1E140F',
    borderRadius: 24,
    padding: 20,
    flexDirection: 'row',
    alignItems: 'center',
    minHeight: 160,
    elevation: 4,
    shadowColor: '#1E140F',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
  },
  heroContent: {
    flex: 1,
    zIndex: 1,
  },
  heroScript: {
    fontFamily: Platform.OS === 'ios' ? 'Snell Roundhand' : 'serif',
    fontStyle: 'italic',
    fontSize: 19,
    color: '#EBDCCE',
    fontWeight: '600',
  },
  heroHeading: {
    fontSize: 24,
    fontWeight: '900',
    color: '#FFFFFF',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
    marginTop: 1,
  },
  heroSubtitle: {
    fontSize: 11,
    color: '#D4C4B5',
    lineHeight: 15,
    marginTop: 6,
    maxWidth: 180,
  },
  orderNowBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.15)',
    borderRadius: 20,
    paddingLeft: 12,
    paddingRight: 6,
    paddingVertical: 6,
    alignSelf: 'flex-start',
    marginTop: 14,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.25)',
  },
  orderNowText: {
    color: '#FFFFFF',
    fontSize: 10.5,
    fontWeight: '900',
    letterSpacing: 0.5,
    marginRight: 6,
  },
  orderNowArrowCircle: {
    width: 20,
    height: 20,
    borderRadius: 10,
    backgroundColor: colors.accent,
    alignItems: 'center',
    justifyContent: 'center',
  },
  heroVisualWrap: {
    width: 90,
    alignItems: 'center',
    justifyContent: 'center',
  },
  coffeeCupDisc: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: 'rgba(255, 255, 255, 0.08)',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1.5,
    borderColor: 'rgba(255, 255, 255, 0.12)',
  },

  // Pagination Dots
  dotsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    marginTop: 12,
    marginBottom: 16,
  },
  dot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#D1C4B7',
  },
  dotActive: {
    width: 18,
    borderRadius: 4,
    backgroundColor: colors.primary,
  },

  // Categories Section
  categoriesSection: {
    marginBottom: 20,
  },
  categoriesScroll: {
    paddingVertical: 4,
  },

  // Section Header
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '900',
    color: colors.textDark,
  },
  viewAllRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  viewAllText: {
    fontSize: 12.5,
    fontWeight: '800',
    color: colors.textDark,
  },

  // Popular Picks Scroll
  popularScroll: {
    paddingVertical: 4,
    paddingRight: 60,
  },
  emptyPicks: {
    width: 200,
    height: 120,
    alignItems: 'center',
    justifyContent: 'center',
  },
  emptyPicksText: {
    color: colors.textMuted,
    fontSize: 12,
    marginTop: 6,
  },

  // Promo Banner
  promoBanner: {
    backgroundColor: '#F3ECE3',
    borderRadius: 22,
    padding: 18,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 20,
    borderWidth: 1,
    borderColor: '#E7DDD2',
  },
  promoContent: {
    flex: 1,
  },
  promoKicker: {
    fontSize: 9,
    fontWeight: '800',
    color: '#8A7669',
    letterSpacing: 1.2,
  },
  promoPercent: {
    fontSize: 26,
    fontWeight: '900',
    color: colors.primaryDark,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
    marginTop: 2,
  },
  promoSub: {
    fontSize: 11,
    color: '#6E5C50',
    fontWeight: '600',
    marginTop: 2,
  },
  promoBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 16,
    paddingHorizontal: 12,
    paddingVertical: 7,
    alignSelf: 'flex-start',
    marginTop: 10,
  },
  promoBtnText: {
    color: '#FFFFFF',
    fontSize: 10.5,
    fontWeight: '900',
  },
  promoVisual: {
    width: 90,
    alignItems: 'center',
    justifyContent: 'center',
    position: 'relative',
  },
  discountBadge: {
    position: 'absolute',
    top: -6,
    right: 4,
    backgroundColor: colors.primaryDark,
    borderRadius: 18,
    width: 36,
    height: 36,
    alignItems: 'center',
    justifyContent: 'center',
    zIndex: 2,
  },
  discountBadgeTop: {
    color: '#FFFFFF',
    fontSize: 9,
    fontWeight: '900',
  },
  discountBadgeBottom: {
    color: '#FFFFFF',
    fontSize: 7,
    fontWeight: '800',
  },

  // AI FAB
  aiFab: {
    position: 'absolute',
    bottom: 16,
    right: 16,
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 8,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.35,
    shadowRadius: 8,
    borderWidth: 2,
    borderColor: '#FFFFFF',
  },

  // Toast
  toast: {
    position: 'absolute',
    bottom: 30,
    left: 40,
    right: 40,
    backgroundColor: colors.primaryDark,
    borderRadius: 16,
    paddingVertical: 12,
    paddingHorizontal: 16,
    alignItems: 'center',
    elevation: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
  },
  toastText: {
    color: '#FFFFFF',
    fontSize: 13,
    fontWeight: '800',
  },

  // Modal Overlays
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.45)',
    justifyContent: 'flex-end',
  },
  filterModalCard: {
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 20,
    paddingBottom: Platform.OS === 'ios' ? 40 : 24,
  },
  filterModalHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 16,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    paddingBottom: 12,
  },
  filterModalTitle: {
    fontSize: 17,
    fontWeight: '900',
    color: colors.textDark,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  filterModalRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 8,
  },
  filterSectionTitle: {
    fontSize: 14,
    fontWeight: '800',
    color: colors.textDark,
  },
  filterSectionSub: {
    fontSize: 11.5,
    color: colors.textMuted,
    marginTop: 2,
  },
  sortOptionGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
    marginBottom: 20,
  },
  sortChip: {
    paddingHorizontal: 14,
    paddingVertical: 9,
    borderRadius: 12,
    backgroundColor: colors.surfaceAlt,
    borderWidth: 1,
    borderColor: colors.border,
  },
  sortChipActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primary,
  },
  sortChipText: {
    fontSize: 12,
    fontWeight: '700',
    color: colors.textDark,
  },
  sortChipTextActive: {
    color: '#FFFFFF',
    fontWeight: '800',
  },
  filterModalActions: {
    flexDirection: 'row',
    gap: 12,
    marginTop: 6,
  },
  modalResetBtn: {
    flex: 1,
    paddingVertical: 13,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
    alignItems: 'center',
    justifyContent: 'center',
  },
  modalResetText: {
    fontSize: 13.5,
    fontWeight: '800',
    color: colors.textDark,
  },
  modalApplyBtn: {
    flex: 2,
    backgroundColor: colors.primary,
    paddingVertical: 13,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 3,
  },
  modalApplyText: {
    fontSize: 13.5,
    fontWeight: '800',
    color: '#FFFFFF',
  },

  // Side Drawer
  drawerOverlay: {
    flex: 1,
    flexDirection: 'row',
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
  },
  drawerBackdrop: {
    flex: 1,
  },
  drawerContainer: {
    width: 290,
    backgroundColor: '#FFFFFF',
    paddingTop: Platform.OS === 'android' ? 44 : 20,
    paddingHorizontal: 20,
    paddingBottom: 24,
    elevation: 16,
  },
  drawerHeader: {
    paddingBottom: 16,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    marginBottom: 12,
  },
  drawerBrandRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  drawerBrandTitle: {
    fontSize: 22,
    fontWeight: '900',
    color: colors.primaryDark,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  drawerBrandSub: {
    fontSize: 9,
    fontWeight: '800',
    color: colors.textMuted,
    letterSpacing: 2,
    marginTop: 2,
  },
  drawerGreeting: {
    fontSize: 13,
    fontWeight: '700',
    color: colors.textDark,
    marginTop: 10,
  },
  drawerNavList: {
    flex: 1,
  },
  drawerItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 13,
    borderBottomWidth: 1,
    borderBottomColor: '#F7F3EE',
  },
  drawerItemText: {
    fontSize: 13.5,
    fontWeight: '700',
    color: colors.textDark,
  },
  drawerCloseBtn: {
    marginTop: 10,
    paddingVertical: 12,
    borderRadius: 14,
    backgroundColor: colors.surfaceAlt,
    alignItems: 'center',
    justifyContent: 'center',
  },
  drawerCloseText: {
    fontSize: 12.5,
    fontWeight: '800',
    color: colors.textMuted,
  },
});
