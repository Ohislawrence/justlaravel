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
        Schema::dropIfExists('quiz_group_items');
        Schema::dropIfExists('quiz_groups');
        

        Schema::create('quiz_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('organization_id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('parent_id')->references('id')->on('quiz_groups')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
        
        // Create migration for quiz_group_items table (if not exists)
        Schema::create('quiz_group_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_group_id');
            $table->unsignedBigInteger('quiz_id');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('quiz_group_id')->references('id')->on('quiz_groups')->onDelete('cascade');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_groups', function (Blueprint $table) {
            //
        });
    }
};
