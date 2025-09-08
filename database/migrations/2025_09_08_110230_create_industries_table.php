<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Seed common industries with slugs
        $industries = [
            'Education',
            'Healthcare',
            'Information Technology',
            'Finance & Banking',
            'Government',
            'Manufacturing',
            'Retail & E-commerce',
            'Hospitality & Tourism',
            'Transportation & Logistics',
            'Energy & Utilities',
            'Construction & Real Estate',
            'Media & Entertainment',
            'Nonprofit & NGO',
            'Telecommunications',
            'Legal Services',
            'Pharmaceuticals',
            'Automotive',
            'Agriculture',
            'Aviation & Aerospace',
            'Other',
        ];

        foreach ($industries as $name) {
            DB::table('industries')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industries');
    }
};
