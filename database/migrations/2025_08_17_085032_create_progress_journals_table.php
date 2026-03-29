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
        Schema::create('progress_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('coach_id')->nullable()->constrained('users')->onDelete('cascade')->comment('Coach who made the entry (for coach notes)');
            $table->date('entry_date');
            $table->enum('entry_type', ['workout', 'nutrition', 'wellness', 'measurements', 'goals', 'coach_note'])->comment('Type of journal entry');
            $table->string('title')->nullable()->comment('Entry title');
            $table->text('content')->nullable()->comment('Main entry content');
            $table->unsignedTinyInteger('mood_rating')->nullable()->comment('Mood rating from 1 to 5');
            $table->unsignedTinyInteger('energy_level')->nullable()->comment('Energy level from 1 to 5');
            $table->text('notes')->nullable()->comment('Additional notes (legacy field)');
            $table->unsignedTinyInteger('mood')->nullable()->comment('Legacy mood field (1 to 5)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_journals');
    }
};
