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
        Schema::create('user_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('weight', 5, 1)->nullable()->comment('Weight in kg');
            $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
            $table->decimal('body_fat_percentage', 4, 1)->nullable()->comment('Body fat percentage');
            $table->decimal('muscle_mass', 5, 1)->nullable()->comment('Muscle mass in kg');
            $table->decimal('waist_circumference', 5, 1)->nullable()->comment('Waist circumference in cm');
            $table->decimal('chest_circumference', 5, 1)->nullable()->comment('Chest circumference in cm');
            $table->decimal('arm_circumference', 4, 1)->nullable()->comment('Arm circumference in cm');
            $table->decimal('thigh_circumference', 4, 1)->nullable()->comment('Thigh circumference in cm');
            $table->text('notes')->nullable()->comment('Additional notes about measurements');
            $table->decimal('weight_kg', 5, 2)->nullable()->comment('Legacy weight field for backward compatibility');
            $table->decimal('bmi', 4, 2)->nullable()->comment('Body Mass Index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_measurements');
    }
};
