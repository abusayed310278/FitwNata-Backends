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
        Schema::create('coach_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->string('day_of_week')->nullable()->comment('Day name: Monday, Tuesday, etc. OR null for specific dates');
            $table->date('blocked_date')->nullable()->comment('Specific date for blocked time');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_blocked')->default(false)->comment('True for blocked/unavailable time');
            $table->text('notes')->nullable()->comment('Reason for availability or blocking');

            // Allow multiple entries for the same coach on the same day for different time slots
            $table->index(['coach_id', 'day_of_week']);
            $table->index(['coach_id', 'blocked_date']);
            $table->index(['coach_id', 'is_blocked']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coach_availabilities');
    }
};
