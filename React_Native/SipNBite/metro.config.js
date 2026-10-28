const os = require('os');

// ──────────────────────────────────────────────────────────────
// Detect the correct Wi-Fi/Ethernet IP (skip loopback & link-local)
// and pin Metro to it so the QR code always points to the right address.
// ──────────────────────────────────────────────────────────────
function getHostIp() {
  const ifaces = os.networkInterfaces();
  let wifiIp = null, ethIp = null, otherIp = null;
  for (const [name, addrs] of Object.entries(ifaces)) {
    if (!addrs) continue;
    for (const addr of addrs) {
      if (addr.family === 'IPv4' && !addr.internal && !addr.address.startsWith('169.254.')) {
        const n = name.toLowerCase();
        if (n.includes('wi-fi') || n.includes('wifi') || n.includes('wlan')) wifiIp = addr.address;
        else if (n.includes('ethernet') || n.includes('eth')) ethIp = addr.address;
        else if (!otherIp) otherIp = addr.address;
      }
    }
  }
  return wifiIp || ethIp || otherIp || '127.0.0.1';
}

const hostIp = getHostIp();
// Force Metro (and Expo Go QR code) to use the real network IP
if (!process.env.REACT_NATIVE_PACKAGER_HOSTNAME) {
  process.env.REACT_NATIVE_PACKAGER_HOSTNAME = hostIp;
  console.log(`[Metro] Pinned packager hostname to: ${hostIp}`);
}

// Automatically verify & connect backend (Laravel), database (MySQL), and IP
try {
  require('./scripts/ensure-services')();
} catch (e) {
  console.warn('[Metro] Could not run ensure-services hook:', e.message);
}

// Learn more https://docs.expo.io/guides/customizing-metro
const { getDefaultConfig } = require('expo/metro-config');

/** @type {import('expo/metro-config').MetroConfig} */
const config = getDefaultConfig(__dirname);

module.exports = config;
