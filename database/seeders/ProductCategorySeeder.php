<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Main Categories
            [
                'name' => 'Fitness Equipment',
                'slug' => 'fitness-equipment',
                'description' => 'Professional fitness and exercise equipment for home and gym use.',
                'image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Nutrition & Supplements',
                'slug' => 'nutrition-supplements',
                'description' => 'High-quality supplements and nutrition products to support your fitness goals.',
                'image' => 'https://images.unsplash.com/photo-1493770348161-369560ae357d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => null,
                'sort_order' => 2,
            ],
            [
                'name' => 'Apparel & Accessories',
                'slug' => 'apparel-accessories',
                'description' => 'Fitness apparel, accessories, and gear for optimal performance.',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => null,
                'sort_order' => 3,
            ],
            [
                'name' => 'Recovery & Wellness',
                'slug' => 'recovery-wellness',
                'description' => 'Products designed to enhance recovery and overall wellness.',
                'image' => 'https://images.unsplash.com/photo-1542766788-a2f588f43057?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => null,
                'sort_order' => 4,
            ],
        ];

        $parentCategories = [];

        // Create parent categories first
        foreach ($categories as $categoryData) {
            $category = ProductCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
            $parentCategories[$categoryData['slug']] = $category;
        }

        // Sub-categories for Fitness Equipment
        $fitnessSubCategories = [
            [
                'name' => 'Cardio Equipment',
                'slug' => 'cardio-equipment',
                'description' => 'Treadmills, bikes, ellipticals, and other cardio machines.',
                'image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['fitness-equipment']->id,
                'sort_order' => 1,
            ],
            [
                'name' => 'Strength Training',
                'slug' => 'strength-training',
                'description' => 'Weights, barbells, dumbbells, and resistance training equipment.',
                'image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['fitness-equipment']->id,
                'sort_order' => 2,
            ],
            [
                'name' => 'Yoga & Pilates',
                'slug' => 'yoga-pilates',
                'description' => 'Mats, blocks, straps, and other yoga and pilates accessories.',
                'image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['fitness-equipment']->id,
                'sort_order' => 3,
            ],
            [
                'name' => 'Home Gym',
                'slug' => 'home-gym',
                'description' => 'Complete home gym setups and multi-functional equipment.',
                'image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['fitness-equipment']->id,
                'sort_order' => 4,
            ],
        ];

        // Sub-categories for Nutrition & Supplements
        $nutritionSubCategories = [
            [
                'name' => 'Protein Supplements',
                'slug' => 'protein-supplements',
                'description' => 'Whey, casein, plant-based, and other protein powders.',
                'image' => 'https://images.unsplash.com/photo-1493770348161-369560ae357d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['nutrition-supplements']->id,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pre & Post Workout',
                'slug' => 'pre-post-workout',
                'description' => 'Energy boosters, recovery drinks, and workout enhancers.',
                'image' => 'https://images.unsplash.com/photo-1493770348161-369560ae357d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['nutrition-supplements']->id,
                'sort_order' => 2,
            ],
            [
                'name' => 'Vitamins & Minerals',
                'slug' => 'vitamins-minerals',
                'description' => 'Essential vitamins, minerals, and micronutrients.',
                'image' => 'https://images.unsplash.com/photo-1493770348161-369560ae357d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['nutrition-supplements']->id,
                'sort_order' => 3,
            ],
            [
                'name' => 'Health Foods',
                'slug' => 'health-foods',
                'description' => 'Organic foods, superfoods, and healthy snacks.',
                'image' => 'https://images.unsplash.com/photo-1493770348161-369560ae357d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['nutrition-supplements']->id,
                'sort_order' => 4,
            ],
        ];

        // Sub-categories for Apparel & Accessories
        $apparelSubCategories = [
            [
                'name' => 'Activewear',
                'slug' => 'activewear',
                'description' => 'Performance clothing for workouts and athletic activities.',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['apparel-accessories']->id,
                'sort_order' => 1,
            ],
            [
                'name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Athletic shoes, running shoes, and training footwear.',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['apparel-accessories']->id,
                'sort_order' => 2,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Gym bags, water bottles, gloves, and other fitness accessories.',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['apparel-accessories']->id,
                'sort_order' => 3,
            ],
            [
                'name' => 'Wearable Tech',
                'slug' => 'wearable-tech',
                'description' => 'Fitness trackers, smartwatches, and health monitoring devices.',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['apparel-accessories']->id,
                'sort_order' => 4,
            ],
        ];

        // Sub-categories for Recovery & Wellness
        $recoverySubCategories = [
            [
                'name' => 'Massage & Recovery',
                'slug' => 'massage-recovery',
                'description' => 'Massage tools, foam rollers, and recovery equipment.',
                'image' => 'https://images.unsplash.com/photo-1542766788-a2f588f43057?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['recovery-wellness']->id,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sleep & Relaxation',
                'slug' => 'sleep-relaxation',
                'description' => 'Products to improve sleep quality and relaxation.',
                'image' => 'https://images.unsplash.com/photo-1542766788-a2f588f43057?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['recovery-wellness']->id,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pain Relief',
                'slug' => 'pain-relief',
                'description' => 'Natural pain relief and injury prevention products.',
                'image' => 'https://images.unsplash.com/photo-1542766788-a2f588f43057?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['recovery-wellness']->id,
                'sort_order' => 3,
            ],
            [
                'name' => 'Mobility & Flexibility',
                'slug' => 'mobility-flexibility',
                'description' => 'Tools and products to improve mobility and flexibility.',
                'image' => 'https://images.unsplash.com/photo-1542766788-a2f588f43057?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'parent_id' => $parentCategories['recovery-wellness']->id,
                'sort_order' => 4,
            ],
        ];

        // Create all subcategories
        $allSubCategories = array_merge(
            $fitnessSubCategories,
            $nutritionSubCategories,
            $apparelSubCategories,
            $recoverySubCategories
        );

        foreach ($allSubCategories as $subCategoryData) {
            ProductCategory::updateOrCreate(
                ['slug' => $subCategoryData['slug']],
                $subCategoryData
            );
        }

        $this->command->info('Created ' . (count($categories) + count($allSubCategories)) . ' product categories');
    }
}
