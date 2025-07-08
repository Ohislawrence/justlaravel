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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Creator
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->string('quiz_type'); // test, exam, practice, survey, etc.
            $table->string('industry')->nullable(); // HR, Education, etc.
            $table->boolean('is_published')->default(false);
            $table->boolean('is_public')->default(false);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_answers')->default(false);
            $table->boolean('show_correct_answers')->default(false);
            $table->boolean('show_leaderboard')->default(false);
            $table->boolean('enable_discussions')->default(false);
            $table->integer('max_attempts')->default(1);
            $table->integer('max_participants')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('passing_score')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
