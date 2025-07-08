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
        Schema::create('question_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->text('answer')->nullable();
            $table->string('file_path')->nullable(); // For file uploads
            $table->string('audio_path')->nullable(); // For audio responses
            $table->string('video_path')->nullable(); // For video responses
            $table->integer('points_earned')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('time_spent')->nullable(); // in seconds
            $table->text('feedback')->nullable();
            $table->json('grading_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_responses');
    }
};
