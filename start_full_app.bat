@echo off
title Sip N Bite - Full Stack Launcher
color 0A

echo.
echo ======================================================
echo   SIP N BITE - One-Click Full Stack Launcher
echo ======================================================

:: ── Kill any stale servers on ports 8000 and 8081 ──────────────
echo [Cleanup] Freeing ports 8000 and 8081...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr ":8000 " ^| findstr "LISTENING" 2^>nul') do (
    taskkill /F /PID %%a >nul 2>&1
)
for /f "tokens=5" %%a in ('netstat -ano ^| findstr ":8081 " ^| findstr "LISTENING" 2^>nul') do (
    taskkill /F /PID %%a >nul 2>&1
)
timeout /t 1 /nobreak >nul

:: ── Detect Wi-Fi IP (skip loopback and 169.254.x.x) ────────────
echo [IP] Detecting Wi-Fi IP address...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr /v "169.254"') do (
    set RAW_IP=%%a
    goto :got_ip
)
:got_ip
:: Strip leading space
set HOST_IP=%RAW_IP: =%
echo [IP] Using: %HOST_IP%

:: ── Clear Laravel config cache ──────────────────────────────────
echo [Laravel] Clearing config cache...
cd /d "%~dp0\WEB"
php artisan config:clear >nul 2>&1

:: ── Start Laravel on 0.0.0.0 (all interfaces) ──────────────────
echo [Laravel] Starting backend on 0.0.0.0:8000 ...
start "Laravel Backend" cmd /k "cd /d %~dp0\WEB && php artisan serve --host=0.0.0.0 --port=8000"
timeout /t 3 /nobreak >nul

:: ── Start Expo (Metro pinned to correct IP) ─────────────────────
echo [Expo] Starting Expo Metro bundler...
echo [Expo] Metro will use IP: %HOST_IP%
cd /d "%~dp0\React_Native\SipNBite"
set REACT_NATIVE_PACKAGER_HOSTNAME=%HOST_IP%
start "Expo Metro" cmd /k "cd /d %~dp0\React_Native\SipNBite && set REACT_NATIVE_PACKAGER_HOSTNAME=%HOST_IP% && npx expo start"

echo.
echo ======================================================
echo   READY!
echo   Web (browser):  http://localhost:8000
echo   Web (phone):    http://%HOST_IP%:8000
echo   Metro (phone):  exp://%HOST_IP%:8081
echo ======================================================
echo.
echo Both servers are running in separate windows.
echo Close those windows or press Ctrl+C in each to stop.
pause
