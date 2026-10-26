-- Run as the postgres (owner) user while connected to the mhv database:
--   psql -h 127.0.0.1 -U postgres -d mhv -f database/admin/roles.sql
-- Safe to run again at any time (e.g. after migrate:fresh).

-- 1. Roles ---------------------------------------------------------------
DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'mhv_app') THEN
        CREATE ROLE mhv_app LOGIN PASSWORD 'change_me_app';
    END IF;
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'mhv_readonly') THEN
        CREATE ROLE mhv_readonly LOGIN PASSWORD 'change_me_readonly';
    END IF;
END
$$;

-- 2. Lock down the defaults: nobody gets in or creates objects unless granted.
REVOKE ALL ON DATABASE mhv FROM PUBLIC;
REVOKE CREATE ON SCHEMA public FROM PUBLIC;

GRANT CONNECT ON DATABASE mhv TO mhv_app, mhv_readonly;
GRANT USAGE ON SCHEMA public TO mhv_app, mhv_readonly;

-- 3. mhv_app: what the Laravel application needs at runtime (no DDL) ------
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO mhv_app;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO mhv_app;

-- Tables created later by the owner (new migrations) are granted automatically,
-- so migrate:fresh no longer leaves mhv_app without access.
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO mhv_app;
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT USAGE, SELECT ON SEQUENCES TO mhv_app;

-- The app never needs to read or change the migration bookkeeping table.
REVOKE ALL ON migrations FROM mhv_app;

-- 4. mhv_readonly: reporting only, and it cannot see password hashes ------
GRANT SELECT ON roles, announcements, complaints TO mhv_readonly; -- can see but can't edit--
GRANT SELECT (id, name, email, block, lot, mobile_number, role_id, created_at, updated_at)
    ON users TO mhv_readonly;

-- 5. Audit table: the app and the reporting role can read history but never
--    change it. Only the trigger function (SECURITY DEFINER) writes to it.
--    This must come AFTER the default privileges above, which would otherwise
--    hand the app write access to it.
REVOKE INSERT, UPDATE, DELETE ON complaint_status_history FROM mhv_app;
GRANT SELECT ON complaint_status_history TO mhv_app, mhv_readonly;

-- Quick check (optional): who can do what on complaint_status_history
--   SELECT grantee, privilege_type FROM information_schema.role_table_grants
--   WHERE table_name = 'complaint_status_history' ORDER BY grantee;