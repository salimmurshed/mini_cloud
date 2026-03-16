<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->timestamp('upload_time');
            $table->softDeletes();
            $table->timestamps();

            // Unique constraint to prevent duplicate names for active files
            $table->unique(['user_id', 'file_name', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_files');
    }
};
