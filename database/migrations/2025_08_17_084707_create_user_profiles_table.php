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

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('username')->unique();
            $table->enum('gender',['male','female','other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->json('health_conditions')->nullable();
            $table->json('preferred_workout_types')->nullable();
            $table->enum('training_location', ['home', 'gym', 'outdoors', 'outdoor', 'studio', 'crossfit_box', 'no_preference'])->nullable();
            $table->json('fitness_goals')->nullable();
            $table->enum('training_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable();
            $table->string('weekly_training_objective')->nullable()->comment('e.g., 3-4_times, daily, etc.');
            $table->json('equipment_availability')->nullable();
            $table->enum('nutrition_knowledge_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable();
            $table->enum('preferred_recipe_type', ['quick_easy', 'high_protein', 'healthy_balanced', 'energy_boosting', 'balanced_macros', 'plant_based', 'performance_nutrition', 'diabetic_friendly', 'scientifically_based', 'western', 'local', 'both'])->nullable();

            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
