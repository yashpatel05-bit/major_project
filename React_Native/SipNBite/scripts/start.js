/**
 * start.js
 * Detects the correct Wi-Fi IP, pins Metro to it, runs ensure-services,
 * then spawns `expo start` with the env var already in place.
 * This avoids the race condition where Expo CLI picks up the wrong
 * network interface (169.254.x.x) before app.config.js runs.
 */

const os = require('os');
const { spawn } = require('child_process');
const path = require('path');

// ── 1. Detect correct Wi-Fi IP ──────────────────────────────────
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

// ── 2. Pin Metro BEFORE Expo CLI starts ─────────────────────────
process.env.REACT_NATIVE_PACKAGER_HOSTNAME = hostIp;
console.log(`\n[Launcher] Wi-Fi IP detected: ${hostIp}`);
console.log(`[Launcher] REACT_NATIVE_PACKAGER_HOSTNAME = ${hostIp}`);

// ── 3. Run ensure-services (starts Laravel, MySQL, updates config) ─
try {
  // Reset the guard so it always runs fresh
  global.__SIPNBITE_SERVICES_INITIALIZED__ = false;
  require('./ensure-services')();
} catch (e) {
  console.warn('[Launcher] ensure-services warning:', e.message);
}

// ── 4. Parse extra args passed to this script (e.g. --android) ──
const extraArgs = process.argv.slice(2);

// ── 5. Spawn expo start ─────────────────────────────────────────
console.log(`[Launcher] Starting Expo on http://${hostIp}:8081\n`);

const expo = spawn('npx', ['expo', 'start', '--clear', ...extraArgs], {
  env: { ...process.env, REACT_NATIVE_PACKAGER_HOSTNAME: hostIp },
  stdio: 'inherit',
  shell: true,
  cwd: path.resolve(__dirname, '..'),
});

expo.on('exit', (code) => process.exit(code ?? 0));
