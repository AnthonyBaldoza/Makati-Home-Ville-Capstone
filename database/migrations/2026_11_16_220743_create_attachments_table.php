<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('complaint_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->timestamps();

            // PostgreSQL does not index foreign keys automatically
            $table->index('announcement_id');
            $table->index('complaint_id');
        });

        DB::statement('ALTER TABLE attachments ADD CONSTRAINT attachments_one_parent_check
            CHECK (num_nonnulls(announcement_id, complaint_id) = 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};