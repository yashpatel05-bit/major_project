import Constants from 'expo-constants';

export const RAZORPAY_KEY = 'rzp_test_YOUR_KEY_HERE';
export const RAZORPAY_SECRET = 'YOUR_SECRET_HERE';
export const DISTANCE_MATRIX_API_KEY = 'YOUR_DISTANCE_MATRIX_API_KEY';

// ──────────────────────────────────────────────────────────────
// Your PC's local IP address (run `ipconfig` to find it).
// Update this if your WiFi IP changes.
// Make sure to start Laravel with: php artisan serve --host=0.0.0.0
// ──────────────────────────────────────────────────────────────
import { Platform } from 'react-native';

// Automatically detect the current IP address from Expo
const getDevServerIp = () => {
  const hostUri = 
    Constants.expoConfig?.hostUri || 
    Constants.manifest2?.extra?.expoGo?.debuggerHost || 
    Constants.manifest?.debuggerHost;

  if (hostUri) {
    const ip = hostUri.split(':')[0];
    if (ip && ip !== 'localhost' && ip !== '127.0.0.1') {
      return ip;
    }
  }

  // Fallback to current Wi-Fi network IP
  return '10.206.5.191';
};

export const SERVER_IP = getDevServerIp();

// Use the local network IP so both physical devices and emulators can connect to the backend
export const API_BASE_URL = `http://${SERVER_IP}:8000/api/`;

console.log(`[AppConfig] Connected API Base URL: ${API_BASE_URL}`);

