import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  ActivityIndicator,
  ScrollView,
  StyleSheet,
  Alert,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { checkout, getUserAddresses } from '../api/ApiClient';
import RazorpayPaymentModal from '../components/RazorpayPaymentModal';
import { colors } from '../theme/colors';

export default function CheckoutScreen({ navigation }) {
  const { user } = useAuth();
  const { getCartItems, getTotal, clear } = useCart();
  const [paymentMethod, setPaymentMethod] = useState('COD');
  const [notes, setNotes] = useState('');
  const [loading, setLoading] = useState(false);

  // Delivery Address State
  const [addresses, setAddresses] = useState([]);
  const [selectedAddressId, setSelectedAddressId] = useState(null);

  // Real Razorpay Payment Gateway State
  const [showRazorpayModal, setShowRazorpayModal] = useState(false);

  const totalAmount = Math.round(getTotal());

  useEffect(() => {
    if (user?.id) {
      getUserAddresses(user.id)
        .then((res) => {
          if (res.data?.success && res.data.data?.length > 0) {
            setAddresses(res.data.data);
            setSelectedAddressId(res.data.data[0].id);
          }
        })
        .catch(() => {});
    }
  }, [user?.id]);

  const handlePlaceOrderClick = () => {
    if (!user) {
      Alert.alert('Login Required', 'Please login to place an order.');
      navigation.navigate('Login');
      return;
    }

    const cartItems = getCartItems();
    if (cartItems.length === 0) {
      Alert.alert('Empty Cart', 'Your cart is empty.');
      return;
    }

    if (paymentMethod === 'Razorpay') {
      setShowRazorpayModal(true);
    } else {
      executeCheckoutSubmit('COD', null);
    }
  };

  const handleRazorpaySuccess = (paymentId) => {
    setShowRazorpayModal(false);
    executeCheckoutSubmit('Razorpay', paymentId);
  };

  const handleRazorpayCancel = () => {
    setShowRazorpayModal(false);
  };

  const executeCheckoutSubmit = async (method, razorpayPaymentId) => {
    setLoading(true);
    try {
      const cartItems = getCartItems();
      const orderItems = cartItems.map((ci) => ({
        menu_item_id: ci.menuItem.id,
        item_name: ci.menuItem.name,
        price: ci.menuItem.price,
        quantity: ci.quantity,
        subtotal: ci.menuItem.price * ci.quantity,
      }));

      const requestData = {
        user_id: user.id,
        address_id: selectedAddressId || null,
        payment_method: method,
        razorpay_payment_id: razorpayPaymentId,
        notes: notes.trim() || null,
        items: orderItems,
      };

      const res = await checkout(requestData);

      if (res.data?.success) {
        clear();
        Alert.alert(
          '🎉 Order Placed Successfully!',
          `Your order #${res.data.data.order_number || res.data.data.id} has been received and sent to the barista & kitchen!`,
          [
            {
              text: 'Track Order',
              onPress: () =>
                navigation.reset({
                  index: 0,
                  routes: [
                    { name: 'MainTabs' },
                    { name: 'OrderTracking', params: { orderId: res.data.data.id } },
                  ],
                }),
            },
          ]
        );
      } else {
        Alert.alert('Order Failed', res.data?.message || 'Could not place order. Try again.');
      }
    } catch (err) {
      const msg = err.response?.data?.message || err.message || 'Network request failed';
      Alert.alert('Connection Error', msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      {/* Header Bar */}
      <View style={styles.headerBar}>
        <Pressable onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color={colors.primaryDark} />
        </Pressable>
        <Text style={styles.headerTitle}>Order Checkout</Text>
        <View style={{ width: 36 }} />
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
      >
        {/* Delivery Address Section */}
        <View style={styles.section}>
          <View style={styles.sectionHeaderRow}>
            <Ionicons name="location-outline" size={18} color={colors.primary} style={{ marginRight: 6 }} />
            <Text style={styles.sectionTitle}>Delivery Address</Text>
          </View>
          {addresses.length > 0 ? (
            <View style={styles.addressContainer}>
              {addresses.map((addr) => {
                const isSelected = selectedAddressId === addr.id;
                return (
                  <Pressable
                    key={addr.id}
                    style={[styles.addressCard, isSelected && styles.addressCardSelected]}
                    onPress={() => setSelectedAddressId(addr.id)}
                  >
                    <Ionicons
                      name={isSelected ? 'radio-button-on' : 'radio-button-off'}
                      size={20}
                      color={isSelected ? colors.primary : colors.textMuted}
                      style={{ marginRight: 10 }}
                    />
                    <View style={{ flex: 1 }}>
                      <Text style={styles.addressTitle}>{addr.title}</Text>
                      <Text style={styles.addressLine} numberOfLines={2}>{addr.address_line}</Text>
                      {(addr.city || addr.pincode) && (
                        <Text style={styles.addressCity}>
                          {[addr.city, addr.pincode].filter(Boolean).join(' - ')}
                        </Text>
                      )}
                    </View>
                  </Pressable>
                );
              })}
            </View>
          ) : (
            <View style={styles.noAddressCard}>
              <Ionicons name="information-circle-outline" size={18} color={colors.textMuted} style={{ marginRight: 8 }} />
              <Text style={styles.noAddressText}>
                No saved address. Deliver to default location or add in Profile.
              </Text>
            </View>
          )}
        </View>

        {/* Order Summary Card */}
        <View style={styles.section}>
          <View style={styles.sectionHeaderRow}>
            <Ionicons name="cart-outline" size={18} color={colors.primary} style={{ marginRight: 6 }} />
            <Text style={styles.sectionTitle}>Items in Order</Text>
          </View>
          <View style={styles.summaryCard}>
            {getCartItems().map((ci) => (
              <View key={ci.menuItem.id} style={styles.itemRow}>
                <Text style={styles.itemName} numberOfLines={1}>
                  {ci.quantity}x {ci.menuItem.name}
                </Text>
                <Text style={styles.itemPrice}>
                  ₹{Math.round(ci.menuItem.price * ci.quantity)}
                </Text>
              </View>
            ))}
            <View style={styles.divider} />
            <View style={styles.itemRow}>
              <Text style={styles.totalLabel}>Grand Total</Text>
              <Text style={styles.totalValue}>₹{totalAmount}</Text>
            </View>
          </View>
        </View>

        {/* Payment Method Selector */}
        <View style={styles.section}>
          <View style={styles.sectionHeaderRow}>
            <Ionicons name="card-outline" size={18} color={colors.primary} style={{ marginRight: 6 }} />
            <Text style={styles.sectionTitle}>Choose Payment Method</Text>
          </View>
          <View style={styles.paymentOptions}>
            <Pressable
              style={[
                styles.optionCard,
                paymentMethod === 'COD' && styles.optionCardSelected,
              ]}
              onPress={() => setPaymentMethod('COD')}
            >
              <Ionicons name="cash-outline" size={24} color={colors.primary} style={{ marginRight: 12 }} />
              <View style={styles.optionInfo}>
                <Text style={styles.optionTitle}>Cash on Delivery</Text>
                <Text style={styles.optionSubtitle}>Pay in cash when your order arrives</Text>
              </View>
              {paymentMethod === 'COD' && <Ionicons name="checkmark-circle" size={20} color={colors.primary} />}
            </Pressable>

            <Pressable
              style={[
                styles.optionCard,
                paymentMethod === 'Razorpay' && styles.optionCardSelected,
              ]}
              onPress={() => setPaymentMethod('Razorpay')}
            >
              <Ionicons name="card-outline" size={24} color={colors.primary} style={{ marginRight: 12 }} />
              <View style={styles.optionInfo}>
                <Text style={styles.optionTitle}>Online Payment (Razorpay)</Text>
                <Text style={styles.optionSubtitle}>Real UPI, Google Pay, Cards, NetBanking</Text>
              </View>
              {paymentMethod === 'Razorpay' && <Ionicons name="checkmark-circle" size={20} color={colors.primary} />}
            </Pressable>
          </View>
        </View>

        {/* Delivery Note Input */}
        <View style={styles.section}>
          <View style={styles.sectionHeaderRow}>
            <Ionicons name="document-text-outline" size={18} color={colors.primary} style={{ marginRight: 6 }} />
            <Text style={styles.sectionTitle}>Delivery Instructions (Optional)</Text>
          </View>
          <TextInput
            style={styles.notesInput}
            value={notes}
            onChangeText={setNotes}
            multiline
            numberOfLines={3}
            placeholder="e.g. Leave with security, extra napkins..."
            placeholderTextColor={colors.textMuted}
          />
        </View>
      </ScrollView>

      {/* Bottom Bar Action Button */}
      <View style={styles.bottomBar}>
        <View style={styles.bottomTotal}>
          <Text style={styles.bottomTotalLabel}>Total to Pay</Text>
          <Text style={styles.bottomTotalVal}>₹{totalAmount}</Text>
        </View>
        <Pressable
          style={[styles.placeOrderButton, loading && styles.buttonDisabled]}
          onPress={handlePlaceOrderClick}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#FFF" />
          ) : (
            <Text style={styles.placeOrderText}>
              {paymentMethod === 'Razorpay' ? 'Proceed to Online Payment' : 'Place Order (COD)'}
            </Text>
          )}
        </Pressable>
      </View>

      {/* Real Razorpay Online Payment Gateway Modal */}
      <RazorpayPaymentModal
        visible={showRazorpayModal}
        amount={totalAmount}
        description="Sip N Bite Café Food Order"
        userInfo={{
          name: user?.name,
          email: user?.email,
          phone: user?.phone,
        }}
        onSuccess={handleRazorpaySuccess}
        onCancel={handleRazorpayCancel}
        onClose={() => setShowRazorpayModal(false)}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  headerBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? ((StatusBar.currentHeight || 28) + 8) : 12,
    paddingBottom: 14,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  backButton: { width: 36, height: 36, justifyContent: 'center' },
  headerTitle: {
    fontSize: 18,
    fontWeight: '900',
    color: colors.primaryDark,
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
  },
  scrollContent: { padding: 16, paddingBottom: 120 },
  section: { marginBottom: 20 },
  sectionHeaderRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 10 },
  sectionTitle: { fontSize: 14.5, fontWeight: '800', color: colors.textDark },
  addressContainer: { gap: 10 },
  addressCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 14,
    padding: 12,
  },
  addressCardSelected: {
    borderColor: colors.primary,
    backgroundColor: colors.surfaceAlt,
  },
  addressTitle: { fontSize: 13, fontWeight: '800', color: colors.textDark },
  addressLine: { fontSize: 12, color: colors.textMuted, marginTop: 2 },
  addressCity: { fontSize: 11, color: colors.textPlaceholder, marginTop: 1 },
  noAddressCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 14,
    padding: 12,
  },
  noAddressText: { fontSize: 12, color: colors.textMuted, flex: 1 },
  summaryCard: { backgroundColor: '#FFFFFF', borderRadius: 16, padding: 16, borderColor: colors.border, borderWidth: 1, elevation: 2 },
  itemRow: { flexDirection: 'row', justifyContent: 'space-between', marginVertical: 4 },
  itemName: { fontSize: 13.5, color: colors.textMuted, flex: 1, fontWeight: '600' },
  itemPrice: { fontSize: 13.5, fontWeight: '800', color: colors.textDark },
  divider: { height: 1, backgroundColor: colors.border, marginVertical: 10 },
  totalLabel: { fontSize: 15, fontWeight: '900', color: colors.textDark },
  totalValue: { fontSize: 17, fontWeight: '900', color: colors.primaryDark },
  paymentOptions: { gap: 10 },
  optionCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 16,
    padding: 14,
  },
  optionCardSelected: { borderColor: colors.primary, backgroundColor: colors.surfaceAlt },
  optionInfo: { flex: 1 },
  optionTitle: { fontSize: 14, fontWeight: '800', color: colors.textDark },
  optionSubtitle: { fontSize: 11.5, color: colors.textMuted, marginTop: 2, fontWeight: '500' },
  notesInput: {
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: 16,
    padding: 14,
    fontSize: 13.5,
    color: colors.textDark,
    textAlignVertical: 'top',
    height: 80,
  },
  bottomBar: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: colors.border,
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    elevation: 8,
  },
  bottomTotalLabel: { fontSize: 11, color: colors.textMuted, fontWeight: '600' },
  bottomTotalVal: { fontSize: 20, fontWeight: '900', color: colors.primaryDark },
  placeOrderButton: {
    backgroundColor: colors.primary,
    borderRadius: 16,
    paddingHorizontal: 20,
    paddingVertical: 14,
    elevation: 3,
  },
  buttonDisabled: { opacity: 0.6 },
  placeOrderText: { color: '#FFFFFF', fontSize: 13.5, fontWeight: '800' },
});
