import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  ScrollView,
  ActivityIndicator,
  StyleSheet,
  Alert,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { submitFeedback } from '../api/ApiClient';
import { colors } from '../theme/colors';

export default function FeedbackScreen({ route, navigation }) {
  const { user } = useAuth();
  const orderId = route.params?.orderId || null;
  const [rating, setRating] = useState(0);
  const [comment, setComment] = useState('');
  const [loading, setLoading] = useState(false);
  const [ratingError, setRatingError] = useState(false);

  const handleSubmit = async () => {
    if (!user) {
      Alert.alert('Login Required', 'Please login to submit feedback.');
      return;
    }
    if (rating === 0) {
      setRatingError(true);
      Alert.alert('Please Fill Required Field', 'Please select a star rating for your experience.');
      return;
    }
    setRatingError(false);

    setLoading(true);
    try {
      const res = await submitFeedback({
        user_id: user.id,
        order_id: orderId,
        rating,
        comment: comment.trim() || null,
      });
      if (res.data?.success) {
        Alert.alert('Thank You!', 'Your feedback has been submitted.', [
          { text: 'OK', onPress: () => navigation.goBack() },
        ]);
      } else {
        Alert.alert('Failed', res.data?.message || 'Could not submit feedback');
      }
    } catch (err) {
      Alert.alert('Error', err.message || 'Network error');
    } finally {
      setLoading(false);
    }
  };

  const stars = [1, 2, 3, 4, 5];
  const ratingLabels = ['', 'Needs Improvement', 'Fair Experience', 'Good Food & Drinks', 'Great Service', 'Absolute Perfection!'];

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>Feedback</Text>
        <View style={{ width: 36 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
        {/* Hero */}
        <View style={styles.heroCard}>
          <Ionicons name="chatbubble-ellipses-outline" size={40} color={colors.accent} style={{ marginBottom: 8 }} />
          <Text style={styles.heroTitle}>We would love your thoughts!</Text>
          <Text style={styles.heroSubtitle}>
            Help us brew an even better experience at Sip & Bite
          </Text>
        </View>

        {orderId && (
          <View style={styles.orderRef}>
            <Text style={styles.orderRefText}>For Order #{orderId}</Text>
          </View>
        )}

        {/* Star Rating */}
        <View style={[styles.ratingSection, ratingError && styles.ratingSectionError]}>
          <Text style={styles.sectionLabel}>How was your experience? *</Text>
          <View style={styles.starsRow}>
            {stars.map((s) => (
              <Pressable
                key={s}
                onPress={() => {
                  setRating(s);
                  setRatingError(false);
                }}
                style={styles.starButton}
              >
                <Ionicons
                  name={s <= rating ? 'star' : 'star-outline'}
                  size={36}
                  color={s <= rating ? colors.accentGold : '#D8CBC0'}
                />
              </Pressable>
            ))}
          </View>
          {ratingError && (
            <Text style={styles.errorText}>Please select a star rating</Text>
          )}
          {rating > 0 && (
            <Text style={styles.ratingLabel}>{ratingLabels[rating]}</Text>
          )}
        </View>

        {/* Comment */}
        <View style={styles.commentSection}>
          <Text style={styles.sectionLabel}>Tell us more (optional)</Text>
          <TextInput
            style={styles.commentInput}
            multiline
            numberOfLines={5}
            value={comment}
            onChangeText={setComment}
            textAlignVertical="top"
          />
        </View>

        {/* Submit */}
        <Pressable
          style={[styles.submitButton, loading && styles.buttonDisabled]}
          onPress={handleSubmit}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#FFFFFF" />
          ) : (
            <Text style={styles.submitText}>Submit Feedback</Text>
          )}
        </Pressable>
      </ScrollView>
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

  scrollContent: { paddingHorizontal: 20, paddingTop: 16, paddingBottom: 40 },

  // Hero
  heroCard: {
    alignItems: 'center',
    backgroundColor: '#1E140F',
    borderRadius: 20,
    marginBottom: 20,
    paddingHorizontal: 20,
    paddingVertical: 24,
  },
  heroTitle: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: '800',
    textAlign: 'center',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  heroSubtitle: { color: '#D4C4B5', fontSize: 12, marginTop: 4, textAlign: 'center', fontWeight: '500' },

  orderRef: {
    backgroundColor: colors.surfaceAlt,
    borderRadius: 12,
    marginBottom: 20,
    padding: 12,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.border,
  },
  orderRefText: { color: colors.textDark, fontSize: 13, fontWeight: '700' },

  // Rating
  ratingSection: { alignItems: 'center', marginBottom: 24, padding: 12, borderRadius: 16 },
  ratingSectionError: {
    backgroundColor: '#FFF5F5',
    borderWidth: 1.5,
    borderColor: colors.error,
  },
  errorText: {
    color: colors.error,
    fontSize: 12,
    fontWeight: '700',
    marginTop: 6,
  },
  sectionLabel: { color: colors.textDark, fontSize: 14.5, fontWeight: '800', marginBottom: 14, alignSelf: 'flex-start' },
  starsRow: { flexDirection: 'row', gap: 10 },
  starButton: { padding: 4 },
  ratingLabel: { color: colors.primaryDark, fontSize: 13.5, fontWeight: '800', marginTop: 10 },

  // Comment
  commentSection: { marginBottom: 24 },
  commentInput: {
    backgroundColor: '#FFFFFF',
    borderColor: colors.border,
    borderRadius: 14,
    borderWidth: 1,
    color: colors.textDark,
    fontSize: 13.5,
    minHeight: 110,
    padding: 16,
  },

  // Submit
  submitButton: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 14,
    height: 48,
    justifyContent: 'center',
  },
  buttonDisabled: { opacity: 0.6 },
  submitText: { color: '#FFFFFF', fontSize: 14, fontWeight: '800' },
});
