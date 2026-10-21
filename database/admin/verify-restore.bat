@echo off
setlocal EnableDelayedExpansion
set "PGBIN=C:\Program Files\PostgreSQL\17\bin"
set "PATH=%PGBIN%;%PATH%"
set "SRC=mhv"
set "DST=mhv_restore"

if "%PGPASSWORD%"=="" (
    echo Set the password first: $env:PGPASSWORD="your_password"
    exit /b 1
)

set FAIL=0
echo Restore verification: %SRC% vs %DST%
echo.
echo Row counts
for %%T in (roles users announcements complaints complaint_status_history) do (
    for /f %%A in ('psql -h 127.0.0.1 -p 5432 -U postgres -d %SRC% -t -A -c "select count(*) from %%T"') do set "A=%%A"
    for /f %%B in ('psql -h 127.0.0.1 -p 5432 -U postgres -d %DST% -t -A -c "select count(*) from %%T"') do set "B=%%B"
    if "!A!"=="!B!" (
        echo   %%T: !A! = !B!  OK
    ) else (
        echo   %%T: !A! vs !B!  MISMATCH
        set FAIL=1
    )
)

echo.
echo Database objects in %DST% (expected 1 each)
for /f %%C in ('psql -h 127.0.0.1 -p 5432 -U postgres -d %DST% -t -A -c "select count(*) from pg_trigger where tgname='complaints_status_audit' and not tgisinternal"') do set "C=%%C"
echo   trigger complaints_status_audit: !C!
if not "!C!"=="1" set FAIL=1
for /f %%C in ('psql -h 127.0.0.1 -p 5432 -U postgres -d %DST% -t -A -c "select count(*) from pg_indexes where indexname='complaints_user_id_created_at_index'"') do set "C=%%C"
echo   index complaints_user_id_created_at_index: !C!
if not "!C!"=="1" set FAIL=1
for /f %%C in ('psql -h 127.0.0.1 -p 5432 -U postgres -d %DST% -t -A -c "select count(*) from pg_constraint where conname='complaints_status_check'"') do set "C=%%C"
echo   check constraint complaints_status_check: !C!
if not "!C!"=="1" set FAIL=1

echo.
if "!FAIL!"=="0" (
    echo RESULT: RESTORE VERIFIED
    exit /b 0
) else (
    echo RESULT: RESTORE FAILED
    exit /b 1
)