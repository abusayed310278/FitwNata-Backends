<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSpecification;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $categories = ProductCategory::all()->keyBy('slug');

        if ($categories->isEmpty()) {
            $this->command->warn('No product categories found. Please run ProductCategorySeeder first.');
            return;
        }

        $products = [
            // Cardio Equipment
            [
                'name' => 'ProFit Treadmill X500',
                'slug' => 'profit-treadmill-x500',
                'description' => 'Professional-grade treadmill with advanced features for home and commercial use.',
                'short_description' => 'High-performance treadmill with 15% incline and 12 preset programs.',
                'price' => 1299.99,
                'sale_price' => 999.99,
                'sku' => 'TRD-X500-001',
                'stock_quantity' => 25,
                'category_slug' => 'cardio-equipment',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1549576490-b0b4831ef60a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Motor' => '3.0 HP',
                    'Speed Range' => '0.5 - 12 mph',
                    'Incline' => '0 - 15%',
                    'Belt Size' => '20" x 55"',
                    'Weight Capacity' => '300 lbs',
                    'Programs' => '12 preset + manual'
                ],
                'is_featured' => true,
                'is_active' => true,
                'weight' => 180.00,
                'dimensions' => '72" L x 35" W x 55" H',
            ],
            [
                'name' => 'Elite Stationary Bike Pro',
                'slug' => 'elite-stationary-bike-pro',
                'description' => 'Commercial-quality stationary bike with magnetic resistance and digital display.',
                'short_description' => 'Smooth magnetic resistance bike with heart rate monitoring.',
                'price' => 799.99,
                'sale_price' => null,
                'sku' => 'BKE-PRO-002',
                'stock_quantity' => 18,
                'category_slug' => 'cardio-equipment',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1549576490-b0b4831ef60a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Resistance' => 'Magnetic',
                    'Flywheel' => '40 lbs',
                    'Seat Adjustment' => 'Vertical & Horizontal',
                    'Display' => 'LCD with heart rate',
                    'Weight Capacity' => '330 lbs',
                    'Warranty' => '2 years'
                ],
                'is_featured' => false,
                'is_active' => true,
                'weight' => 95.00,
                'dimensions' => '48" L x 22" W x 46" H',
            ],

            // Strength Training
            [
                'name' => 'Olympic Barbell Set 300lbs',
                'slug' => 'olympic-barbell-set-300lbs',
                'description' => 'Complete Olympic barbell set with 300 lbs of weight plates and collars.',
                'short_description' => 'Professional Olympic barbell with 300 lbs of bumper plates.',
                'price' => 599.99,
                'sale_price' => 449.99,
                'sku' => 'BAR-OLY-300',
                'stock_quantity' => 12,
                'category_slug' => 'strength-training',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1549576490-b0b4831ef60a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Bar Length' => '7 feet',
                    'Bar Weight' => '45 lbs',
                    'Plate Material' => 'Cast iron with rubber coating',
                    'Included Plates' => '2x45, 2x35, 2x25, 4x10, 4x5 lbs',
                    'Collars' => 'Olympic spring collars included',
                    'Total Weight' => '300 lbs'
                ],
                'is_featured' => true,
                'is_active' => true,
                'weight' => 300.00,
                'dimensions' => '84" L x 8" W x 8" H',
            ],
            [
                'name' => 'Adjustable Dumbbell Set 50lbs',
                'slug' => 'adjustable-dumbbell-set-50lbs',
                'description' => 'Space-saving adjustable dumbbells that replace multiple weights.',
                'short_description' => 'Quick-adjust dumbbells from 5-50 lbs each.',
                'price' => 299.99,
                'sale_price' => null,
                'sku' => 'DBL-ADJ-50',
                'stock_quantity' => 30,
                'category_slug' => 'strength-training',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Weight Range' => '5-50 lbs per dumbbell',
                    'Adjustment' => 'Dial system',
                    'Increments' => '5 lb increments',
                    'Material' => 'Steel with rubber coating',
                    'Handle' => 'Ergonomic grip',
                    'Space Required' => 'Minimal'
                ],
                'is_featured' => false,
                'is_active' => true,
                'weight' => 100.00,
                'dimensions' => '17" L x 8" W x 9" H',
            ],

            // Yoga & Pilates
            [
                'name' => 'Premium Yoga Mat 6mm',
                'slug' => 'premium-yoga-mat-6mm',
                'description' => 'Extra thick yoga mat with superior grip and cushioning for all types of yoga.',
                'short_description' => 'Non-slip 6mm thick yoga mat with carrying strap.',
                'price' => 39.99,
                'sale_price' => 29.99,
                'sku' => 'YGA-MAT-6MM',
                'stock_quantity' => 150,
                'category_slug' => 'yoga-pilates',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1549576490-b0b4831ef60a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Thickness' => '6mm',
                    'Material' => 'TPE (eco-friendly)',
                    'Size' => '72" x 24"',
                    'Weight' => '2.5 lbs',
                    'Non-slip' => 'Both sides',
                    'Carrying Strap' => 'Included'
                ],
                'is_featured' => false,
                'is_active' => true,
                'weight' => 2.50,
                'dimensions' => '72" L x 24" W x 0.25" H',
            ],

            // Protein Supplements
            [
                'name' => 'WheyMax Protein Powder 5lbs',
                'slug' => 'wheymax-protein-powder-5lbs',
                'description' => 'Premium whey protein isolate with 25g protein per serving in delicious flavors.',
                'short_description' => 'Fast-absorbing whey protein with 25g protein per scoop.',
                'price' => 79.99,
                'sale_price' => 59.99,
                'sku' => 'PRO-WHY-5LB',
                'stock_quantity' => 85,
                'category_slug' => 'protein-supplements',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Protein per Serving' => '25g',
                    'Servings per Container' => '74',
                    'Protein Source' => 'Whey Isolate',
                    'Flavors' => 'Chocolate, Vanilla, Strawberry',
                    'Added Sugars' => 'None',
                    'Mixability' => 'Excellent'
                ],
                'is_featured' => true,
                'is_active' => true,
                'weight' => 5.50,
                'dimensions' => '10" H x 7" W x 7" D',
            ],

            // Pre & Post Workout
            [
                'name' => 'EnergyBoost Pre-Workout',
                'slug' => 'energyboost-pre-workout',
                'description' => 'Clean energy pre-workout with natural caffeine and performance enhancers.',
                'short_description' => 'Natural pre-workout for sustained energy and focus.',
                'price' => 44.99,
                'sale_price' => null,
                'sku' => 'PRE-ENB-001',
                'stock_quantity' => 60,
                'category_slug' => 'pre-post-workout',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Caffeine' => '150mg from natural sources',
                    'Servings' => '30',
                    'Key Ingredients' => 'L-Citrulline, Beta-Alanine, Creatine',
                    'Flavors' => 'Berry Blast, Citrus Punch',
                    'Artificial Colors' => 'None',
                    'Sugar Free' => 'Yes'
                ],
                'is_featured' => false,
                'is_active' => true,
                'weight' => 1.20,
                'dimensions' => '6" H x 4" W x 4" D',
            ],

            // Activewear
            [
                'name' => 'Performance Athletic T-Shirt',
                'slug' => 'performance-athletic-t-shirt',
                'description' => 'Moisture-wicking athletic t-shirt designed for high-intensity workouts.',
                'short_description' => 'Breathable performance tee with moisture-wicking technology.',
                'price' => 24.99,
                'sale_price' => 19.99,
                'sku' => 'APP-TSH-001',
                'stock_quantity' => 200,
                'category_slug' => 'activewear',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Material' => '88% Polyester, 12% Spandex',
                    'Features' => 'Moisture-wicking, Quick-dry',
                    'Fit' => 'Athletic fit',
                    'Sizes' => 'XS, S, M, L, XL, XXL',
                    'Colors' => 'Black, Navy, Gray, Red',
                    'Care' => 'Machine washable'
                ],
                'is_featured' => false,
                'is_active' => true,
                'weight' => 0.30,
                'dimensions' => 'Varies by size',
            ],

            // Wearable Tech
            [
                'name' => 'FitTracker Pro Smartwatch',
                'slug' => 'fittracker-pro-smartwatch',
                'description' => 'Advanced fitness tracking smartwatch with heart rate, GPS, and workout modes.',
                'short_description' => 'Multi-sport smartwatch with health monitoring features.',
                'price' => 249.99,
                'sale_price' => 199.99,
                'sku' => 'WRB-FTP-001',
                'stock_quantity' => 45,
                'category_slug' => 'wearable-tech',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1549576490-b0b4831ef60a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Display' => '1.3" AMOLED',
                    'Battery Life' => 'Up to 14 days',
                    'Water Resistance' => '5ATM',
                    'GPS' => 'Built-in',
                    'Heart Rate' => '24/7 monitoring',
                    'Workout Modes' => '100+'
                ],
                'is_featured' => true,
                'is_active' => true,
                'weight' => 0.15,
                'dimensions' => '1.8" x 1.8" x 0.5"',
            ],

            // Massage & Recovery
            [
                'name' => 'Deep Tissue Massage Gun',
                'slug' => 'deep-tissue-massage-gun',
                'description' => 'Professional-grade percussion massage device for muscle recovery and pain relief.',
                'short_description' => 'Powerful massage gun with multiple attachments and speeds.',
                'price' => 149.99,
                'sale_price' => 119.99,
                'sku' => 'REC-MSG-001',
                'stock_quantity' => 35,
                'category_slug' => 'massage-recovery',
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80'
                ],
                'specifications' => [
                    'Speed Settings' => '5 levels',
                    'Attachments' => '6 interchangeable heads',
                    'Battery Life' => 'Up to 6 hours',
                    'Noise Level' => 'Ultra-quiet (<45dB)',
                    'Weight' => '2.2 lbs',
                    'Warranty' => '1 year'
                ],
                'is_featured' => false,
                'is_active' => true,
                'weight' => 2.20,
                'dimensions' => '9" L x 7" W x 3" H',
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            $specifications = $productData['specifications'] ?? [];
            unset($productData['category_slug']);
            unset($productData['specifications']);

            $category = $categories->get($categorySlug);
            if (!$category) {
                $this->command->warn("Category not found: {$categorySlug}");
                continue;
            }

            $productData['category_id'] = $category->id;

            // Create or update the product
            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );

            // Handle specifications
            if (!empty($specifications)) {
                // Delete existing specifications for this product
                ProductSpecification::where('product_id', $product->id)->delete();

                // Create new specifications
                $specs = [];
                $sortOrder = 0;
                foreach ($specifications as $key => $value) {
                    $specs[] = [
                        'product_id' => $product->id,
                        'key' => $key,
                        'value' => $value,
                        'sort_order' => $sortOrder++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($specs)) {
                    ProductSpecification::insert($specs);
                }
            }
        }

        $this->command->info('Created ' . count($products) . ' products');
    }
}
