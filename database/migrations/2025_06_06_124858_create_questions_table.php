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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // mcq, true_false, fill_blank, essay, file_upload, audio, video, etc.
            $table->text('question');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('audio')->nullable();
            $table->string('video')->nullable();
            $table->integer('points')->default(1);
            $table->integer('time_limit')->nullable(); // seconds per question
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->json('options')->nullable(); // For MCQs, etc.
            $table->json('correct_answers')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
