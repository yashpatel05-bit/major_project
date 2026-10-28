/**
 * ensure-services.js
 * Automatically verifies and starts MySQL database, Laravel backend (0.0.0.0:8000),
 * detects host network IP, updates app configuration, and clears stale 8081 ports.
 * Runs automatically when `npx expo start` or `npm start` is invoked.
 */

const fs = require('fs');
const path = require('path');
const os = require('os');
const { execSync } = require('child_process');

function ensureServices() {
  if (global.__SIPNBITE_SERVICES_INITIALIZED__) {
    return;
  }
  global.__SIPNBITE_SERVICES_INITIALIZED__ = true;

  console.log('\n======================================================');
  console.log('  SIP N BITE - AUTO-CONNECTING BACKEND & DATABASE');
  console.log('======================================================');

  // 1. Detect Host IPv4 Address
  const localIp = getHostIpAddress();
  console.log(`[IP Detection] Host Network IP: ${localIp}`);

  // 2. Pin Metro to the correct IP (always override — Expo CLI may have set the wrong interface)
  process.env.REACT_NATIVE_PACKAGER_HOSTNAME = localIp;
  console.log(`[Metro] Pinned packager hostname -> ${localIp}`);

  // 3. Update serverConfig.json
  updateServerConfig(localIp);

  // 4. Ensure MySQL Database is running on Port 3306
  ensureDatabase();

  // 5. Ensure Laravel Backend is running on 0.0.0.0:8000
  ensureBackend(localIp);

  // 6. Check & clean stale port 8081 (if held by old dead node process)
  cleanStaleExpoPort();

  console.log('======================================================');
  console.log(`  READY! API URL: http://${localIp}:8000/api/`);
  console.log('======================================================\n');

}

/**
 * Finds the local IPv4 address (prioritizing Wi-Fi, then Ethernet, avoiding loopback/link-local)
 */
function getHostIpAddress() {
  const interfaces = os.networkInterfaces();
  let wifiIp = null;
  let ethIp = null;
  let otherIp = null;

  for (const [name, addrs] of Object.entries(interfaces)) {
    if (!addrs) continue;
    for (const addr of addrs) {
      if (addr.family === 'IPv4' && !addr.internal && !addr.address.startsWith('169.254.')) {
        const lowerName = name.toLowerCase();
        if (lowerName.includes('wi-fi') || lowerName.includes('wifi') || lowerName.includes('wlan')) {
          wifiIp = addr.address;
        } else if (lowerName.includes('ethernet') || lowerName.includes('eth')) {
          ethIp = addr.address;
        } else if (!otherIp) {
          otherIp = addr.address;
        }
      }
    }
  }

  return wifiIp || ethIp || otherIp || '127.0.0.1';
}

/**
 * Updates src/config/serverConfig.json with the detected host IP
 */
function updateServerConfig(ip) {
  try {
    const configDir = path.resolve(__dirname, '../src/config');
    if (!fs.existsSync(configDir)) {
      fs.mkdirSync(configDir, { recursive: true });
    }
    const configPath = path.join(configDir, 'serverConfig.json');
    const data = {
      serverIp: ip,
      backendPort: 8000,
      apiBaseUrl: `http://${ip}:8000/api/`,
      updatedAt: new Date().toISOString()
    };
    fs.writeFileSync(configPath, JSON.stringify(data, null, 2), 'utf8');
    console.log(`[Config] Synchronized serverConfig.json -> ${data.apiBaseUrl}`);
  } catch (err) {
    console.warn(`[Config Warning] Could not update serverConfig.json:`, err.message);
  }
}

/**
 * Checks if a port is in LISTENING state
 */
function isPortListening(port) {
  try {
    const out = execSync(`cmd /c "netstat -ano | findstr :${port}"`, {
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'ignore'],
      timeout: 2000
    });
    return out.includes('LISTENING');
  } catch {
    return false;
  }
}

/**
 * Gets listening details for port 8000
 */
function getPort8000Details() {
  try {
    const out = execSync('cmd /c "netstat -ano | findstr :8000"', {
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'ignore'],
      timeout: 2000
    });
    const lines = out.split('\r\n').filter((l) => l.includes('LISTENING'));
    if (lines.length === 0) return { listening: false };

    const isWildcard = lines.some((l) => l.includes('0.0.0.0:8000') || l.includes('[::]:8000'));
    const isLoopbackOnly = !isWildcard && lines.some((l) => l.includes('127.0.0.1:8000'));

    let pids = [];
    lines.forEach((l) => {
      const match = l.trim().match(/\s+(\d+)$/);
      if (match && !pids.includes(match[1])) {
        pids.push(match[1]);
      }
    });

    return { listening: true, isWildcard, isLoopbackOnly, pids };
  } catch {
    return { listening: false };
  }
}

/**
 * Ensures MySQL is running
 */
function ensureDatabase() {
  if (isPortListening(3306)) {
    console.log('[Database] MySQL is active and listening on port 3306.');
    return;
  }

  console.log('[Database] MySQL is not running. Attempting to start service...');
  try {
    execSync(
      'powershell -NoProfile -ExecutionPolicy Bypass -Command "Start-Service wampmysqld64 -ErrorAction SilentlyContinue; net start mysql 2>$null; Start-Service MySQL80 -ErrorAction SilentlyContinue"',
      { stdio: 'ignore', timeout: 5000 }
    );
  } catch {
    // Ignore error and poll
  }

  // Poll for up to 6 seconds
  const start = Date.now();
  while (Date.now() - start < 6000) {
    if (isPortListening(3306)) {
      console.log('[Database] MySQL started successfully on port 3306!');
      return;
    }
    sleepMs(500);
  }

  console.warn('[Database Warning] Port 3306 is not responding yet. Please ensure WampServer/XAMPP MySQL is active.');
}

/**
 * Ensures Laravel Backend is running on 0.0.0.0:8000
 */
function ensureBackend(localIp) {
  const webDir = path.resolve(__dirname, '../../../WEB');
  const details = getPort8000Details();

  if (details.listening && details.isWildcard) {
    console.log(`[Backend] Laravel is already running on 0.0.0.0:8000.`);
    return;
  }

  if (details.listening && details.isLoopbackOnly) {
    console.log('[Backend] Laravel is only bound to 127.0.0.1 (inaccessible to phones). Restarting on 0.0.0.0...');
    if (details.pids && details.pids.length > 0) {
      details.pids.forEach((pid) => {
        try {
          execSync(`taskkill /F /PID ${pid}`, { stdio: 'ignore', timeout: 3000 });
        } catch {}
      });
    }
    sleepMs(800);
  }

  console.log('[Backend] Starting Laravel Backend on 0.0.0.0:8000...');
  try {
    const escapedDir = webDir.replace(/'/g, "''");
    execSync(
      `powershell -NoProfile -ExecutionPolicy Bypass -Command "Invoke-CimMethod -ClassName Win32_Process -MethodName Create -Arguments @{ CommandLine = 'php artisan serve --host=0.0.0.0 --port=8000'; CurrentDirectory = '${escapedDir}' }"`,
      { stdio: 'ignore', timeout: 5000 }
    );
  } catch (err) {
    console.warn('[Backend Warning] Could not launch via CimMethod:', err.message);
  }

  // Poll until port 8000 is listening
  const start = Date.now();
  let ready = false;
  while (Date.now() - start < 7000) {
    const check = getPort8000Details();
    if (check.listening) {
      ready = true;
      break;
    }
    sleepMs(500);
  }

  if (ready) {
    console.log(`[Backend] Laravel Backend is running and reachable on http://${localIp}:8000/api/`);
  } else {
    console.warn('[Backend Warning] Laravel did not bind to port 8000 within 7 seconds. You can run start_backend.bat manually if needed.');
  }
}

/**
 * Free port 8081 if occupied by another node process (prevents "Port 8081 is being used by another process")
 */
function cleanStaleExpoPort() {
  try {
    const out = execSync('cmd /c "netstat -ano | findstr :8081"', {
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'ignore'],
      timeout: 2000
    });
    const lines = out.split('\r\n').filter((l) => l.includes('LISTENING'));
    if (lines.length === 0) return;

    for (const line of lines) {
      const match = line.trim().match(/\s+(\d+)$/);
      if (match) {
        const pid = match[1];
        if (parseInt(pid, 10) !== process.pid) {
          // Check if it's a node process
          try {
            const procOut = execSync(`powershell -NoProfile -Command "(Get-Process -Id ${pid} -ErrorAction SilentlyContinue).ProcessName"`, {
              encoding: 'utf8',
              stdio: ['ignore', 'pipe', 'ignore'],
              timeout: 2000
            }).trim();

            if (procOut.toLowerCase() === 'node') {
              console.log(`[Port 8081] Found old orphaned Expo node process (PID ${pid}). Cleaning up to free port 8081...`);
              execSync(`taskkill /F /PID ${pid}`, { stdio: 'ignore', timeout: 2000 });
              sleepMs(500);
            }
          } catch {}
        }
      }
    }
  } catch {}
}

function sleepMs(ms) {
  const end = Date.now() + ms;
  while (Date.now() < end) {}
}

module.exports = ensureServices;

// If executed directly from CLI: `node scripts/ensure-services.js`
if (require.main === module) {
  ensureServices();
}
