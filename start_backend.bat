@echo off
echo Starting Sip N Bite Laravel Backend on 0.0.0.0:8000...
cd /d "%~dp0\WEB"
php artisan serve --host=0.0.0.0 --port=8000
pause
