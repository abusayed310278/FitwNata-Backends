<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MealPlan;
use App\Models\Recipe;

class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mealPlans = [
            [
                'title' => '7-Day Beginner Healthy Eating',
                'description' => 'A simple and nutritious meal plan perfect for those starting their healthy eating journey.',
                'duration_days' => 7,
                'total_calories' => 1800,
                'difficulty' => 'easy',
                'goal' => 'general_health',
                'is_premium' => false,
                'recipes' => ['Protein Power Smoothie', 'Quinoa Buddha Bowl', 'Overnight Chia Pudding'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Weight Loss Kickstart',
                'description' => 'A 14-day meal plan designed to jumpstart your weight loss journey with balanced, low-calorie meals.',
                'duration_days' => 14,
                'total_calories' => 1500,
                'difficulty' => 'medium',
                'goal' => 'weight_loss',
                'is_premium' => true,
                'recipes' => ['Green Detox Smoothie', 'Mediterranean Chicken Wrap', 'Grilled Salmon with Vegetables'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Muscle Building Nutrition',
                'description' => 'High-protein meal plan to support muscle growth and strength training goals.',
                'duration_days' => 21,
                'total_calories' => 2200,
                'difficulty' => 'medium',
                'goal' => 'muscle_gain',
                'is_premium' => true,
                'recipes' => ['Protein Power Smoothie', 'Grilled Salmon with Vegetables', 'Stuffed Sweet Potato'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Plant-Based Power',
                'description' => 'Complete vegan meal plan providing all essential nutrients for optimal health.',
                'duration_days' => 10,
                'total_calories' => 1900,
                'difficulty' => 'medium',
                'goal' => 'general_health',
                'is_premium' => false,
                'recipes' => ['Quinoa Buddha Bowl', 'Green Detox Smoothie', 'Stuffed Sweet Potato', 'Overnight Chia Pudding'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Quick & Easy Meals',
                'description' => 'Fast and simple meal plan for busy lifestyles without compromising nutrition.',
                'duration_days' => 5,
                'total_calories' => 1700,
                'difficulty' => 'easy',
                'goal' => 'convenience',
                'is_premium' => false,
                'recipes' => ['Mediterranean Chicken Wrap', 'Energy Balls', 'Protein Power Smoothie'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Mediterranean Lifestyle',
                'description' => 'Heart-healthy Mediterranean diet plan rich in whole foods, healthy fats, and lean proteins.',
                'duration_days' => 28,
                'total_calories' => 2000,
                'difficulty' => 'medium',
                'goal' => 'heart_health',
                'is_premium' => true,
                'recipes' => ['Mediterranean Chicken Wrap', 'Grilled Salmon with Vegetables', 'Quinoa Buddha Bowl'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Detox & Reset',
                'description' => 'Clean eating plan to reset your system and boost energy levels.',
                'duration_days' => 7,
                'total_calories' => 1600,
                'difficulty' => 'medium',
                'goal' => 'detox',
                'is_premium' => true,
                'recipes' => ['Green Detox Smoothie', 'Quinoa Buddha Bowl', 'Stuffed Sweet Potato'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Family-Friendly Nutrition',
                'description' => 'Nutritious and delicious meals that the whole family will enjoy.',
                'duration_days' => 14,
                'total_calories' => 1850,
                'difficulty' => 'easy',
                'goal' => 'family',
                'is_premium' => false,
                'recipes' => ['Stuffed Sweet Potato', 'Grilled Salmon with Vegetables', 'Energy Balls'],
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($mealPlans as $mealPlanData) {
            $recipes = $mealPlanData['recipes'];
            unset($mealPlanData['recipes']);

            $mealPlan = MealPlan::updateOrCreate(
                ['title' => $mealPlanData['title']],
                $mealPlanData
            );

            // Attach recipes to meal plan if they exist
            if ($mealPlan->wasRecentlyCreated || $mealPlan->wasChanged()) {
                $recipeData = [];
                $mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
                $dayOfWeek = 1;

                foreach ($recipes as $index => $recipeTitle) {
                    $recipe = Recipe::where('title', $recipeTitle)->first();
                    if ($recipe) {
                        $mealType = $mealTypes[$index % count($mealTypes)];
                        $recipeData[$recipe->id] = [
                            'day_of_week' => $dayOfWeek,
                            'meal_type' => $mealType
                        ];

                        // Cycle through days for variety
                        if (($index + 1) % count($mealTypes) === 0) {
                            $dayOfWeek++;
                            if ($dayOfWeek > 7) $dayOfWeek = 1; // Reset to Monday
                        }
                    }
                }

                if (!empty($recipeData)) {
                    $mealPlan->recipes()->sync($recipeData);
                }
            }
        }
    }
}
