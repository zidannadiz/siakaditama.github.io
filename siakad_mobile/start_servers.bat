@echo off
REM Script untuk menjalankan Laravel dan Flutter secara bersamaan
REM Untuk Windows CMD

echo === Starting SIAKAD Development Servers ===
echo.

REM Path ke project Laravel
set LARAVEL_PATH=..\SIAKAD-BARU

REM Check if Laravel path exists
if not exist "%LARAVEL_PATH%" (
    echo Error: Laravel project not found at %LARAVEL_PATH%
    echo Please update the path in this script
    pause
    exit /b 1
)

REM Start Laravel server in new window
echo Starting Laravel server...
start "Laravel Server" cmd /k "cd /d %LARAVEL_PATH% && echo Laravel Server Running on http://127.0.0.1:8000 && php artisan serve"

REM Wait a bit for Laravel to start
timeout /t 3 /nobreak >nul

REM Start Flutter app
echo Starting Flutter app...
echo.
echo Laravel server is running in a separate window
echo You can close this window after Flutter starts
echo.

flutter run
