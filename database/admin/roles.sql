-- Run as the postgres user while connected to the mhv database.

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

GRANT CONNECT ON DATABASE mhv TO mhv_app, mhv_readonly;
GRANT USAGE ON SCHEMA public TO mhv_app, mhv_readonly;

-- mhv_app: what the Laravel application needs at runtime (no DDL)
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO mhv_app;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO mhv_app;

-- mhv_readonly: reporting only, and it cannot see password hashes
GRANT SELECT ON roles, announcements, complaints TO mhv_readonly;
GRANT SELECT (id, name, email, block, lot, mobile_number, role_id, created_at, updated_at)
    ON users TO mhv_readonly;

    
-- Audit table: the app and the reporting role can read history but never change it.
-- Only the trigger function (SECURITY DEFINER) writes to it.
REVOKE INSERT, UPDATE, DELETE ON complaint_status_history FROM mhv_app;
GRANT SELECT ON complaint_status_history TO mhv_app, mhv_readonly;