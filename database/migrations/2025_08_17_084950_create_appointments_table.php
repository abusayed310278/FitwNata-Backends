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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('Client')->constrained('users')->onDelete('cascade');
            $table->foreignId('coach_id')->nullable()->comment('Coach')->constrained('users')->onDelete('cascade');
            $table->foreignId('nutritionist_id')->nullable()->comment('Nutritionist')->constrained('users')->onDelete('cascade');
            $table->string('appointment_type');
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->enum('status', ['pending', 'confirmed', 'rescheduled', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
