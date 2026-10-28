import React from 'react';
import {
  View,
  Text,
  Pressable,
  ScrollView,
  StyleSheet,
  Linking,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';

export default function AboutScreen({ navigation }) {
  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>About Us</Text>
        <View style={{ width: 36 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Hero */}
        <View style={styles.heroCard}>
          <Ionicons name="restaurant" size={42} color={colors.accent} style={{ marginBottom: 8 }} />
          <Text style={styles.brand}>Sip & Bite</Text>
          <Text style={styles.tagline}>Delicious food & joyful sips in every bite</Text>
        </View>

        {/* Story */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Our Story</Text>
          <Text style={styles.cardText}>
            Welcome to Sip & Bite — your favorite cafe & eatery where every dish is crafted with love
            and every flavor delights. Founded with a passion for bringing people together over
            delicious gourmet food, artisan burgers, crispy snacks, and refreshing drinks, we are
            committed to freshness, quality, and an unforgettable dining experience.
          </Text>
        </View>

        {/* Stats */}
        <View style={styles.statsRow}>
          <View style={styles.statCard}>
            <Ionicons name="restaurant-outline" size={22} color={colors.primary} style={{ marginBottom: 4 }} />
            <Text style={styles.statValue}>15+</Text>
            <Text style={styles.statLabel}>Master Chefs</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="fast-food-outline" size={22} color={colors.primary} style={{ marginBottom: 4 }} />
            <Text style={styles.statValue}>50+</Text>
            <Text style={styles.statLabel}>Food & Drinks</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="star" size={22} color={colors.accentGold} style={{ marginBottom: 4 }} />
            <Text style={styles.statValue}>4.9</Text>
            <Text style={styles.statLabel}>Rating</Text>
          </View>
        </View>

        {/* What We Offer */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>What We Offer</Text>
          <FeatureRow iconName="fast-food-outline" text="Gourmet burgers, pizzas & crispy wraps" />
          <FeatureRow iconName="nutrition-outline" text="Handcrafted sandwiches & artisan snacks" />
          <FeatureRow iconName="cafe-outline" text="Refreshing shakes, mocktails & hot beverages" />
          <FeatureRow iconName="ice-cream-outline" text="Signature desserts & sweet treats" />
          <FeatureRow iconName="calendar-outline" text="Cozy ambience & easy table reservations" />
        </View>

        {/* Timings */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Hours & Location</Text>
          <View style={styles.timingRow}>
            <Text style={styles.timingDay}>Mon – Fri</Text>
            <Text style={styles.timingTime}>7:30 AM – 10:00 PM</Text>
          </View>
          <View style={styles.timingRow}>
            <Text style={styles.timingDay}>Sat – Sun</Text>
            <Text style={styles.timingTime}>8:00 AM – 11:00 PM</Text>
          </View>
          <View style={styles.locationRow}>
            <Ionicons name="location-outline" size={18} color={colors.primary} style={{ marginRight: 8, marginTop: 2 }} />
            <Text style={styles.locationText}>
              Sip & Bite Café & Eatery{'\n'}Downtown Boulevard
            </Text>
          </View>
        </View>

        {/* Contact */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Get in Touch</Text>
          <Pressable
            style={styles.contactRow}
            onPress={() => Linking.openURL('tel:+919876543210')}
          >
            <Ionicons name="call-outline" size={18} color={colors.primary} style={{ marginRight: 10 }} />
            <Text style={styles.contactText}>+91 98765 43210</Text>
          </Pressable>
          <Pressable
            style={styles.contactRow}
            onPress={() => Linking.openURL('mailto:hello@sipnbite.com')}
          >
            <Ionicons name="mail-outline" size={18} color={colors.primary} style={{ marginRight: 10 }} />
            <Text style={styles.contactText}>hello@sipnbite.com</Text>
          </Pressable>
        </View>

        <Text style={styles.versionText}>
          Sip & Bite Cafe & Food • Mobile Edition{'\n'}v2.0
        </Text>
      </ScrollView>
    </View>
  );
}

function FeatureRow({ iconName, text }) {
  return (
    <View style={styles.featureRow}>
      <Ionicons name={iconName} size={18} color={colors.primary} style={{ marginRight: 10 }} />
      <Text style={styles.featureText}>{text}</Text>
    </View>
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
  headerTitle: {
    color: colors.primaryDark,
    fontSize: 18,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },

  scrollContent: { paddingHorizontal: 16, paddingTop: 16, paddingBottom: 40 },

  // Hero
  heroCard: {
    alignItems: 'center',
    backgroundColor: '#1E140F',
    borderRadius: 22,
    marginBottom: 16,
    paddingHorizontal: 24,
    paddingVertical: 28,
  },
  brand: {
    color: '#FFFFFF',
    fontSize: 26,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  tagline: { color: '#D4C4B5', fontSize: 13, marginTop: 4, fontWeight: '500' },

  // Cards
  card: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    elevation: 1,
    marginBottom: 14,
    padding: 18,
    borderWidth: 1,
    borderColor: colors.border,
  },
  cardTitle: { color: colors.textDark, fontSize: 15, fontWeight: '800', marginBottom: 10 },
  cardText: { color: colors.textMuted, fontSize: 13.5, lineHeight: 21 },

  // Stats
  statsRow: { flexDirection: 'row', gap: 10, marginBottom: 14 },
  statCard: {
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    elevation: 1,
    flex: 1,
    paddingVertical: 16,
    borderWidth: 1,
    borderColor: colors.border,
  },
  statValue: { color: colors.primaryDark, fontSize: 20, fontWeight: '900' },
  statLabel: { color: colors.textMuted, fontSize: 10.5, fontWeight: '600', marginTop: 2 },

  // Features
  featureRow: { alignItems: 'center', flexDirection: 'row', marginBottom: 10 },
  featureText: { color: colors.textDark, flex: 1, fontSize: 13 },

  // Timings
  timingRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 6,
    paddingVertical: 2,
  },
  timingDay: { color: colors.textDark, fontSize: 13, fontWeight: '700' },
  timingTime: { color: colors.primary, fontSize: 13, fontWeight: '700' },
  locationRow: { alignItems: 'flex-start', flexDirection: 'row', marginTop: 8 },
  locationText: { color: colors.textMuted, flex: 1, fontSize: 13, lineHeight: 18 },

  // Contact
  contactRow: {
    alignItems: 'center',
    flexDirection: 'row',
    marginBottom: 8,
    paddingVertical: 4,
  },
  contactText: { color: colors.primaryDark, fontSize: 13.5, fontWeight: '700' },

  versionText: {
    color: colors.textPlaceholder,
    fontSize: 11.5,
    lineHeight: 18,
    marginTop: 10,
    textAlign: 'center',
  },
});
