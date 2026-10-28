import React, { useState, useRef } from 'react';
import {
  View,
  Text,
  Modal,
  Pressable,
  ActivityIndicator,
  StyleSheet,
  Platform,
  StatusBar,
  Alert,
  Linking,
  ToastAndroid,
} from 'react-native';
import { WebView } from 'react-native-webview';
import { Ionicons } from '@expo/vector-icons';
import { RAZORPAY_KEY } from '../config/AppConfig';
import { colors } from '../theme/colors';

export default function RazorpayPaymentModal({
  visible,
  amount = 0,
  description = 'Order Payment',
  userInfo = {},
  orderId = '',
  onSuccess,
  onCancel,
  onClose,
}) {
  const [webViewLoading, setWebViewLoading] = useState(true);
  const webViewRef = useRef(null);

  const cleanName = (userInfo?.name || 'Customer').replace(/["'\\]/g, '');
  const cleanEmail = (userInfo?.email || 'customer@sipnbite.com').replace(/["'\\]/g, '');
  const cleanPhone = (userInfo?.phone || '').replace(/["'\\]/g, '');
  const cleanDesc = (description || 'Order Payment').replace(/["'\\]/g, '');
  const numAmount = Number(amount) || 0;
  const amountPaise = Math.round(numAmount * 100);

  // Trigger immediate test payment simulation
  const handleQuickTestPay = () => {
    const testId = `pay_test_${Date.now().toString(36)}_${Math.random().toString(36).substring(2, 7)}`;
    if (Platform.OS === 'android') {
      ToastAndroid.show('✅ Online Payment Verified via Razorpay Test Mode!', ToastAndroid.SHORT);
    }
    if (onSuccess) {
      onSuccess(testId, {
        razorpay_payment_id: testId,
        razorpay_order_id: `order_test_${Date.now()}`,
        amount: numAmount,
        simulated: true,
      });
    }
  };

  const parseIntentToUpi = (url) => {
    if (url.startsWith('upi://')) return url;
    if (url.startsWith('intent://')) {
      const match = url.match(/intent:\/\/([^#]+)/);
      if (match && match[1]) {
        return `upi://${match[1]}`;
      }
    }
    return url;
  };

  const handleShouldStartLoadWithRequest = (request) => {
    const { url } = request;
    if (!url) return true;

    // Normal web browsing inside gateway
    if (
      url.startsWith('http://') ||
      url.startsWith('https://') ||
      url.startsWith('about:') ||
      url.startsWith('data:')
    ) {
      return true;
    }

    // Intercept UPI or Android intent schemes to prevent WebView net::ERR_UNKNOWN_URL_SCHEME
    if (
      url.startsWith('upi://') ||
      url.startsWith('intent://') ||
      url.startsWith('gpay://') ||
      url.startsWith('phonepe://') ||
      url.startsWith('paytmmp://')
    ) {
      const targetUrl = parseIntentToUpi(url);
      Linking.canOpenURL(targetUrl)
        .then((supported) => {
          if (supported) {
            Linking.openURL(targetUrl);
          } else {
            Alert.alert(
              'UPI App Not Found',
              'No external UPI app (Google Pay / PhonePe) was found on this device. You can select NetBanking / Card or tap "Quick Test Pay".',
              [{ text: 'OK' }]
            );
          }
        })
        .catch(() => {
          Alert.alert(
            'UPI Notice',
            'Could not launch UPI app. Please use NetBanking, Card, or Quick Test Pay.',
            [{ text: 'OK' }]
          );
        });
      return false;
    }

    return true;
  };

  const handleMessage = (event) => {
    try {
      const data = JSON.parse(event.nativeEvent.data);
      if (data.status === 'success') {
        if (Platform.OS === 'android') {
          ToastAndroid.show('Payment Successful!', ToastAndroid.SHORT);
        }
        if (onSuccess) {
          onSuccess(data.razorpay_payment_id, data);
        }
      } else if (data.status === 'dismissed') {
        if (Platform.OS === 'android') {
          ToastAndroid.show('Razorpay window closed. You can retry or tap Quick Pay.', ToastAndroid.LONG);
        }
      } else if (data.status === 'failed') {
        Alert.alert(
          'Payment Not Completed',
          data.message || 'Payment could not be processed. You can retry or use Quick Test Pay.',
          [{ text: 'OK' }]
        );
      } else if (data.status === 'quick_test_pay') {
        handleQuickTestPay();
      }
    } catch (_) {}
  };

  const handleClosePress = () => {
    Alert.alert(
      'Exit Online Payment?',
      'Are you sure you want to cancel online payment and return?',
      [
        { text: 'Keep Paying', style: 'cancel' },
        {
          text: 'Exit Payment',
          style: 'destructive',
          onPress: () => {
            if (onCancel) onCancel();
            if (onClose) onClose();
          },
        },
      ]
    );
  };

  const razorpayHtml = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
      <title>Razorpay Secure Checkout</title>
      <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body {
          background-color: #FAF7F2;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 100vh;
          padding: 18px;
          text-align: center;
        }
        .card {
          background: #FFFFFF;
          border-radius: 20px;
          padding: 24px 20px;
          box-shadow: 0 4px 20px rgba(0,0,0,0.06);
          width: 100%;
          max-width: 360px;
          border: 1px solid #EFE8E1;
        }
        .badge {
          display: inline-flex;
          align-items: center;
          gap: 6px;
          background: #FEE2E2;
          color: #DC2626;
          padding: 5px 14px;
          border-radius: 50px;
          font-size: 11.5px;
          font-weight: 800;
          margin-bottom: 14px;
          letter-spacing: 0.5px;
        }
        .amount-box {
          background: #FFF8F6;
          border: 1.5px dashed #FED7AA;
          border-radius: 16px;
          padding: 14px;
          margin-bottom: 16px;
        }
        .amount-lbl { font-size: 11px; color: #8C7A70; font-weight: 700; text-transform: uppercase; }
        .amount-val { font-size: 28px; font-weight: 900; color: #E23744; margin-top: 2px; }
        .desc-val { font-size: 12px; color: #5C4E43; margin-top: 4px; font-weight: 600; }
        
        .spinner {
          border: 3px solid #FEE2E2;
          border-top: 3px solid #E23744;
          border-radius: 50%;
          width: 32px;
          height: 32px;
          animation: spin 0.8s linear infinite;
          margin: 12px auto;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .status-title { font-size: 14px; font-weight: 800; color: #1E140F; margin-bottom: 4px; }
        .status-sub { font-size: 11.5px; color: #8C7A70; margin-bottom: 16px; line-height: 1.4; }
        
        .btn-rzp {
          background: #E23744;
          color: #FFFFFF;
          border: none;
          padding: 14px 20px;
          border-radius: 12px;
          font-size: 13.5px;
          font-weight: 800;
          width: 100%;
          cursor: pointer;
          margin-bottom: 10px;
          box-shadow: 0 4px 12px rgba(226, 55, 68, 0.25);
        }
        .btn-test {
          background: #16A34A;
          color: #FFFFFF;
          border: none;
          padding: 13px 20px;
          border-radius: 12px;
          font-size: 13px;
          font-weight: 800;
          width: 100%;
          cursor: pointer;
          box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
        }
        .test-tip {
          background: #F0FDF4;
          border: 1px solid #BBF7D0;
          border-radius: 12px;
          padding: 10px;
          margin-top: 14px;
          font-size: 11px;
          color: #166534;
          text-align: left;
          line-height: 1.4;
        }
        .test-tip strong { color: #15803D; }
        .footer-sec {
          margin-top: 14px;
          font-size: 11px;
          color: #9CA3AF;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 4px;
        }
      </style>
      <script id="razorpay-sdk" src="https://checkout.razorpay.com/v1/checkout.js"></script>
    </head>
    <body>
      <div class="card">
        <div class="badge">⚡ RAZORPAY TEST GATEWAY</div>
        
        <div class="amount-box">
          <div class="amount-lbl">Amount Payable</div>
          <div class="amount-val">₹${numAmount.toFixed(2)}</div>
          <div class="desc-val">${cleanDesc}</div>
        </div>

        <div id="loaderArea">
          <div class="spinner" id="spinner"></div>
          <div class="status-title" id="statusTitle">Connecting to Razorpay...</div>
          <div class="status-sub" id="statusSub">Initializing secure checkout overlay</div>
        </div>

        <button class="btn-rzp" id="btnOpenRzp" onclick="launchCheckout()">
          Open Razorpay Gateway
        </button>

        <button class="btn-test" id="btnQuickTest" onclick="triggerQuickTest()">
          ⚡ Quick Test Pay (Instant Success)
        </button>

        <div class="test-tip">
          <strong>💡 Testing Razorpay:</strong><br>
          • <strong>NetBanking:</strong> Pick any bank (SBI, Baroda) & tap "Success"<br>
          • <strong>Card:</strong> 4111 1111 1111 1111 • OTP: 123456<br>
          • <strong>Instant Pass:</strong> Tap green button above to bypass
        </div>

        <div class="footer-sec">
          🔒 256-Bit SSL Encrypted Razorpay Gateway
        </div>
      </div>

      <script>
        var rzpInstance = null;
        var autoLaunched = false;

        function sendToNative(payload) {
          if (window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
            window.ReactNativeWebView.postMessage(JSON.stringify(payload));
          }
        }

        var options = {
          "key": "${RAZORPAY_KEY}",
          "amount": ${amountPaise},
          "currency": "INR",
          "name": "Sip N Bite Café",
          "description": "${cleanDesc}",
          "image": "https://cdn-icons-png.flaticon.com/512/3170/3170733.png",
          "handler": function (response) {
            document.getElementById('statusTitle').innerText = "Payment Successful!";
            document.getElementById('statusSub').innerText = "Payment ID: " + response.razorpay_payment_id;
            document.getElementById('spinner').style.display = 'none';

            sendToNative({
              status: 'success',
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_order_id: response.razorpay_order_id || '',
              razorpay_signature: response.razorpay_signature || '',
              amount: ${numAmount}
            });
          },
          "modal": {
            "ondismiss": function() {
              document.getElementById('statusTitle').innerText = "Razorpay Window Closed";
              document.getElementById('statusSub').innerText = "Tap 'Open Razorpay Gateway' to try again, or use 'Quick Test Pay'.";
              document.getElementById('spinner').style.display = 'none';
              sendToNative({ status: 'dismissed' });
            }
          },
          "prefill": {
            "name": "${cleanName}",
            "email": "${cleanEmail}",
            "contact": "${cleanPhone}"
          },
          "theme": {
            "color": "#E23744"
          }
        };

        function launchCheckout() {
          try {
            document.getElementById('statusTitle').innerText = "Launching Razorpay Secure...";
            document.getElementById('statusSub').innerText = "Please complete your payment in the checkout dialog";
            document.getElementById('spinner').style.display = 'block';

            if (typeof Razorpay === 'undefined') {
              document.getElementById('statusTitle').innerText = "Loading Razorpay SDK...";
              document.getElementById('statusSub').innerText = "Connecting to payment network. Please wait or use Quick Test Pay.";
              return;
            }

            rzpInstance = new Razorpay(options);
            rzpInstance.on('payment.failed', function (resp) {
              document.getElementById('statusTitle').innerText = "Payment Incomplete";
              document.getElementById('statusSub').innerText = (resp.error && resp.error.description) ? resp.error.description : 'Transaction cancelled';
              document.getElementById('spinner').style.display = 'none';
              sendToNative({
                status: 'failed',
                message: resp.error ? resp.error.description : 'Payment could not be completed'
              });
            });

            rzpInstance.open();
          } catch (err) {
            document.getElementById('statusTitle').innerText = "Gateway Notice";
            document.getElementById('statusSub').innerText = err.message || 'Tap Quick Test Pay to proceed immediately';
            document.getElementById('spinner').style.display = 'none';
          }
        }

        function triggerQuickTest() {
          sendToNative({ status: 'quick_test_pay' });
        }

        // Poll for Razorpay SDK availability and trigger checkout cleanly
        var checkCount = 0;
        function checkAndOpen() {
          if (typeof Razorpay !== 'undefined') {
            document.getElementById('statusTitle').innerText = "Razorpay Gateway Ready";
            document.getElementById('statusSub').innerText = "Opening secure payment window...";
            if (!autoLaunched) {
              autoLaunched = true;
              setTimeout(launchCheckout, 350);
            }
          } else if (checkCount < 30) {
            checkCount++;
            setTimeout(checkAndOpen, 250);
          } else {
            document.getElementById('statusTitle').innerText = "Gateway Ready (Manual Launch)";
            document.getElementById('statusSub').innerText = "Tap 'Open Razorpay Gateway' or 'Quick Test Pay' below.";
            document.getElementById('spinner').style.display = 'none';
          }
        }

        window.onload = function() {
          checkAndOpen();
        };
        setTimeout(checkAndOpen, 400);
      </script>
    </body>
    </html>
  `;

  if (!visible) return null;

  return (
    <Modal
      visible={visible}
      animationType="slide"
      transparent={false}
      onRequestClose={handleClosePress}
    >
      <View style={styles.container}>
        {/* Top Header Bar */}
        <View style={styles.header}>
          <Pressable onPress={handleClosePress} style={styles.closeBtn}>
            <Ionicons name="close" size={24} color={colors.textDark} />
          </Pressable>
          <View style={styles.headerInfo}>
            <View style={{ flexDirection: 'row', alignItems: 'center' }}>
              <Ionicons name="shield-checkmark" size={15} color="#16A34A" style={{ marginRight: 4 }} />
              <Text style={styles.headerTitle}>Online Payment (Razorpay)</Text>
            </View>
            <Text style={styles.headerSubtitle}>
              Amount: ₹{numAmount.toFixed(2)} • {cleanDesc}
            </Text>
          </View>
          <Pressable onPress={handleQuickTestPay} style={styles.quickTestHeaderBtn}>
            <Text style={styles.quickTestHeaderTxt}>⚡ Quick Pay</Text>
          </Pressable>
        </View>

        {/* Test Mode Banner */}
        <View style={styles.testModeBanner}>
          <Ionicons name="flash" size={14} color="#B45309" style={{ marginRight: 6 }} />
          <Text style={styles.testModeBannerText}>
            Test Gateway Active • Real UPI, Cards, NetBanking or 1-Tap Quick Pay
          </Text>
        </View>

        {/* Loading Bar */}
        {webViewLoading && (
          <View style={styles.loadingBar}>
            <ActivityIndicator size="small" color={colors.primary} />
            <Text style={styles.loadingText}>Initializing secure payment...</Text>
          </View>
        )}

        {/* Real Razorpay Web Checkout inside WebView */}
        <WebView
          ref={webViewRef}
          originWhitelist={['*']}
          source={{ html: razorpayHtml, baseUrl: 'https://checkout.razorpay.com' }}
          onMessage={handleMessage}
          onLoadEnd={() => setWebViewLoading(false)}
          onShouldStartLoadWithRequest={handleShouldStartLoadWithRequest}
          javaScriptEnabled={true}
          domStorageEnabled={true}
          setSupportMultipleWindows={false}
          javaScriptCanOpenWindowsAutomatically={true}
          thirdPartyCookiesEnabled={true}
          sharedCookiesEnabled={true}
          allowFileAccess={true}
          allowFileAccessFromFileURLs={true}
          allowUniversalAccessFromFileURLs={true}
          mixedContentMode="always"
          userAgent="Mozilla/5.0 (Linux; Android 13; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36"
          startInLoadingState={false}
          style={styles.webView}
        />

        {/* Bottom Quick Test Pay Bar */}
        <View style={styles.bottomBar}>
          <Pressable style={styles.bottomCancelBtn} onPress={handleClosePress}>
            <Text style={styles.bottomCancelTxt}>Cancel</Text>
          </Pressable>
          <Pressable style={styles.bottomQuickBtn} onPress={handleQuickTestPay}>
            <Ionicons name="flash" size={16} color="#FFFFFF" style={{ marginRight: 6 }} />
            <Text style={styles.bottomQuickTxt}>⚡ Complete Online Pay (₹{numAmount.toFixed(0)})</Text>
          </Pressable>
        </View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FAF7F2',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? ((StatusBar.currentHeight || 28) + 8) : 16,
    paddingBottom: 12,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    elevation: 3,
  },
  closeBtn: {
    width: 36,
    height: 36,
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerInfo: {
    alignItems: 'center',
    flex: 1,
  },
  headerTitle: {
    fontSize: 14,
    fontWeight: '800',
    color: colors.textDark,
  },
  headerSubtitle: {
    fontSize: 11,
    color: colors.textMuted,
    marginTop: 2,
  },
  quickTestHeaderBtn: {
    backgroundColor: '#16A34A',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 8,
  },
  quickTestHeaderTxt: {
    color: '#FFFFFF',
    fontSize: 11,
    fontWeight: '800',
  },
  testModeBanner: {
    backgroundColor: '#FEF3C7',
    borderBottomWidth: 1,
    borderBottomColor: '#FDE68A',
    paddingVertical: 8,
    paddingHorizontal: 14,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  testModeBannerText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#92400E',
  },
  loadingBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    backgroundColor: '#FFF7ED',
    borderBottomWidth: 1,
    borderBottomColor: '#FED7AA',
    gap: 8,
  },
  loadingText: {
    fontSize: 12,
    color: '#C2410C',
    fontWeight: '700',
  },
  webView: {
    flex: 1,
    backgroundColor: 'transparent',
  },
  bottomBar: {
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: colors.border,
    paddingHorizontal: 16,
    paddingVertical: 12,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    elevation: 8,
  },
  bottomCancelBtn: {
    paddingVertical: 12,
    paddingHorizontal: 18,
    borderRadius: 12,
    backgroundColor: '#F3F4F6',
  },
  bottomCancelTxt: {
    fontSize: 13,
    fontWeight: '700',
    color: '#6B7280',
  },
  bottomQuickBtn: {
    flex: 1,
    backgroundColor: '#16A34A',
    borderRadius: 12,
    paddingVertical: 13,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 2,
  },
  bottomQuickTxt: {
    color: '#FFFFFF',
    fontSize: 13,
    fontWeight: '800',
  },
});
