<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_chat_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('reservation_chat_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable();
            }
            if (! Schema::hasColumn('reservation_chat_messages', 'attachment_name')) {
                $table->string('attachment_name')->nullable();
            }
            if (! Schema::hasColumn('reservation_chat_messages', 'attachment_mime')) {
                $table->string('attachment_mime', 100)->nullable();
            }
            if (! Schema::hasColumn('reservation_chat_messages', 'attachment_size')) {
                $table->unsignedBigInteger('attachment_size')->nullable();
            }
            if (! Schema::hasColumn('reservation_chat_messages', 'edited_at')) {
                $table->timestamp('edited_at')->nullable();
            }
            if (! Schema::hasColumn('reservation_chat_messages', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservation_chat_messages', function (Blueprint $table) {
            foreach (['attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size', 'edited_at', 'deleted_at'] as $col) {
                if (Schema::hasColumn('reservation_chat_messages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
