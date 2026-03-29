<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // Upper Body Exercises
            [
                'name' => 'Push-ups',
                'description' => 'Classic bodyweight exercise for chest, shoulders, and triceps',
                'muscle_group' => 'chest',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Start in plank position, lower body until chest nearly touches floor, push back up',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Pull-ups',
                'description' => 'Upper body pulling exercise targeting back and biceps',
                'muscle_group' => 'back',
                'difficulty' => 'intermediate',
                'equipment' => 'pull-up bar',
                'instructions' => 'Hang from bar with arms extended, pull body up until chin passes bar',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Dumbbell Bench Press',
                'description' => 'Chest exercise using dumbbells for resistance',
                'muscle_group' => 'chest',
                'difficulty' => 'intermediate',
                'equipment' => 'dumbbells',
                'instructions' => 'Lie on bench, press dumbbells from chest level to full arm extension',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Bicep Curls',
                'description' => 'Isolation exercise for bicep muscles',
                'muscle_group' => 'arms',
                'difficulty' => 'beginner',
                'equipment' => 'dumbbells',
                'instructions' => 'Hold dumbbells at sides, curl weights up by flexing biceps',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],

            // Lower Body Exercises
            [
                'name' => 'Squats',
                'description' => 'Fundamental lower body exercise for legs and glutes',
                'muscle_group' => 'legs',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Stand with feet shoulder-width apart, lower hips back and down, return to standing',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Lunges',
                'description' => 'Unilateral leg exercise for strength and balance',
                'muscle_group' => 'legs',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Step forward into lunge position, lower back knee toward ground, return to start',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Deadlifts',
                'description' => 'Compound exercise targeting posterior chain',
                'muscle_group' => 'legs',
                'difficulty' => 'advanced',
                'equipment' => 'barbell',
                'instructions' => 'Stand with barbell over feet, hinge at hips to lower bar, drive hips forward to stand',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Calf Raises',
                'description' => 'Isolation exercise for calf muscles',
                'muscle_group' => 'legs',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Stand on balls of feet, raise heels as high as possible, lower slowly',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],

            // Core Exercises
            [
                'name' => 'Plank',
                'description' => 'Isometric core strengthening exercise',
                'muscle_group' => 'core',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Hold push-up position with body in straight line from head to heels',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Crunches',
                'description' => 'Traditional abdominal exercise',
                'muscle_group' => 'core',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Lie on back, hands behind head, lift shoulders off ground by contracting abs',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Russian Twists',
                'description' => 'Rotational core exercise',
                'muscle_group' => 'core',
                'difficulty' => 'intermediate',
                'equipment' => 'none',
                'instructions' => 'Sit with knees bent, lean back slightly, rotate torso side to side',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Mountain Climbers',
                'description' => 'Dynamic core and cardio exercise',
                'muscle_group' => 'core',
                'difficulty' => 'intermediate',
                'equipment' => 'none',
                'instructions' => 'Start in plank, alternate bringing knees to chest in running motion',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],

            // Cardio Exercises
            [
                'name' => 'Jumping Jacks',
                'description' => 'Full body cardio exercise',
                'muscle_group' => 'cardio',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Jump while spreading legs and raising arms overhead, return to start',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Burpees',
                'description' => 'High-intensity full body exercise',
                'muscle_group' => 'cardio',
                'difficulty' => 'advanced',
                'equipment' => 'none',
                'instructions' => 'From standing, drop to squat, jump back to plank, do push-up, jump feet to squat, jump up',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'High Knees',
                'description' => 'Cardio exercise focusing on leg drive',
                'muscle_group' => 'cardio',
                'difficulty' => 'beginner',
                'equipment' => 'none',
                'instructions' => 'Run in place bringing knees up toward chest as high as possible',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($exercises as $exercise) {
            Exercise::updateOrCreate(
                ['name' => $exercise['name']],
                $exercise
            );
        }
    }
}
