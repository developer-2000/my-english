@echo off
echo Starting My English Project...
echo.

REM Проверяем что Open Server запущен
echo Checking Open Server...
tasklist /FI "IMAGENAME eq OpenServer.exe" 2>NUL | find /I /N "OpenServer.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo Open Server is running
) else (
    echo Starting Open Server...
    start "" "C:\OpenServer\OpenServer.exe"
    timeout /t 5 /nobreak >nul
)

REM Запускаем браузер
echo Opening browser...
start "" "http://english.my"

echo.
echo Project started successfully!
echo You can now access: http://english.my
pause
