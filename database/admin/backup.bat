@echo off
setlocal
set "PGBIN=C:\Program Files\PostgreSQL\17\bin"

if "%PGPASSWORD%"=="" (
    echo Set the password first: $env:PGPASSWORD="your_password"
    exit /b 1
)

if not exist backups mkdir backups

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyy-MM-dd_HHmm"') do set "STAMP=%%i"

"%PGBIN%\pg_dump.exe" -h 127.0.0.1 -p 5432 -U postgres -Fc -f "backups\mhv_%STAMP%.dump" mhv

if errorlevel 1 (
    echo Backup FAILED
    exit /b 1
)

echo Backup OK: backups\mhv_%STAMP%.dump