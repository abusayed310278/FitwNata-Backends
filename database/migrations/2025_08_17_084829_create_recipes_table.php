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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name')->nullable()->comment('Legacy field, kept for compatibility');
            $table->text('description')->nullable();
            $table->json('instructions');
            $table->json('ingredients');
            $table->unsignedSmallInteger('prep_time')->comment('Prep time in minutes');
            $table->unsignedSmallInteger('cook_time')->comment('Cook time in minutes');
            $table->unsignedSmallInteger('prep_time_minutes')->nullable()->comment('Legacy field');
            $table->unsignedSmallInteger('cook_time_minutes')->nullable()->comment('Legacy field');
            $table->unsignedTinyInteger('servings')->default(1);
            $table->unsignedSmallInteger('calories');
            $table->decimal('protein', 5, 1)->nullable()->comment('Protein in grams');
            $table->decimal('carbs', 5, 1)->nullable()->comment('Carbs in grams');
            $table->decimal('fat', 5, 1)->nullable()->comment('Fat in grams');
            $table->decimal('protein_grams', 5, 1)->nullable()->comment('Legacy field');
            $table->decimal('carbs_grams', 5, 1)->nullable()->comment('Legacy field');
            $table->decimal('fat_grams', 5, 1)->nullable()->comment('Legacy field');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->json('tags')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
