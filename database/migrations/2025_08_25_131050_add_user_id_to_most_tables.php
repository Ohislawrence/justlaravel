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
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone');
            $table->boolean('is_active');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('grading_systems', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('question_pools', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
