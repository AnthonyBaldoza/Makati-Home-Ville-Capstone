<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laravel's standard table for database notifications.
     * notifiable_type + notifiable_id point to the user who receives it.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable'); // adds an index on (notifiable_type, notifiable_id)
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // The bell runs "count my unread notifications" on every page. A partial index
        // holds only the unread rows, so it stays small however many are already read.
        DB::statement('CREATE INDEX notifications_unread_index
            ON notifications (notifiable_type, notifiable_id)
            WHERE read_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};