<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id(); # Primary Key para makroon ng unique identifier
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); #para malaman kung sino nag sumbit , para hinde mabura pag may user pa
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
