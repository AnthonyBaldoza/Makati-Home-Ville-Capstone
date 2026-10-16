<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->restrictOnDelete();
            $table->string('old_status');
            $table->string('new_status');
            $table->string('db_user');
            $table->timestamp('changed_at')->useCurrent();
        });

        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION log_complaint_status_change() RETURNS trigger
        LANGUAGE plpgsql SECURITY DEFINER SET search_path = public AS $$
        BEGIN
            INSERT INTO complaint_status_history (complaint_id, old_status, new_status, db_user, changed_at)
            VALUES (OLD.id, OLD.status, NEW.status, session_user, now());
            RETURN NEW;
        END;
        $$;
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER complaints_status_audit
        AFTER UPDATE OF status ON complaints
        FOR EACH ROW
        WHEN (OLD.status IS DISTINCT FROM NEW.status)
        EXECUTE FUNCTION log_complaint_status_change();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS complaints_status_audit ON complaints');
        DB::unprepared('DROP FUNCTION IF EXISTS log_complaint_status_change()');
        Schema::dropIfExists('complaint_status_history');
    }
};
