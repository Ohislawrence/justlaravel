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
        Schema::create('subscription_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('number');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        Schema::create('plan_feature', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_plan_id');
            $table->unsignedBigInteger('subscription_feature_id');
            $table->string('value')->nullable(); // Can be a number, boolean, etc.
            $table->timestamps();
            
            $table->primary(['subscription_plan_id', 'subscription_feature_id']);
            $table->foreign('subscription_plan_id')
                ->references('id')
                ->on('subscription_plans')
                ->onDelete('cascade');

            $table->foreign('subscription_feature_id')
                ->references('id')
                ->on('subscription_features')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_features');
    }
};
