<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customer users to create profiles for
        $customers = User::role('customer')->get();

        $profileData = [
            [
                'username' => 'alice_fit',
                'gender' => 'female',
                'date_of_birth' => '1992-05-15',
                'health_conditions' => ['none'],
                'preferred_workout_types' => ['cardio', 'yoga', 'strength_training'],
                'training_location' => 'home',
                'fitness_goals' => ['weight_loss', 'improve_endurance'],
                'training_level' => 'beginner',
                'weekly_training_objective' => '3-4_times',
                'equipment_availability' => ['dumbbells', 'yoga_mat', 'resistance_bands'],
                'nutrition_knowledge_level' => 'beginner',
                'preferred_recipe_type' => 'quick_easy',
                'weight_kg' => 65.5,
                'height_cm' => 165,
                'profile_image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'bob_strong',
                'gender' => 'male',
                'date_of_birth' => '1988-03-22',
                'health_conditions' => ['none'],
                'preferred_workout_types' => ['strength_training', 'hiit', 'crossfit'],
                'training_location' => 'gym',
                'fitness_goals' => ['muscle_gain', 'increase_strength'],
                'training_level' => 'intermediate',
                'weekly_training_objective' => '5-6_times',
                'equipment_availability' => ['full_gym', 'barbells', 'machines'],
                'nutrition_knowledge_level' => 'intermediate',
                'preferred_recipe_type' => 'high_protein',
                'weight_kg' => 80.2,
                'height_cm' => 178,
                'profile_image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'carol_wellness',
                'gender' => 'female',
                'date_of_birth' => '1985-11-08',
                'health_conditions' => ['back_pain'],
                'preferred_workout_types' => ['yoga', 'pilates', 'walking'],
                'training_location' => 'home',
                'fitness_goals' => ['improve_flexibility', 'pain_management'],
                'training_level' => 'beginner',
                'weekly_training_objective' => '2-3_times',
                'equipment_availability' => ['yoga_mat', 'resistance_bands'],
                'nutrition_knowledge_level' => 'intermediate',
                'preferred_recipe_type' => 'healthy_balanced',
                'weight_kg' => 58.7,
                'height_cm' => 160,
                'profile_image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'daniel_runner',
                'gender' => 'male',
                'date_of_birth' => '1990-07-12',
                'health_conditions' => ['none'],
                'preferred_workout_types' => ['cardio', 'running', 'cycling'],
                'training_location' => 'outdoor',
                'fitness_goals' => ['improve_endurance', 'weight_loss'],
                'training_level' => 'intermediate',
                'weekly_training_objective' => '4-5_times',
                'equipment_availability' => ['running_shoes', 'bike'],
                'nutrition_knowledge_level' => 'beginner',
                'preferred_recipe_type' => 'energy_boosting',
                'weight_kg' => 72.1,
                'height_cm' => 175,
                'profile_image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'eva_dancer',
                'gender' => 'female',
                'date_of_birth' => '1995-01-28',
                'health_conditions' => ['none'],
                'preferred_workout_types' => ['dance', 'cardio', 'flexibility'],
                'training_location' => 'studio',
                'fitness_goals' => ['improve_flexibility', 'maintain_weight'],
                'training_level' => 'advanced',
                'weekly_training_objective' => '6-7_times',
                'equipment_availability' => ['dance_studio', 'mirrors'],
                'nutrition_knowledge_level' => 'advanced',
                'preferred_recipe_type' => 'balanced_macros',
                'weight_kg' => 55.8,
                'height_cm' => 168,
                'profile_image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'frank_powerlifter',
                'gender' => 'male',
                'date_of_birth' => '1987-09-14',
                'health_conditions' => ['knee_injury_history'],
                'preferred_workout_types' => ['powerlifting', 'strength_training'],
                'training_location' => 'gym',
                'fitness_goals' => ['increase_strength', 'muscle_gain'],
                'training_level' => 'advanced',
                'weekly_training_objective' => '5-6_times',
                'equipment_availability' => ['full_gym', 'powerlifting_equipment'],
                'nutrition_knowledge_level' => 'advanced',
                'preferred_recipe_type' => 'high_protein',
                'weight_kg' => 95.3,
                'height_cm' => 185,
                'profile_image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'grace_yogi',
                'gender' => 'female',
                'date_of_birth' => '1993-06-03',
                'health_conditions' => ['anxiety'],
                'preferred_workout_types' => ['yoga', 'meditation', 'pilates'],
                'training_location' => 'home',
                'fitness_goals' => ['stress_relief', 'improve_flexibility'],
                'training_level' => 'intermediate',
                'weekly_training_objective' => '4-5_times',
                'equipment_availability' => ['yoga_mat', 'blocks', 'straps'],
                'nutrition_knowledge_level' => 'intermediate',
                'preferred_recipe_type' => 'plant_based',
                'weight_kg' => 62.4,
                'height_cm' => 170,
                'profile_image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'henry_athlete',
                'gender' => 'male',
                'date_of_birth' => '1991-04-19',
                'health_conditions' => ['none'],
                'preferred_workout_types' => ['hiit', 'sports', 'functional_training'],
                'training_location' => 'gym',
                'fitness_goals' => ['athletic_performance', 'maintain_fitness'],
                'training_level' => 'advanced',
                'weekly_training_objective' => '6-7_times',
                'equipment_availability' => ['full_gym', 'sports_equipment'],
                'nutrition_knowledge_level' => 'advanced',
                'preferred_recipe_type' => 'performance_nutrition',
                'weight_kg' => 78.6,
                'height_cm' => 180,
                'profile_image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'isabella_wellness',
                'gender' => 'female',
                'date_of_birth' => '1989-12-05',
                'health_conditions' => ['diabetes_type2'],
                'preferred_workout_types' => ['walking', 'swimming', 'gentle_yoga'],
                'training_location' => 'gym',
                'fitness_goals' => ['health_management', 'weight_loss'],
                'training_level' => 'beginner',
                'weekly_training_objective' => '3-4_times',
                'equipment_availability' => ['pool_access', 'gym_membership'],
                'nutrition_knowledge_level' => 'advanced',
                'preferred_recipe_type' => 'diabetic_friendly',
                'weight_kg' => 68.9,
                'height_cm' => 163,
                'profile_image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
            [
                'username' => 'jack_crossfit',
                'gender' => 'male',
                'date_of_birth' => '1994-08-11',
                'health_conditions' => ['none'],
                'preferred_workout_types' => ['crossfit', 'hiit', 'olympic_lifting'],
                'training_location' => 'crossfit_box',
                'fitness_goals' => ['functional_fitness', 'competition_prep'],
                'training_level' => 'advanced',
                'weekly_training_objective' => '5-6_times',
                'equipment_availability' => ['crossfit_equipment', 'olympic_bars'],
                'nutrition_knowledge_level' => 'intermediate',
                'preferred_recipe_type' => 'performance_nutrition',
                'weight_kg' => 82.7,
                'height_cm' => 177,
                'profile_image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
            ],
        ];

        foreach ($customers as $index => $customer) {
            if (isset($profileData[$index])) {
                $profileInfo = $profileData[$index];
                $profileInfo['user_id'] = $customer->id;

                UserProfile::updateOrCreate(
                    ['user_id' => $customer->id],
                    $profileInfo
                );
            }
        }

        // Create profiles for some coaches and nutritionists too
        $coaches = User::role('coach')->get();
        $nutritionists = User::role('nutritionist')->get();

        // Basic profiles for coaches
        foreach ($coaches as $coach) {
            UserProfile::updateOrCreate(
                ['user_id' => $coach->id],
                [
                    'user_id' => $coach->id,
                    'username' => strtolower(str_replace(' ', '_', $coach->name . '_coach')),
                    'gender' => $coach->name === 'Sarah' ? 'female' : 'male',
                    'date_of_birth' => '1985-01-01',
                    'health_conditions' => ['none'],
                    'preferred_workout_types' => ['strength_training', 'hiit', 'functional_training'],
                    'training_location' => 'gym',
                    'fitness_goals' => ['maintain_fitness', 'help_others'],
                    'training_level' => 'expert',
                    'weekly_training_objective' => 'daily',
                    'equipment_availability' => ['full_gym', 'all_equipment'],
                    'nutrition_knowledge_level' => 'advanced',
                    'preferred_recipe_type' => 'balanced_macros',
                    'weight_kg' => $coach->name === 'Sarah' ? 58.0 : 80.0,
                    'height_cm' => $coach->name === 'Sarah' ? 165 : 178,
                    'profile_image_url' => $coach->name === 'Sarah' ?
                        'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80' :
                        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                ]
            );
        }

        // Basic profiles for nutritionists
        foreach ($nutritionists as $nutritionist) {
            UserProfile::updateOrCreate(
                ['user_id' => $nutritionist->id],
                [
                    'user_id' => $nutritionist->id,
                    'username' => strtolower(str_replace(' ', '_', $nutritionist->name . '_nutritionist')),
                    'gender' => $nutritionist->name === 'Emily' ? 'female' : 'male',
                    'date_of_birth' => '1980-01-01',
                    'health_conditions' => ['none'],
                    'preferred_workout_types' => ['yoga', 'walking', 'light_cardio'],
                    'training_location' => 'home',
                    'fitness_goals' => ['maintain_health', 'help_others'],
                    'training_level' => 'intermediate',
                    'weekly_training_objective' => '3-4_times',
                    'equipment_availability' => ['basic_equipment'],
                    'nutrition_knowledge_level' => 'expert',
                    'preferred_recipe_type' => 'scientifically_based',
                    'weight_kg' => $nutritionist->name === 'Emily' ? 62.0 : 75.0,
                    'height_cm' => $nutritionist->name === 'Emily' ? 168 : 175,
                    'profile_image_url' => $nutritionist->name === 'Emily' ?
                        'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80' :
                        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80',
                ]
            );
        }
    }
}
