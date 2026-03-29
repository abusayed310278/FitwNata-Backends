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
        Schema::table('workouts', function (Blueprint $table) {
            $table->text('fitness_goals')->nullable()->after('tips');
            $table->text('training_location')->nullable()->after('fitness_goals');
            $table->text('health_conditions')->nullable()->after('training_location');
            $table->text('gender_preference')->nullable()->after('health_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropColumn([
                'fitness_goals',
                'training_location',
                'health_conditions',
                'gender_preference'
            ]);
        });
    }
};
