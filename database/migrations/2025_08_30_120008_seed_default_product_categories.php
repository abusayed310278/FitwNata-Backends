<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default product categories
        $categories = [
            [
                'name' => 'Wellness',
                'slug' => 'wellness',
                'description' => 'Mental health products, stress management tools, sleep aids, and wellness accessories for overall well-being.',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fitness',
                'slug' => 'fitness',
                'description' => 'Fitness equipment, workout accessories, exercise gear, and training tools for physical fitness.',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nutrition',
                'slug' => 'nutrition',
                'description' => 'Nutritional supplements, healthy foods, meal planning tools, and dietary support products.',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Recovery',
                'slug' => 'recovery',
                'description' => 'Recovery tools, massage equipment, injury prevention products, and post-workout recovery aids.',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Only insert if categories don't already exist
        foreach ($categories as $category) {
            DB::table('product_categories')
                ->updateOrInsert(
                    ['slug' => $category['slug']],
                    $category
                );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_categories')
            ->whereIn('slug', ['wellness', 'fitness', 'nutrition', 'recovery'])
            ->delete();
    }
};
