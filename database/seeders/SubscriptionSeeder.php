<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and plans
        $customers = User::role('customer')->get();
        $plans = Plan::all();

        if ($customers->isEmpty() || $plans->isEmpty()) {
            $this->command->warn('No customers or plans found. Please run UserSeeder and PlanSeeder first.');
            return;
        }

        $freePlan = $plans->where('name', 'Free Plan')->first();
        $basicPlan = $plans->where('name', 'Basic Plan')->first();
        $premiumMonthly = $plans->where('name', 'Premium Plan')->first();
        $premiumAnnual = $plans->where('name', 'Premium Annual')->first();

        $subscriptions = [
            // Free plan users
            [
                'user_email' => 'alice@example.com',
                'plan' => $freePlan,
                'stripe_status' => 'active',
                'stripe_price' => null,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ],
            [
                'user_email' => 'daniel@example.com',
                'plan' => $freePlan,
                'stripe_status' => 'active',
                'stripe_price' => null,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ],
            [
                'user_email' => 'grace@example.com',
                'plan' => $freePlan,
                'stripe_status' => 'active',
                'stripe_price' => null,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ],

            // Basic plan users
            [
                'user_email' => 'bob@example.com',
                'plan' => $basicPlan,
                'stripe_status' => 'active',
                'stripe_price' => 'price_basic_monthly',
                'quantity' => 1,
                'trial_ends_at' => now()->addDays(7),
                'ends_at' => now()->addMonth(),
            ],
            [
                'user_email' => 'carol@example.com',
                'plan' => $basicPlan,
                'stripe_status' => 'active',
                'stripe_price' => 'price_basic_monthly',
                'quantity' => 1,
                'trial_ends_at' => now()->subDays(3),
                'ends_at' => now()->addDays(25),
            ],

            // Premium Monthly users
            [
                'user_email' => 'eva@example.com',
                'plan' => $premiumMonthly,
                'stripe_status' => 'active',
                'stripe_price' => 'price_premium_monthly',
                'quantity' => 1,
                'trial_ends_at' => now()->subDays(10),
                'ends_at' => now()->addDays(20),
            ],
            [
                'user_email' => 'henry@example.com',
                'plan' => $premiumMonthly,
                'stripe_status' => 'active',
                'stripe_price' => 'price_premium_monthly',
                'quantity' => 1,
                'trial_ends_at' => now()->subDays(5),
                'ends_at' => now()->addDays(25),
            ],
            [
                'user_email' => 'jack@example.com',
                'plan' => $premiumMonthly,
                'stripe_status' => 'active',
                'stripe_price' => 'price_premium_monthly',
                'quantity' => 1,
                'trial_ends_at' => now()->subDays(15),
                'ends_at' => now()->addDays(15),
            ],

            // Premium Annual users
            [
                'user_email' => 'frank@example.com',
                'plan' => $premiumAnnual,
                'stripe_status' => 'active',
                'stripe_price' => 'price_premium_annual',
                'quantity' => 1,
                'trial_ends_at' => now()->subDays(20),
                'ends_at' => now()->addMonths(11),
            ],
            [
                'user_email' => 'isabella@example.com',
                'plan' => $premiumAnnual,
                'stripe_status' => 'active',
                'stripe_price' => 'price_premium_annual',
                'quantity' => 1,
                'trial_ends_at' => now()->subDays(30),
                'ends_at' => now()->addMonths(10),
            ],
        ];

        foreach ($subscriptions as $subscriptionData) {
            $user = User::where('email', $subscriptionData['user_email'])->first();

            if ($user && $subscriptionData['plan']) {
                $subscription = Subscription::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'plan_id' => $subscriptionData['plan']->id
                    ],
                    [
                        'user_id' => $user->id,
                        'plan_id' => $subscriptionData['plan']->id,
                        'stripe_id' => 'sub_' . strtoupper(substr(md5($user->email . $subscriptionData['plan']->id), 0, 24)),
                        'stripe_status' => $subscriptionData['stripe_status'],
                        'stripe_price' => $subscriptionData['stripe_price'],
                        'quantity' => $subscriptionData['quantity'],
                        'trial_ends_at' => $subscriptionData['trial_ends_at'],
                        'ends_at' => $subscriptionData['ends_at'],
                    ]
                );

                $this->command->info("Created subscription for {$user->email} - {$subscriptionData['plan']->name}");
            }
        }

        // Create some cancelled/expired subscriptions for testing
        $cancelledSubscriptions = [
            [
                'user_email' => 'alice@example.com',
                'plan' => $basicPlan,
                'stripe_status' => 'cancelled',
                'stripe_price' => 'price_basic_monthly',
                'ends_at' => now()->subDays(5),
            ],
        ];

        foreach ($cancelledSubscriptions as $cancelledData) {
            $user = User::where('email', $cancelledData['user_email'])->first();

            if ($user && $cancelledData['plan']) {
                Subscription::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'plan_id' => $cancelledData['plan']->id,
                        'stripe_status' => $cancelledData['stripe_status'],
                    ],
                    [
                        'user_id' => $user->id,
                        'plan_id' => $cancelledData['plan']->id,
                        'stripe_id' => 'sub_cancelled_' . strtoupper(substr(md5($user->email . 'cancelled' . time()), 0, 20)),
                        'stripe_status' => $cancelledData['stripe_status'],
                        'stripe_price' => $cancelledData['stripe_price'],
                        'quantity' => 1,
                        'trial_ends_at' => now()->subDays(15),
                        'ends_at' => $cancelledData['ends_at'],
                    ]
                );

                $this->command->info("Created cancelled subscription for {$user->email}");
            }
        }
    }
}
