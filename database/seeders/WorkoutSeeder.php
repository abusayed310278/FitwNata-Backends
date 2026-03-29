<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workout;
use App\Models\Exercise;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workouts = [
            [
                'title' => 'Beginner Full Body Workout',
                'description' => 'A complete full-body workout perfect for beginners starting their fitness journey.',
                'level' => 'beginner',
                'duration' => 30,
                'type' => 'strength',
                'equipment' => 'none',
                'calories_burned' => 200,
                'is_premium' => false,
                'exercises' => ['Push-ups', 'Squats', 'Plank', 'Jumping Jacks'],
                'instructions' => 'Complete each exercise for 30 seconds with 15 seconds rest between exercises. Rest 2 minutes between rounds. Complete 3 rounds.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Upper Body Strength',
                'description' => 'Focused upper body workout to build strength in chest, back, shoulders, and arms.',
                'level' => 'intermediate',
                'duration' => 45,
                'type' => 'strength',
                'equipment' => 'dumbbells',
                'calories_burned' => 300,
                'is_premium' => false,
                'exercises' => ['Push-ups', 'Pull-ups', 'Dumbbell Bench Press', 'Bicep Curls'],
                'instructions' => 'Perform 3 sets of 8-12 reps for each exercise. Rest 60 seconds between sets.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Lower Body Power',
                'description' => 'Intensive lower body workout targeting legs and glutes for strength and power.',
                'level' => 'intermediate',
                'duration' => 40,
                'type' => 'strength',
                'equipment' => 'barbell',
                'calories_burned' => 350,
                'is_premium' => true,
                'exercises' => ['Squats', 'Lunges', 'Deadlifts', 'Calf Raises'],
                'instructions' => 'Perform 4 sets of 6-10 reps for each exercise. Rest 90 seconds between sets.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Core Crusher',
                'description' => 'Intense core workout to strengthen and define your abdominal muscles.',
                'level' => 'intermediate',
                'duration' => 25,
                'type' => 'strength',
                'equipment' => 'none',
                'calories_burned' => 180,
                'is_premium' => false,
                'exercises' => ['Plank', 'Crunches', 'Russian Twists', 'Mountain Climbers'],
                'instructions' => 'Perform each exercise for 45 seconds with 15 seconds rest. Complete 4 rounds.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'HIIT Cardio Blast',
                'description' => 'High-intensity interval training to boost cardiovascular fitness and burn calories.',
                'level' => 'advanced',
                'duration' => 20,
                'type' => 'cardio',
                'equipment' => 'none',
                'calories_burned' => 250,
                'is_premium' => true,
                'exercises' => ['Burpees', 'Mountain Climbers', 'Jumping Jacks', 'High Knees'],
                'instructions' => 'Work for 40 seconds, rest for 20 seconds. Complete 5 rounds with 2 minutes rest between rounds.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Morning Energy Boost',
                'description' => 'Quick morning routine to energize your body and prepare for the day ahead.',
                'level' => 'beginner',
                'duration' => 15,
                'type' => 'flexibility',
                'equipment' => 'none',
                'calories_burned' => 80,
                'is_premium' => false,
                'exercises' => ['Jumping Jacks', 'Push-ups', 'Squats', 'Plank'],
                'instructions' => 'Perform each exercise for 30 seconds with 10 seconds rest. Complete 2 rounds.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Advanced Full Body Challenge',
                'description' => 'Comprehensive advanced workout challenging all major muscle groups.',
                'level' => 'advanced',
                'duration' => 60,
                'type' => 'strength',
                'equipment' => 'dumbbells',
                'calories_burned' => 450,
                'is_premium' => true,
                'exercises' => ['Deadlifts', 'Pull-ups', 'Dumbbell Bench Press', 'Burpees'],
                'instructions' => 'Perform 5 sets of 5-8 reps for strength exercises, 3 sets of 10 for burpees. Rest 2-3 minutes between sets.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Flexibility and Mobility',
                'description' => 'Gentle workout focusing on improving flexibility and joint mobility.',
                'level' => 'beginner',
                'duration' => 35,
                'type' => 'flexibility',
                'equipment' => 'none',
                'calories_burned' => 120,
                'is_premium' => false,
                'exercises' => ['Plank', 'Lunges', 'Calf Raises', 'Russian Twists'],
                'instructions' => 'Hold each position for 30-60 seconds. Focus on controlled movements and breathing.',
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($workouts as $workoutData) {
            $exercises = $workoutData['exercises'];
            unset($workoutData['exercises']);

            $workout = Workout::updateOrCreate(
                ['title' => $workoutData['title']],
                $workoutData
            );

            // Attach exercises to workout if they exist
            if ($workout->wasRecentlyCreated || $workout->wasChanged()) {
                $exerciseData = [];
                $order = 1;
                foreach ($exercises as $exerciseName) {
                    $exercise = Exercise::where('name', $exerciseName)->first();
                    if ($exercise) {
                        $exerciseData[$exercise->id] = [
                            'order' => $order,
                            'sets' => 3,
                            'reps' => 12,
                            'duration_seconds' => null
                        ];
                        $order++;
                    }
                }

                if (!empty($exerciseData)) {
                    $workout->exercises()->sync($exerciseData);
                }
            }
        }
    }
}
