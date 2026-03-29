<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free-plan',
                'description' => 'Start your fitness journey with basic features and limited content access.',
                'price' => 0.00,
                'interval' => 'month',
                'type' => 'free',
                'stripe_plan_id' => null,
                'features' => [
                    'Access to basic workouts',
                    'Limited meal plans',
                    'Basic progress tracking',
                    'Community support'
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Basic Plan',
                'slug' => 'basic-plan',
                'description' => 'Perfect for beginners with expanded access to workouts and nutrition plans.',
                'price' => 9.99,
                'interval' => 'month',
                'type' => 'basic',
                'stripe_plan_id' => 'price_basic_monthly', // Replace with actual Stripe Price ID
                'features' => [
                    'Access to all basic workouts',
                    'Full meal plan library',
                    'Progress tracking & analytics',
                    'Email support',
                    'Mobile app access'
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 2,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Premium Plan',
                'slug' => 'premium-plan',
                'description' => 'Complete fitness solution with personalized coaching and premium content.',
                'price' => 19.99,
                'interval' => 'month',
                'type' => 'premium',
                'stripe_plan_id' => 'price_premium_monthly', // Replace with actual Stripe Price ID
                'features' => [
                    'All Basic Plan features',
                    'Premium workout videos',
                    'Personalized meal plans',
                    'Coach consultations',
                    'Advanced analytics',
                    'Priority support',
                    'Exclusive content'
                ],
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 3,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Premium Annual',
                'slug' => 'premium-annual',
                'description' => 'Best value! Premium features with 2 months free when you pay annually.',
                'price' => 199.99,
                'interval' => 'year',
                'type' => 'premium',
                'stripe_plan_id' => 'price_premium_yearly', // Replace with actual Stripe Price ID
                'features' => [
                    'All Premium Plan features',
                    '2 months free (save $40)',
                    'Yearly progress reports',
                    'Exclusive annual content',
                    'Priority customer support'
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
