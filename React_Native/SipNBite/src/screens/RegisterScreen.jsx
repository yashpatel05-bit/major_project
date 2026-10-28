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
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { colors } from '../theme/colors';

export default function RegisterScreen({ navigation }) {
  const { register } = useAuth();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [address, setAddress] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  // Validation errors
  const [errors, setErrors] = useState({});

  const handleRegister = async () => {
    const newErrors = {};

    if (!name.trim()) {
      newErrors.name = 'Please enter this field: Full Name';
    }

    if (!email.trim()) {
      newErrors.email = 'Please enter this field: Email Address';
    } else if (!email.includes('@')) {
      newErrors.email = 'Please enter a valid email address';
    }

    if (!password.trim()) {
      newErrors.password = 'Please enter this field: Password';
    } else if (password.trim().length < 6) {
      newErrors.password = 'Password must be at least 6 characters';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      const firstError = newErrors.name || newErrors.email || newErrors.password;
      Alert.alert('Please Fill Required Field', firstError);
      return;
    }

    setErrors({});
    setLoading(true);
    try {
      const result = await register(
        name.trim(),
        email.trim(),
        password.trim(),
        phone.trim() || null,
        address.trim() || null,
      );
      if (!result.success) {
        Alert.alert('Registration Failed', result.message);
      }
    } catch (err) {
      Alert.alert('Error', err.message || 'Unable to connect to server');
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
        showsVerticalScrollIndicator={false}
      >
        {/* Top bar */}
        <View style={styles.topBar}>
          <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
            <Ionicons name="arrow-back" size={22} color="#FFFFFF" style={{ marginRight: 4 }} />
            <Text style={styles.backText}>Back</Text>
          </Pressable>
        </View>

        <View style={styles.heroMini}>
          <Text style={styles.brand}>
            Create <Text style={styles.brandAccent}>Account</Text>
          </Text>
          <Text style={styles.tagline}>Join the Sip & Bite food & dining family</Text>
        </View>

        {/* Form Card */}
        <View style={styles.formCard}>
          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>
              Full Name <Text style={styles.requiredStar}>*</Text>
            </Text>
            <TextInput
              style={[styles.input, errors.name && styles.inputError]}
              value={name}
              onChangeText={(val) => {
                setName(val);
                if (errors.name) setErrors((prev) => ({ ...prev, name: null }));
              }}
            />
            {errors.name ? <Text style={styles.errorText}>{errors.name}</Text> : null}
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>
              Email Address <Text style={styles.requiredStar}>*</Text>
            </Text>
            <TextInput
              style={[styles.input, errors.email && styles.inputError]}
              value={email}
              onChangeText={(val) => {
                setEmail(val);
                if (errors.email) setErrors((prev) => ({ ...prev, email: null }));
              }}
              keyboardType="email-address"
              autoCapitalize="none"
            />
            {errors.email ? <Text style={styles.errorText}>{errors.email}</Text> : null}
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>Phone Number</Text>
            <TextInput
              style={styles.input}
              value={phone}
              onChangeText={setPhone}
              keyboardType="phone-pad"
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>Delivery Address</Text>
            <TextInput
              style={[styles.input, styles.textArea]}
              value={address}
              onChangeText={setAddress}
              multiline
              numberOfLines={2}
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.inputLabel}>
              Password <Text style={styles.requiredStar}>*</Text>
            </Text>
            <TextInput
              style={[styles.input, errors.password && styles.inputError]}
              value={password}
              onChangeText={(val) => {
                setPassword(val);
                if (errors.password) setErrors((prev) => ({ ...prev, password: null }));
              }}
              secureTextEntry
            />
            {errors.password ? <Text style={styles.errorText}>{errors.password}</Text> : null}
          </View>

          <Pressable
            style={[styles.registerButton, loading && styles.buttonDisabled]}
            onPress={handleRegister}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#FFF8F0" />
            ) : (
              <Text style={styles.registerButtonText}>Create Account</Text>
            )}
          </Pressable>

          <View style={styles.loginRow}>
            <Text style={styles.loginHint}>Already have an account? </Text>
            <Pressable onPress={() => navigation.goBack()}>
              <Text style={styles.loginLink}>Sign In</Text>
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
  },
  topBar: {
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? 16 : 10,
    paddingBottom: 10,
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 6,
  },
  backText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '700',
  },
  heroMini: {
    alignItems: 'center',
    paddingBottom: 22,
    paddingTop: 8,
  },
  brand: {
    color: '#FFF8F0',
    fontSize: 28,
    fontWeight: '900',
    letterSpacing: 0.3,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  brandAccent: {
    color: colors.accent,
  },
  tagline: {
    color: '#D4C4B5',
    fontSize: 12.5,
    marginTop: 4,
    fontWeight: '500',
  },
  formCard: {
    backgroundColor: colors.background,
    borderTopLeftRadius: 30,
    borderTopRightRadius: 30,
    flex: 1,
    paddingHorizontal: 24,
    paddingTop: 28,
    paddingBottom: 36,
  },
  inputGroup: {
    marginBottom: 14,
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
  textArea: {
    height: 64,
    textAlignVertical: 'top',
    paddingTop: 10,
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
  registerButton: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 14,
    height: 50,
    justifyContent: 'center',
    marginTop: 14,
    elevation: 2,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  registerButtonText: {
    color: '#FFFFFF',
    fontSize: 15,
    fontWeight: '800',
  },
  loginRow: {
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 20,
  },
  loginHint: {
    color: colors.textMuted,
    fontSize: 13,
  },
  loginLink: {
    color: colors.primary,
    fontSize: 13,
    fontWeight: '900',
  },
});
