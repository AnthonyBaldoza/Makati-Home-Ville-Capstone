<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('purpose');
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->index(['facility_id', 'reservation_date']);
        });

        DB::statement('ALTER TABLE reservations ADD CONSTRAINT reservations_time_check CHECK (end_time > start_time)');
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT reservations_status_check CHECK (status IN ('Pending', 'Approved', 'Declined'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};