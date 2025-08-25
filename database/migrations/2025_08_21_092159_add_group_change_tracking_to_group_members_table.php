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
        Schema::table('group_members', function (Blueprint $table) {
            // Add timestamps for tracking when user joined/left groups
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->text('change_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('group_members', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'left_at', 'change_reason']);
        });
    }
};
