<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserMeasurement;
use Carbon\Carbon;

class UserMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customer users
        $customers = User::role('customer')->get();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run UserSeeder first.');
            return;
        }

        foreach ($customers as $customer) {
            $this->createMeasurementsForUser($customer);
        }
    }

    private function createMeasurementsForUser($customer)
    {
        // Get user profile for baseline measurements
        $profile = $customer->profile;
        $baseWeight = $profile ? $profile->weight_kg : 70; // Default weight
        $baseHeight = $profile ? $profile->height_cm : 170; // Default height

        // Create measurements over the past 90 days
        for ($i = 90; $i >= 0; $i -= 7) { // Weekly measurements
            $date = now()->subDays($i);

            // Calculate progressive changes (some users lose weight, some gain muscle)
            $weeksPassed = ($i / 7);
            $weightChange = $this->calculateWeightProgression($customer->email, $weeksPassed);
            $currentWeight = $baseWeight + $weightChange;

            // Vary measurements slightly for realism
            $measurements = [
                'user_id' => $customer->id,
                'date' => $date->format('Y-m-d'),
                'weight' => round($currentWeight + (rand(-5, 5) / 10), 1), // ±0.5kg variation
                'height' => $baseHeight, // Height doesn't change
                'body_fat_percentage' => $this->calculateBodyFat($customer->email, $weeksPassed),
                'muscle_mass' => $this->calculateMuscleMass($customer->email, $weeksPassed, $currentWeight),
                'waist_circumference' => $this->calculateWaist($customer->email, $weeksPassed),
                'chest_circumference' => $this->calculateChest($customer->email, $weeksPassed),
                'arm_circumference' => $this->calculateArm($customer->email, $weeksPassed),
                'thigh_circumference' => $this->calculateThigh($customer->email, $weeksPassed),
                'notes' => $this->generateMeasurementNotes($weeksPassed),
                'created_at' => $date,
                'updated_at' => $date,
            ];

            UserMeasurement::create($measurements);
        }
    }

    private function calculateWeightProgression($email, $weeksPassed)
    {
        // Different users have different goals and progressions
        $progressionPatterns = [
            'alice@example.com' => -0.3, // Weight loss: -0.3kg per week
            'bob@example.com' => 0.2,    // Muscle gain: +0.2kg per week
            'carol@example.com' => -0.1, // Slow weight loss: -0.1kg per week
            'daniel@example.com' => -0.2, // Moderate weight loss: -0.2kg per week
            'eva@example.com' => 0.0,    // Maintenance: stable weight
            'frank@example.com' => 0.3,  // Bulking: +0.3kg per week
            'grace@example.com' => -0.1, // Gentle weight loss: -0.1kg per week
            'henry@example.com' => 0.1,  // Lean muscle gain: +0.1kg per week
            'isabella@example.com' => -0.2, // Weight loss for health: -0.2kg per week
            'jack@example.com' => 0.2,   // Muscle building: +0.2kg per week
        ];

        $weeklyChange = $progressionPatterns[$email] ?? 0;
        return $weeklyChange * $weeksPassed;
    }

    private function calculateBodyFat($email, $weeksPassed)
    {
        // Base body fat percentages (realistic for different users)
        $baseBF = [
            'alice@example.com' => 28,    // Female, starting higher
            'bob@example.com' => 18,      // Male, athletic
            'carol@example.com' => 32,    // Female, older
            'daniel@example.com' => 20,   // Male, runner
            'eva@example.com' => 22,      // Female, dancer
            'frank@example.com' => 15,    // Male, powerlifter
            'grace@example.com' => 25,    // Female, yoga
            'henry@example.com' => 16,    // Male, athlete
            'isabella@example.com' => 30, // Female, health issues
            'jack@example.com' => 17,     // Male, crossfit
        ];

        $base = $baseBF[$email] ?? 20;

        // Body fat changes based on training type
        $changePerWeek = [
            'alice@example.com' => -0.15,  // Losing fat
            'bob@example.com' => -0.05,    // Slight fat loss
            'carol@example.com' => -0.08,  // Gradual fat loss
            'daniel@example.com' => -0.10, // Running burns fat
            'eva@example.com' => 0,        // Maintaining
            'frank@example.com' => 0.02,   // Slight increase (bulking)
            'grace@example.com' => -0.05,  // Slow fat loss
            'henry@example.com' => -0.03,  // Maintaining low BF
            'isabella@example.com' => -0.12, // Health-focused fat loss
            'jack@example.com' => -0.05,   // CrossFit fat loss
        ];

        $weeklyChange = $changePerWeek[$email] ?? 0;
        $result = $base + ($weeklyChange * $weeksPassed);

        // Keep within realistic bounds
        return max(8, min(40, round($result, 1)));
    }

    private function calculateMuscleMass($email, $weeksPassed, $currentWeight)
    {
        // Base muscle mass as percentage of total weight
        $baseMusclePercentage = [
            'alice@example.com' => 35,    // Female, beginner
            'bob@example.com' => 45,      // Male, intermediate
            'carol@example.com' => 32,    // Female, older
            'daniel@example.com' => 42,   // Male, runner
            'eva@example.com' => 38,      // Female, dancer
            'frank@example.com' => 50,    // Male, powerlifter
            'grace@example.com' => 36,    // Female, yoga
            'henry@example.com' => 48,    // Male, athlete
            'isabella@example.com' => 33, // Female, health focus
            'jack@example.com' => 46,     // Male, crossfit
        ];

        $basePercentage = $baseMusclePercentage[$email] ?? 40;

        // Muscle gain rates (percentage increase per week)
        $muscleGainRate = [
            'alice@example.com' => 0.1,   // Beginner gains
            'bob@example.com' => 0.15,    // Good muscle building
            'carol@example.com' => 0.05,  // Slower gains
            'daniel@example.com' => 0.05, // Running focus
            'eva@example.com' => 0.08,    // Moderate gains
            'frank@example.com' => 0.2,   // Powerlifting gains
            'grace@example.com' => 0.06,  // Yoga/light gains
            'henry@example.com' => 0.12,  // Athletic gains
            'isabella@example.com' => 0.08, // Health gains
            'jack@example.com' => 0.15,   // CrossFit gains
        ];

        $gainRate = $muscleGainRate[$email] ?? 0.1;
        $musclePercentage = $basePercentage + ($gainRate * $weeksPassed);

        // Keep within realistic bounds (25-55% of body weight)
        $musclePercentage = max(25, min(55, $musclePercentage));

        return round(($currentWeight * $musclePercentage / 100), 1);
    }

    private function calculateWaist($email, $weeksPassed)
    {
        $baseWaist = [
            'alice@example.com' => 76,    // cm
            'bob@example.com' => 85,
            'carol@example.com' => 82,
            'daniel@example.com' => 80,
            'eva@example.com' => 68,
            'frank@example.com' => 95,
            'grace@example.com' => 72,
            'henry@example.com' => 78,
            'isabella@example.com' => 88,
            'jack@example.com' => 82,
        ];

        $base = $baseWaist[$email] ?? 80;

        // Waist changes (negative = smaller waist)
        $changePerWeek = [
            'alice@example.com' => -0.2,
            'bob@example.com' => -0.1,
            'carol@example.com' => -0.15,
            'daniel@example.com' => -0.18,
            'eva@example.com' => 0,
            'frank@example.com' => 0.1,
            'grace@example.com' => -0.08,
            'henry@example.com' => -0.05,
            'isabella@example.com' => -0.25,
            'jack@example.com' => -0.12,
        ];

        $weeklyChange = $changePerWeek[$email] ?? 0;
        return round($base + ($weeklyChange * $weeksPassed), 1);
    }

    private function calculateChest($email, $weeksPassed)
    {
        $baseChest = [
            'alice@example.com' => 88,    // cm
            'bob@example.com' => 102,
            'carol@example.com' => 95,
            'daniel@example.com' => 98,
            'eva@example.com' => 85,
            'frank@example.com' => 115,
            'grace@example.com' => 90,
            'henry@example.com' => 105,
            'isabella@example.com' => 100,
            'jack@example.com' => 108,
        ];

        $base = $baseChest[$email] ?? 95;

        // Chest changes (positive = muscle growth)
        $changePerWeek = [
            'alice@example.com' => 0.05,
            'bob@example.com' => 0.08,
            'carol@example.com' => 0.02,
            'daniel@example.com' => 0.03,
            'eva@example.com' => 0.04,
            'frank@example.com' => 0.12,
            'grace@example.com' => 0.03,
            'henry@example.com' => 0.06,
            'isabella@example.com' => 0.02,
            'jack@example.com' => 0.08,
        ];

        $weeklyChange = $changePerWeek[$email] ?? 0.05;
        return round($base + ($weeklyChange * $weeksPassed), 1);
    }

    private function calculateArm($email, $weeksPassed)
    {
        $baseArm = [
            'alice@example.com' => 26,    // cm
            'bob@example.com' => 35,
            'carol@example.com' => 28,
            'daniel@example.com' => 32,
            'eva@example.com' => 25,
            'frank@example.com' => 42,
            'grace@example.com' => 26,
            'henry@example.com' => 38,
            'isabella@example.com' => 30,
            'jack@example.com' => 37,
        ];

        $base = $baseArm[$email] ?? 30;

        // Arm changes
        $changePerWeek = [
            'alice@example.com' => 0.03,
            'bob@example.com' => 0.05,
            'carol@example.com' => 0.01,
            'daniel@example.com' => 0.02,
            'eva@example.com' => 0.02,
            'frank@example.com' => 0.08,
            'grace@example.com' => 0.02,
            'henry@example.com' => 0.04,
            'isabella@example.com' => 0.01,
            'jack@example.com' => 0.05,
        ];

        $weeklyChange = $changePerWeek[$email] ?? 0.03;
        return round($base + ($weeklyChange * $weeksPassed), 1);
    }

    private function calculateThigh($email, $weeksPassed)
    {
        $baseThigh = [
            'alice@example.com' => 52,    // cm
            'bob@example.com' => 58,
            'carol@example.com' => 55,
            'daniel@example.com' => 56,
            'eva@example.com' => 50,
            'frank@example.com' => 68,
            'grace@example.com' => 51,
            'henry@example.com' => 62,
            'isabella@example.com' => 58,
            'jack@example.com' => 60,
        ];

        $base = $baseThigh[$email] ?? 55;

        // Thigh changes
        $changePerWeek = [
            'alice@example.com' => 0.02,
            'bob@example.com' => 0.06,
            'carol@example.com' => 0.01,
            'daniel@example.com' => 0.04,
            'eva@example.com' => 0.03,
            'frank@example.com' => 0.1,
            'grace@example.com' => 0.02,
            'henry@example.com' => 0.05,
            'isabella@example.com' => 0.01,
            'jack@example.com' => 0.06,
        ];

        $weeklyChange = $changePerWeek[$email] ?? 0.04;
        return round($base + ($weeklyChange * $weeksPassed), 1);
    }

    private function generateMeasurementNotes($weeksPassed)
    {
        $notes = [
            'Feeling stronger and more confident',
            'Clothes fitting better this week',
            'Energy levels are improving',
            'Notice more muscle definition',
            'Weight training is showing results',
            'Cardio endurance has increased',
            'Recovery time between workouts is faster',
            'Sleep quality has improved',
            'Overall mood and motivation are better',
            'Measurements are trending in right direction',
            'Flexibility has increased noticeably',
            'Balance and coordination improving'
        ];

        // Return different notes based on progression
        if ($weeksPassed < 2) {
            return 'Starting measurements - baseline established';
        } elseif ($weeksPassed < 6) {
            return 'Early progress - ' . $notes[array_rand($notes)];
        } elseif ($weeksPassed < 12) {
            return 'Good momentum - ' . $notes[array_rand($notes)];
        } else {
            return 'Consistent progress - ' . $notes[array_rand($notes)];
        }
    }
}
