import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  ActivityIndicator,
  StyleSheet,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import { useAuth } from '../context/AuthContext';
import { colors } from '../theme/colors';
import { Ionicons } from '@expo/vector-icons';

export default function LoginScreen({ navigation }) {
  const { login } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  // Validation errors
  const [errors, setErrors] = useState({});

  const handleLogin = async () => {
    const newErrors = {};

    if (!email.trim()) {
      newErrors.email = 'Please enter this field: Email Address';
    } else if (!email.includes('@')) {
      newErrors.email = 'Please enter a valid email address';
    }

    if (!password.trim()) {
      newErrors.password = 'Please enter this field: Password';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      const firstError = newErrors.email || newErrors.password;
      Alert.alert('Please Fill Required Field', firstError);
      return;
    }

    setErrors({});
    setLoading(true);
    try {
      const result = await login(email.trim(), password.trim());
      if (!result.success) {
        Alert.alert('Login Failed', result.message);
      }
    } catch (err) {
      Alert.alert('Connection Error', err.message || 'Unable to connect to server');
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="handled"
      >
        {/* Hero Area */}
        <View style={styles.heroSection}>
          <View style={styles.heroLogoCircle}>
            <Ionicons name="restaurant" size={32} color={colors.accentGold} />
          </View>
          <Text style={styles.brand}>
            Sip <Text style={styles.brandAccent}>&</Text> Bite
          </Text>
          <Text style={styles.tagline}>Gourmet Food & Artisanal Drinks</Text>
        </View>

        {/* Form Card */}
        <View style={styles.formCard}>
          <Text style={styles.formTitle}>Welcome Back</Text>
          <Text style={styles.formSubtitle}>Sign in to continue</Text>

          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>
              Email Address <Text style={styles.requiredStar}>*</Text>
            </Text>
            <TextInput
              style={[styles.input, errors.email && styles.inputError]}
              keyboardType="email-address"
              autoCapitalize="none"
              value={email}
              onChangeText={(val) => {
                setEmail(val);
                if (errors.email) setErrors((prev) => ({ ...prev, email: null }));
              }}
            />
            {errors.email ? (
              <Text style={styles.errorText}>{errors.email}</Text>
            ) : null}
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>
              Password <Text style={styles.requiredStar}>*</Text>
            </Text>
            <TextInput
              style={[styles.input, errors.password && styles.inputError]}
              secureTextEntry
              value={password}
              onChangeText={(val) => {
                setPassword(val);
                if (errors.password) setErrors((prev) => ({ ...prev, password: null }));
              }}
            />
            {errors.password ? (
              <Text style={styles.errorText}>{errors.password}</Text>
            ) : null}
          </View>

          <Pressable
            style={[styles.loginButton, loading && styles.buttonDisabled]}
            onPress={handleLogin}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#FFF8F0" />
            ) : (
              <Text style={styles.loginButtonText}>Sign In</Text>
            )}
          </Pressable>

          <View style={styles.registerRow}>
            <Text style={styles.registerHint}>Don't have an account? </Text>
            <Pressable onPress={() => navigation.navigate('Register')}>
              <Text style={styles.registerLink}>Sign Up</Text>
            </Pressable>
          </View>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#1E140F',
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
  },
  heroSection: {
    alignItems: 'center',
    paddingBottom: 28,
    paddingTop: 50,
  },
  heroLogoCircle: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: 'rgba(255,255,255,0.12)',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
    borderWidth: 1.5,
    borderColor: 'rgba(230,175,46,0.35)',
  },
  brand: {
    color: '#FFF8F0',
    fontSize: 32,
    fontWeight: '900',
    letterSpacing: 0.3,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  brandAccent: {
    color: colors.accent,
  },
  tagline: {
    color: '#D4C4B5',
    fontSize: 13,
    marginTop: 6,
    fontWeight: '500',
  },
  formCard: {
    backgroundColor: colors.background,
    borderTopLeftRadius: 30,
    borderTopRightRadius: 30,
    flex: 1,
    minHeight: 400,
    paddingHorizontal: 24,
    paddingTop: 32,
  },
  formTitle: {
    color: colors.textDark,
    fontSize: 24,
    fontWeight: '900',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  formSubtitle: {
    color: colors.textMuted,
    fontSize: 13,
    marginBottom: 24,
    marginTop: 4,
    fontWeight: '500',
  },
  inputGroup: {
    marginBottom: 16,
  },
  inputLabel: {
    color: colors.textDark,
    fontSize: 12.5,
    fontWeight: '800',
    marginBottom: 6,
  },
  requiredStar: {
    color: colors.error,
    fontWeight: '900',
  },
  input: {
    backgroundColor: '#FFFFFF',
    borderRadius: 14,
    borderWidth: 1,
    borderColor: colors.border,
    color: colors.textDark,
    fontSize: 14,
    height: 48,
    paddingHorizontal: 16,
    fontWeight: '600',
  },
  inputError: {
    borderColor: colors.error,
    borderWidth: 1.5,
    backgroundColor: '#FFF8F8',
  },
  errorText: {
    color: colors.error,
    fontSize: 11,
    fontWeight: '700',
    marginTop: 4,
    marginLeft: 2,
  },
  loginButton: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 14,
    height: 50,
    justifyContent: 'center',
    marginTop: 12,
    elevation: 2,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  loginButtonText: {
    color: '#FFFFFF',
    fontSize: 15,
    fontWeight: '800',
  },
  registerRow: {
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 22,
  },
  registerHint: {
    color: colors.textMuted,
    fontSize: 13,
  },
  registerLink: {
    color: colors.primary,
    fontSize: 13,
    fontWeight: '900',
  },
});
