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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->string('fingerprint')->nullable();
                $table->string('status')->nullable();
                $table->json('fingerprint_components')->nullable();
                $table->json('proctoring_data')->nullable();
                $table->integer('violation_count')->default(0);
            $table->timestamp('fingerprint_recorded_at')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->dropColumn(['proctoring_data','fingerprint', 'fingerprint_components','status','violation_count', 'fingerprint_recorded_at']);
            });
        });
    }
};
