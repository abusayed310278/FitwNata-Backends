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
        Schema::create('user_workout_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('workout_id')->constrained();

            $table->date('scheduled_for')->index();
            $table->unsignedTinyInteger('sequence')->default(1);

            $table->string('status')->default('pending'); // pending, in_progress, completed, skipped
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('assigned_by')->nullable()->constrained('users');
            $table->string('source')->default('auto');

            // (We’ll add progress fields in a later step)
            // $table->unsignedInteger('total_seconds')->default(0);
            // $table->unsignedInteger('completed_seconds')->default(0);
            // $table->unsignedSmallInteger('progress_percent')->default(0);
            // $table->unsignedBigInteger('current_exercise_id')->nullable();
            $table->unique(['user_id','workout_id','scheduled_for'], 'uniq_user_day_workout');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_workout_assignments');
    }
};
