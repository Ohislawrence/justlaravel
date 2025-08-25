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
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('grading_systems', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('question_pools', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('created_by');
            $table->integer('last_updated_by');
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
