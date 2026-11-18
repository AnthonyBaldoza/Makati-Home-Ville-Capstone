<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Lagyan ng end time ang walang end (1 oras)
        DB::statement("UPDATE community_events SET end_at = start_at + interval '1 hour' WHERE end_at IS NULL");
        DB::statement("ALTER TABLE community_events ALTER COLUMN end_at SET NOT NULL");

        // Bawal ang magsapawan na oras (ang magkasunod ay pwede)
        DB::statement("ALTER TABLE community_events ADD CONSTRAINT community_events_no_overlap EXCLUDE USING gist (tsrange(start_at, end_at) WITH &&)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE community_events DROP CONSTRAINT community_events_no_overlap");
        DB::statement("ALTER TABLE community_events ALTER COLUMN end_at DROP NOT NULL");
    }
};