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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('muscle_group')->nullable()->comment('e.g., chest, back, legs, arms, core, cardio');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('equipment')->nullable()->comment('e.g., dumbbells, barbell, none');
            $table->text('instructions');
            $table->string('video_url')->nullable();
            $table->text('tips')->nullable();
            $table->string('equipment_needed')->nullable()->comment('e.g., Dumbbells, Yoga Mat, None');
            $table->decimal('calories_per_rep_or_second', 8, 2)->nullable();
            $table->boolean('status')->default(1)->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
