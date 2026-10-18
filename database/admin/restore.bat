@echo off
setlocal
set "PGBIN=C:\Program Files\PostgreSQL\17\bin"
set "TARGET=mhv_restore"

if "%~1"=="" (
    echo Usage: restore.bat path\to\backup.dump
    exit /b 1
)
if "%PGPASSWORD%"=="" (
    echo Set the password first: $env:PGPASSWORD="your_password"
    exit /b 1
)

"%PGBIN%\dropdb.exe" --if-exists -h 127.0.0.1 -p 5432 -U postgres %TARGET%
"%PGBIN%\createdb.exe" -h 127.0.0.1 -p 5432 -U postgres %TARGET%
"%PGBIN%\pg_restore.exe" -h 127.0.0.1 -p 5432 -U postgres -d %TARGET% "%~1"

if errorlevel 1 (
    echo Restore reported errors
    exit /b 1
)

echo Restore OK into database %TARGET%