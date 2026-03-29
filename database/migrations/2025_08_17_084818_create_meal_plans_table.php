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
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name')->nullable()->comment('Legacy field, kept for compatibility');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('duration_days')->comment('Duration in days');
            $table->unsignedSmallInteger('total_calories')->comment('Total daily calories');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->enum('goal', ['general_health', 'weight_loss', 'muscle_gain', 'convenience', 'heart_health', 'detox', 'family'])->default('general_health');
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
