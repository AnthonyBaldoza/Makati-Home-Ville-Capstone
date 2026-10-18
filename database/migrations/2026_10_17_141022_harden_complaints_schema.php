<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_status_check CHECK (status IN ('Pending', 'In Progress', 'Resolved'))");

        Schema::table('complaint_status_history', function (Blueprint $table) {
            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::table('complaint_status_history', function (Blueprint $table) {
            $table->dropIndex(['complaint_id']);
        });

        DB::statement('ALTER TABLE complaints DROP CONSTRAINT complaints_status_check');
    }
};
