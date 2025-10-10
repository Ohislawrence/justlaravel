<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable(); 
            $table->string('browser')->nullable(); // Chrome, Firefox, Safari, etc.
            $table->string('platform')->nullable(); // Windows, macOS, Linux, iOS, Android
            $table->json('location_data')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable(); 
        });
    }

    public function down()
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn([
                'ip_address',
                'user_agent',
                'device_type',
                'browser',
                'platform',
                'location_data',
                'country',
                'city',
                'region',
            ]);
        });
    }
};
