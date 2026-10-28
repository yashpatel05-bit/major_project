import Constants from 'expo-constants';
import { Platform } from 'react-native';
import serverConfig from './serverConfig.json';

export const RAZORPAY_KEY = 'rzp_test_TXveDStqLBJjaK';
export const RAZORPAY_SECRET = 'FLXRT96zZHYQaR0M1gqy4wwO';
export const DISTANCE_MATRIX_API_KEY = '1padf1Q3jnteeyaMMFa8kLDnzqxd815ay0VD9VP6omJwsEb8j5HJt86PIVRSvjtk';

// ──────────────────────────────────────────────────────────────
// Automatically detect the current IP address from Expo / serverConfig
// ──────────────────────────────────────────────────────────────
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

  // Use auto-detected server IP from ensure-services.js
  if (serverConfig && serverConfig.serverIp) {
    return serverConfig.serverIp;
  }

  // Fallback to Wi-Fi network IP
  return '10.206.5.191';
};

export const SERVER_IP = getDevServerIp();

// Use the local network IP so both physical devices and emulators can connect to the backend
export const API_BASE_URL = `http://${SERVER_IP}:8000/api/`;

console.log(`[AppConfig] Connected API Base URL: ${API_BASE_URL}`);

