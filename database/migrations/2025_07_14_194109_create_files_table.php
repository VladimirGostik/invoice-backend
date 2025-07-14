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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->morphs('fileable'); // Creates fileable_id and fileable_type columns
            $table->string('path'); // Store the file path
            $table->string('filename');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->string('disk')->default('public'); // Which storage disk it uses
            $table->string('collection')->nullable(); // For grouping files (e.g., 'logos', 'avatars')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
