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
        Schema::create('user_workout_exercise_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('user_workout_assignments')->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->unsignedInteger('planned_seconds')->nullable();
            $table->unsignedInteger('planned_reps')->nullable();
            $table->unsignedInteger('actual_seconds')->nullable();
            $table->unsignedInteger('actual_reps')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unique(['assignment_id','exercise_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_workout_exercise_logs');
    }
};
