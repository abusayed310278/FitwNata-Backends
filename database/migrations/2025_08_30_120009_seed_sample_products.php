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
        // Get category IDs
        $wellnessCategory = DB::table('product_categories')->where('slug', 'wellness')->first();
        $fitnessCategory = DB::table('product_categories')->where('slug', 'fitness')->first();
        $nutritionCategory = DB::table('product_categories')->where('slug', 'nutrition')->first();
        $recoveryCategory = DB::table('product_categories')->where('slug', 'recovery')->first();

        // Sample products data
        $products = [
            // Wellness Products
            [
                'name' => 'Meditation Cushion Premium',
                'slug' => 'meditation-cushion-premium',
                'description' => 'Premium meditation cushion made with organic cotton and buckwheat hull filling. Perfect for daily meditation practice and mindfulness sessions.',
                'short_description' => 'Premium organic meditation cushion for comfortable practice',
                'price' => 59.99,
                'sku' => 'WEL001',
                'stock_quantity' => 25,
                'category_id' => $wellnessCategory?->id,
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Premium Meditation Cushion - Organic Cotton',
                'meta_description' => 'Comfortable organic meditation cushion for mindfulness practice',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Essential Oil Diffuser',
                'slug' => 'essential-oil-diffuser',
                'description' => 'Ultrasonic aromatherapy diffuser with LED lights and timer settings. Create a calming atmosphere for relaxation and stress relief.',
                'short_description' => 'Ultrasonic aromatherapy diffuser with LED lights',
                'price' => 89.99,
                'sale_price' => 69.99,
                'sku' => 'WEL002',
                'stock_quantity' => 15,
                'category_id' => $wellnessCategory?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Fitness Products
            [
                'name' => 'Resistance Band Set',
                'slug' => 'resistance-band-set',
                'description' => 'Complete set of 5 resistance bands with varying resistance levels. Includes door anchor, handles, and ankle straps for full-body workouts.',
                'short_description' => 'Complete resistance band set for full-body workouts',
                'price' => 39.99,
                'sku' => 'FIT001',
                'stock_quantity' => 50,
                'category_id' => $fitnessCategory?->id,
                'is_featured' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yoga Mat Pro',
                'slug' => 'yoga-mat-pro',
                'description' => 'Professional-grade yoga mat with superior grip and cushioning. Non-slip surface perfect for all types of yoga and fitness exercises.',
                'short_description' => 'Professional yoga mat with superior grip',
                'price' => 79.99,
                'sku' => 'FIT002',
                'stock_quantity' => 30,
                'category_id' => $fitnessCategory?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Nutrition Products
            [
                'name' => 'Whey Protein Powder',
                'slug' => 'whey-protein-powder',
                'description' => 'High-quality whey protein isolate with 25g protein per serving. Available in vanilla, chocolate, and strawberry flavors.',
                'short_description' => 'High-quality whey protein isolate supplement',
                'price' => 49.99,
                'sku' => 'NUT001',
                'stock_quantity' => 40,
                'category_id' => $nutritionCategory?->id,
                'is_featured' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Omega-3 Fish Oil Capsules',
                'slug' => 'omega-3-fish-oil',
                'description' => 'Premium omega-3 fish oil supplement with EPA and DHA. Supports heart health, brain function, and joint mobility.',
                'short_description' => 'Premium omega-3 fish oil with EPA and DHA',
                'price' => 29.99,
                'sku' => 'NUT002',
                'stock_quantity' => 60,
                'category_id' => $nutritionCategory?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Recovery Products
            [
                'name' => 'Foam Roller Professional',
                'slug' => 'foam-roller-professional',
                'description' => 'High-density foam roller for deep tissue massage and muscle recovery. Helps reduce muscle soreness and improve flexibility.',
                'short_description' => 'Professional foam roller for muscle recovery',
                'price' => 34.99,
                'sku' => 'REC001',
                'stock_quantity' => 35,
                'category_id' => $recoveryCategory?->id,
                'is_featured' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Muscle Relief Cream',
                'slug' => 'muscle-relief-cream',
                'description' => 'Natural muscle relief cream with arnica and menthol. Provides cooling relief for sore muscles and joints after workouts.',
                'short_description' => 'Natural muscle relief cream with arnica',
                'price' => 19.99,
                'sku' => 'REC002',
                'stock_quantity' => 45,
                'category_id' => $recoveryCategory?->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert sample products
        foreach ($products as $product) {
            if ($product['category_id']) {
                DB::table('products')->updateOrInsert(
                    ['sku' => $product['sku']],
                    $product
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $skus = ['WEL001', 'WEL002', 'FIT001', 'FIT002', 'NUT001', 'NUT002', 'REC001', 'REC002'];
        DB::table('products')->whereIn('sku', $skus)->delete();
    }
};
