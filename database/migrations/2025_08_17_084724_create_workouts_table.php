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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->unsignedSmallInteger('duration')->comment('Duration in minutes');
            $table->unsignedSmallInteger('duration_minutes')->nullable()->comment('Legacy field, kept for compatibility');
            $table->enum('type', ['strength', 'cardio', 'flexibility', 'hiit'])->default('strength');
            $table->string('equipment')->nullable()->comment('Required equipment');
            $table->unsignedSmallInteger('calories_burned')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->text('instructions')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
