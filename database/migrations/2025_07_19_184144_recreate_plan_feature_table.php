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
        // Drop the existing table if it exists
        Schema::dropIfExists('plan_feature');
        
        // Create new table with correct structure
        Schema::create('plan_feature', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('feature_id');
            $table->string('value')->nullable();
            $table->timestamps();
            
            $table->primary(['plan_id', 'feature_id']);
            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->foreign('feature_id')->references('id')->on('subscription_features')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('plan_feature');
        
        // Recreate with old structure if needed
        Schema::create('plan_feature', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_plan_id');
            $table->unsignedBigInteger('subscription_feature_id');
            $table->string('value')->nullable();
            $table->timestamps();
            
            $table->primary(['subscription_plan_id', 'subscription_feature_id']);
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->foreign('subscription_feature_id')->references('id')->on('subscription_features')->onDelete('cascade');
        });
    }
};
