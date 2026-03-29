<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Core seeders - run first
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
        ]);

        // User data seeders
        $this->call([
            UserSeeder::class,
            UserProfileSeeder::class,
        ]);

        // Fitness content seeders
        $this->call([
            ExerciseSeeder::class,
            WorkoutSeeder::class,
        ]);

        // Nutrition content seeders
        $this->call([
            RecipeSeeder::class,
            MealPlanSeeder::class,
        ]);

        // Subscription and plan seeders
        $this->call([
            PlanSeeder::class,
            SubscriptionSeeder::class,
        ]);

        // E-commerce seeders
        $this->call([
            ProductCategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Coach and appointment seeders
        $this->call([
            CoachAvailabilitySeeder::class,
            AppointmentSeeder::class,
        ]);

        // Progress tracking seeders
        $this->call([
            UserMeasurementSeeder::class,
            ProgressJournalSeeder::class,
        ]);

        // Content seeders
        $this->call([
            ArticleSeeder::class,
        ]);

    }
}
