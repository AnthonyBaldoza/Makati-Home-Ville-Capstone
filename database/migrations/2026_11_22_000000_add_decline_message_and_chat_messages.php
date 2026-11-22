<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('reservations', 'decline_message')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->text('decline_message')->nullable();
            });
        }

        if (! Schema::hasTable('reservation_chat_messages')) {
            Schema::create('reservation_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('resident_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->text('body');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['resident_id', 'id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_chat_messages');

        if (Schema::hasColumn('reservations', 'decline_message')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('decline_message');
            });
        }
    }
};
