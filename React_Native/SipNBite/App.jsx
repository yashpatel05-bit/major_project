import React from 'react';
import { ActivityIndicator, View, Text, Pressable, StyleSheet, LogBox, Platform } from 'react-native';
import { SafeAreaProvider, useSafeAreaInsets } from 'react-native-safe-area-context';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';

LogBox.ignoreLogs(['Cannot connect to Expo CLI']);

import { AuthProvider, useAuth } from './src/context/AuthContext';
import { CartProvider } from './src/context/CartContext';
import { colors } from './src/theme/colors';

// Auth Screens
import LoginScreen from './src/screens/LoginScreen';
import RegisterScreen from './src/screens/RegisterScreen';

// Main Screens
import HomeScreen from './src/screens/HomeScreen';
import MenuScreen from './src/screens/MenuScreen';
import MenuItemDetailScreen from './src/screens/MenuItemDetailScreen';
import CartScreen from './src/screens/CartScreen';
import CheckoutScreen from './src/screens/CheckoutScreen';
import OrderHistoryScreen from './src/screens/OrderHistoryScreen';
import OrderTrackingScreen from './src/screens/OrderTrackingScreen';
import TableBookingScreen from './src/screens/TableBookingScreen';
import TableBookingHistoryScreen from './src/screens/TableBookingHistoryScreen';
import ProfileScreen from './src/screens/ProfileScreen';
import FeedbackScreen from './src/screens/FeedbackScreen';
import AddressScreen from './src/screens/AddressScreen';
import AboutScreen from './src/screens/AboutScreen';
import ChatBotScreen from './src/screens/ChatBotScreen';

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

// ─── 5-Item Bottom Navigation Bar (Matching Coffee House UI) ──
function CustomTabBar({ state, descriptors, navigation }) {
  const insets = useSafeAreaInsets();
  const bottomInset = Math.max(insets.bottom, Platform.OS === 'android' ? 12 : 16);

  const tabConfig = {
    HomeTab: {
      label: 'Home',
      activeIcon: 'home',
      inactiveIcon: 'home-outline',
      isCenter: false,
    },
    MenuTab: {
      label: 'Menu',
      activeIcon: 'book',
      inactiveIcon: 'book-outline',
      isCenter: false,
    },
    OrdersTab: {
      label: 'Orders',
      activeIcon: 'restaurant',
      inactiveIcon: 'restaurant-outline',
      isCenter: true, // Elevated center circular button
    },
    TableBookingTab: {
      label: 'Table Booking',
      activeIcon: 'calendar',
      inactiveIcon: 'calendar-outline',
      isCenter: false,
    },
    ProfileTab: {
      label: 'Profile',
      activeIcon: 'person',
      inactiveIcon: 'person-outline',
      isCenter: false,
    },
  };

  return (
    <View style={[tabStyles.wrapper, { paddingBottom: bottomInset }]}>
      <View style={tabStyles.container}>
        {state.routes.map((route, index) => {
          const isFocused = state.index === index;
          const conf = tabConfig[route.name] || {
            label: route.name,
            activeIcon: 'square',
            inactiveIcon: 'square-outline',
            isCenter: false,
          };

          const onPress = () => {
            const event = navigation.emit({
              type: 'tabPress',
              target: route.key,
              canPreventDefault: true,
            });

            if (!isFocused && !event.defaultPrevented) {
              navigation.navigate(route.name);
            }
          };

          if (conf.isCenter) {
            // Highlighted Center Button for Orders
            return (
              <Pressable
                key={route.key}
                onPress={onPress}
                style={tabStyles.centerTabItem}
                accessibilityRole="button"
                accessibilityState={isFocused ? { selected: true } : {}}
              >
                <View style={[tabStyles.centerCircle, isFocused && tabStyles.centerCircleActive]}>
                  <Ionicons name="receipt" size={22} color="#FFFFFF" />
                </View>
                <Text style={[tabStyles.centerLabel, isFocused && tabStyles.centerLabelActive]}>
                  {conf.label}
                </Text>
              </Pressable>
            );
          }

          return (
            <Pressable
              key={route.key}
              onPress={onPress}
              style={tabStyles.tabItem}
              accessibilityRole="button"
              accessibilityState={isFocused ? { selected: true } : {}}
            >
              <Ionicons
                name={isFocused ? conf.activeIcon : conf.inactiveIcon}
                size={22}
                color={isFocused ? colors.primaryDark : '#8C7D73'}
              />
              <Text
                style={[
                  tabStyles.tabLabel,
                  isFocused && tabStyles.tabLabelActive,
                ]}
                numberOfLines={1}
              >
                {conf.label}
              </Text>
            </Pressable>
          );
        })}
      </View>
    </View>
  );
}

// ─── Bottom Tabs Navigator (Exactly 5 Options) ───────────────
function MainTabs() {
  return (
    <Tab.Navigator
      tabBar={(props) => <CustomTabBar {...props} />}
      screenOptions={{ headerShown: false }}
    >
      <Tab.Screen name="HomeTab" component={HomeScreen} />
      <Tab.Screen name="MenuTab" component={MenuScreen} />
      <Tab.Screen name="OrdersTab" component={OrderHistoryScreen} />
      <Tab.Screen name="TableBookingTab" component={TableBookingScreen} />
      <Tab.Screen name="ProfileTab" component={ProfileScreen} />
    </Tab.Navigator>
  );
}

// ─── Auth Stack ─────────────────────────────────────────────
function AuthStack() {
  return (
    <Stack.Navigator
      screenOptions={{ headerShown: false, animation: 'slide_from_right' }}
    >
      <Stack.Screen name="Login" component={LoginScreen} />
      <Stack.Screen name="Register" component={RegisterScreen} />
    </Stack.Navigator>
  );
}

// ─── App Stack (Tabs + Subscreens) ──────────────────────────
function AppStack() {
  return (
    <Stack.Navigator
      screenOptions={{ headerShown: false, animation: 'slide_from_right' }}
    >
      <Stack.Screen name="MainTabs" component={MainTabs} />
      <Stack.Screen name="MenuItemDetail" component={MenuItemDetailScreen} />
      <Stack.Screen name="Cart" component={CartScreen} />
      <Stack.Screen name="Checkout" component={CheckoutScreen} />
      <Stack.Screen name="OrderHistory" component={OrderHistoryScreen} />
      <Stack.Screen name="OrderTracking" component={OrderTrackingScreen} />
      <Stack.Screen name="TableBooking" component={TableBookingScreen} />
      <Stack.Screen name="TableBookingHistory" component={TableBookingHistoryScreen} />
      <Stack.Screen name="Profile" component={ProfileScreen} />
      <Stack.Screen name="Feedback" component={FeedbackScreen} />
      <Stack.Screen name="Address" component={AddressScreen} />
      <Stack.Screen name="About" component={AboutScreen} />
      <Stack.Screen
        name="ChatBot"
        component={ChatBotScreen}
        options={{ presentation: 'modal', animation: 'slide_from_bottom' }}
      />
    </Stack.Navigator>
  );
}

// ─── Root Navigator ─────────────────────────────────────────
function RootNavigator() {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <Ionicons name="restaurant" size={50} color={colors.primary} />
        <ActivityIndicator size="large" color={colors.primary} style={{ marginTop: 16 }} />
        <Text style={styles.loadingText}>Preparing Sip & Bite...</Text>
      </View>
    );
  }

  return (
    <NavigationContainer>
      {user ? <AppStack /> : <AuthStack />}
    </NavigationContainer>
  );
}

// ─── App Entry Point ────────────────────────────────────────
export default function App() {
  return (
    <SafeAreaProvider style={{ flex: 1 }}>
      <AuthProvider>
        <CartProvider>
          <RootNavigator />
        </CartProvider>
      </AuthProvider>
    </SafeAreaProvider>
  );
}

// ─── Tab Bar Styles ─────────────────────────────────────────
const tabStyles = StyleSheet.create({
  wrapper: {
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: colors.border,
  },
  container: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-around',
    height: 56,
    paddingTop: 4,
    backgroundColor: '#FFFFFF',
  },
  tabItem: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  tabLabel: {
    color: '#8C7D73',
    fontSize: 10.5,
    fontWeight: '600',
    marginTop: 3,
  },
  tabLabelActive: {
    color: colors.primaryDark,
    fontWeight: '800',
  },
  centerTabItem: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    top: -14,
  },
  centerCircle: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 6,
    shadowColor: colors.primaryDark,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.35,
    shadowRadius: 6,
    borderWidth: 3,
    borderColor: '#FFFFFF',
  },
  centerCircleActive: {
    backgroundColor: colors.primaryDark,
    transform: [{ scale: 1.05 }],
  },
  centerLabel: {
    color: '#8C7D73',
    fontSize: 10.5,
    fontWeight: '700',
    marginTop: 2,
  },
  centerLabelActive: {
    color: colors.primaryDark,
    fontWeight: '800',
  },
});

// ─── Global Styles ──────────────────────────────────────────
const styles = StyleSheet.create({
  loadingContainer: {
    alignItems: 'center',
    backgroundColor: colors.background,
    flex: 1,
    justifyContent: 'center',
  },
  loadingText: {
    color: colors.textMuted,
    fontSize: 14,
    fontWeight: '700',
    marginTop: 14,
  },
});
