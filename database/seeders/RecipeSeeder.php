<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipes = [
            [
                'title' => 'Protein Power Smoothie',
                'description' => 'A delicious high-protein smoothie perfect for post-workout recovery.',
                'prep_time' => 5,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 280,
                'protein' => 25,
                'carbs' => 30,
                'fat' => 8,
                'difficulty' => 'easy',
                'ingredients' => [
                    '1 scoop vanilla protein powder',
                    '1 banana',
                    '1 cup unsweetened almond milk',
                    '1 tbsp almond butter',
                    '1 tsp honey',
                    'Ice cubes'
                ],
                'instructions' => [
                    'Add all ingredients to a blender',
                    'Blend until smooth and creamy',
                    'Add ice cubes for desired consistency',
                    'Pour into glass and enjoy immediately'
                ],
                'tags' => ['protein', 'smoothie', 'post-workout', 'quick'],
                'is_premium' => false,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Quinoa Buddha Bowl',
                'description' => 'Nutritious and colorful bowl packed with quinoa, vegetables, and tahini dressing.',
                'prep_time' => 20,
                'cook_time' => 15,
                'servings' => 2,
                'calories' => 420,
                'protein' => 15,
                'carbs' => 58,
                'fat' => 16,
                'difficulty' => 'medium',
                'ingredients' => [
                    '1 cup quinoa',
                    '2 cups mixed greens',
                    '1 avocado, sliced',
                    '1 cup roasted chickpeas',
                    '1 bell pepper, sliced',
                    '1 cucumber, diced',
                    '2 tbsp tahini',
                    '1 lemon, juiced',
                    'Salt and pepper to taste'
                ],
                'instructions' => [
                    'Cook quinoa according to package instructions',
                    'Prepare all vegetables and set aside',
                    'Mix tahini with lemon juice, salt, and pepper for dressing',
                    'Assemble bowl with quinoa as base',
                    'Top with vegetables and chickpeas',
                    'Drizzle with tahini dressing'
                ],
                'tags' => ['vegan', 'healthy', 'quinoa', 'bowl', 'lunch'],
                'is_premium' => false,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Grilled Salmon with Vegetables',
                'description' => 'Perfectly grilled salmon with a medley of roasted seasonal vegetables.',
                'prep_time' => 15,
                'cook_time' => 25,
                'servings' => 4,
                'calories' => 380,
                'protein' => 35,
                'carbs' => 12,
                'fat' => 22,
                'difficulty' => 'medium',
                'ingredients' => [
                    '4 salmon fillets (6 oz each)',
                    '2 zucchini, sliced',
                    '1 bell pepper, chopped',
                    '1 red onion, sliced',
                    '2 tbsp olive oil',
                    '2 tsp garlic powder',
                    '1 tsp paprika',
                    'Salt and pepper to taste',
                    'Fresh herbs for garnish'
                ],
                'instructions' => [
                    'Preheat grill to medium-high heat',
                    'Season salmon with salt, pepper, and paprika',
                    'Toss vegetables with olive oil and garlic powder',
                    'Grill salmon for 4-5 minutes per side',
                    'Roast vegetables in oven at 400°F for 20 minutes',
                    'Serve salmon with roasted vegetables',
                    'Garnish with fresh herbs'
                ],
                'tags' => ['salmon', 'grilled', 'vegetables', 'dinner', 'high-protein'],
                'is_premium' => true,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Overnight Chia Pudding',
                'description' => 'Make-ahead breakfast loaded with omega-3s and fiber.',
                'prep_time' => 10,
                'cook_time' => 0,
                'servings' => 2,
                'calories' => 220,
                'protein' => 8,
                'carbs' => 25,
                'fat' => 12,
                'difficulty' => 'easy',
                'ingredients' => [
                    '1/4 cup chia seeds',
                    '1 cup coconut milk',
                    '2 tbsp maple syrup',
                    '1 tsp vanilla extract',
                    'Fresh berries for topping',
                    'Chopped nuts for topping'
                ],
                'instructions' => [
                    'Mix chia seeds, coconut milk, maple syrup, and vanilla',
                    'Stir well to prevent clumping',
                    'Refrigerate overnight or at least 4 hours',
                    'Stir before serving',
                    'Top with fresh berries and nuts'
                ],
                'tags' => ['chia', 'breakfast', 'make-ahead', 'healthy', 'vegan'],
                'is_premium' => false,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Mediterranean Chicken Wrap',
                'description' => 'Fresh and flavorful wrap with Mediterranean-inspired ingredients.',
                'prep_time' => 15,
                'cook_time' => 0,
                'servings' => 2,
                'calories' => 350,
                'protein' => 28,
                'carbs' => 32,
                'fat' => 14,
                'difficulty' => 'easy',
                'ingredients' => [
                    '2 large whole wheat tortillas',
                    '2 cups cooked chicken breast, sliced',
                    '1/2 cup hummus',
                    '1 cucumber, diced',
                    '2 tomatoes, diced',
                    '1/4 red onion, thinly sliced',
                    '1/4 cup feta cheese',
                    '2 tbsp olive oil',
                    'Fresh herbs (parsley, mint)'
                ],
                'instructions' => [
                    'Warm tortillas slightly',
                    'Spread hummus evenly on each tortilla',
                    'Layer chicken, cucumber, tomatoes, and onion',
                    'Sprinkle with feta cheese and herbs',
                    'Drizzle with olive oil',
                    'Roll tightly and slice in half'
                ],
                'tags' => ['mediterranean', 'wrap', 'chicken', 'lunch', 'quick'],
                'is_premium' => true,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Green Detox Smoothie',
                'description' => 'Nutrient-packed green smoothie to boost energy and support detoxification.',
                'prep_time' => 5,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 180,
                'protein' => 4,
                'carbs' => 38,
                'fat' => 3,
                'difficulty' => 'easy',
                'ingredients' => [
                    '2 cups fresh spinach',
                    '1 green apple, cored',
                    '1/2 avocado',
                    '1 cucumber',
                    '1 lemon, juiced',
                    '1 inch fresh ginger',
                    '1 cup coconut water',
                    'Ice cubes'
                ],
                'instructions' => [
                    'Add all ingredients to blender',
                    'Blend until completely smooth',
                    'Add ice for desired consistency',
                    'Strain if preferred',
                    'Serve immediately'
                ],
                'tags' => ['green', 'detox', 'smoothie', 'healthy', 'vegan'],
                'is_premium' => false,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Stuffed Sweet Potato',
                'description' => 'Roasted sweet potato stuffed with black beans, quinoa, and fresh vegetables.',
                'prep_time' => 10,
                'cook_time' => 45,
                'servings' => 4,
                'calories' => 320,
                'protein' => 12,
                'carbs' => 58,
                'fat' => 8,
                'difficulty' => 'medium',
                'ingredients' => [
                    '4 large sweet potatoes',
                    '1 cup cooked quinoa',
                    '1 can black beans, drained',
                    '1 bell pepper, diced',
                    '1/2 red onion, diced',
                    '1 avocado, diced',
                    '2 tbsp olive oil',
                    'Lime juice',
                    'Cilantro for garnish'
                ],
                'instructions' => [
                    'Bake sweet potatoes at 400°F for 45 minutes',
                    'Sauté onion and bell pepper in olive oil',
                    'Mix quinoa with black beans and sautéed vegetables',
                    'Cut open baked sweet potatoes',
                    'Stuff with quinoa mixture',
                    'Top with avocado and cilantro',
                    'Drizzle with lime juice'
                ],
                'tags' => ['sweet-potato', 'stuffed', 'vegan', 'dinner', 'healthy'],
                'is_premium' => true,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Energy Balls',
                'description' => 'No-bake energy balls perfect for a quick snack or pre-workout fuel.',
                'prep_time' => 15,
                'cook_time' => 0,
                'servings' => 12,
                'calories' => 85,
                'protein' => 3,
                'carbs' => 12,
                'fat' => 4,
                'difficulty' => 'easy',
                'ingredients' => [
                    '1 cup rolled oats',
                    '1/2 cup peanut butter',
                    '1/3 cup honey',
                    '1/3 cup mini chocolate chips',
                    '1/3 cup ground flaxseed',
                    '1 tsp vanilla extract',
                    'Pinch of salt'
                ],
                'instructions' => [
                    'Mix all ingredients in a large bowl',
                    'Stir until well combined',
                    'Refrigerate for 30 minutes',
                    'Roll mixture into 12 balls',
                    'Store in refrigerator up to 1 week'
                ],
                'tags' => ['energy-balls', 'snack', 'no-bake', 'healthy', 'pre-workout'],
                'is_premium' => false,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($recipes as $recipe) {
            Recipe::updateOrCreate(
                ['title' => $recipe['title']],
                $recipe
            );
        }
    }
}
