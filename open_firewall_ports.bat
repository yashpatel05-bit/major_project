@echo off
:: Run this script ONCE as Administrator to allow your phone to connect
:: to both Metro (8081) and Laravel (8000) through Windows Firewall.

echo ================================================
echo  SIP N BITE - Opening Firewall Ports
echo  (Run as Administrator)
echo ================================================

:: Remove old rules if they exist
netsh advfirewall firewall delete rule name="Expo Metro 8081" >nul 2>&1
netsh advfirewall firewall delete rule name="Laravel Backend 8000" >nul 2>&1

:: Allow inbound TCP on port 8081 (Metro bundler - Expo Go)
netsh advfirewall firewall add rule name="Expo Metro 8081" dir=in action=allow protocol=TCP localport=8081 profile=any
if %errorlevel%==0 (
    echo [OK] Port 8081 (Expo Metro) is now open.
) else (
    echo [FAIL] Could not open port 8081. Make sure you are running as Administrator.
    pause
    exit /b 1
)

:: Allow inbound TCP on port 8000 (Laravel backend - phone API calls)
netsh advfirewall firewall add rule name="Laravel Backend 8000" dir=in action=allow protocol=TCP localport=8000 profile=any
if %errorlevel%==0 (
    echo [OK] Port 8000 (Laravel) is now open.
) else (
    echo [FAIL] Could not open port 8000.
)

echo.
echo ================================================
echo  Done! Your phone can now reach:
echo    Metro:   http://YOUR_WIFI_IP:8081
echo    Laravel: http://YOUR_WIFI_IP:8000/api/
echo ================================================
echo.
pause
