<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Message ng HOA/Staff (reason kapag Declined)
        Schema::table('reservations', function (Blueprint $table) {
            $table->text('staff_message')->nullable();
        });

        // Chat: isang thread kada resident
        Schema::create('reservation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
            $table->index(['resident_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_messages');

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('staff_message');
        });
    }
};
