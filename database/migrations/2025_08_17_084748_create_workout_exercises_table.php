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
        Schema::create('workout_exercise', function (Blueprint $table) {
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('sets')->nullable();
            $table->unsignedTinyInteger('reps')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->unsignedTinyInteger('order');

            // Primary key to ensure an exercise appears only once in a specific order per workout
            $table->primary(['workout_id', 'exercise_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercise');
    }
};
