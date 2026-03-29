<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CoachAvailabilities;
use Carbon\Carbon;

class CoachAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all coach users
        $coaches = User::role('coach')->get();

        if ($coaches->isEmpty()) {
            $this->command->warn('No coaches found. Please run UserSeeder first.');
            return;
        }

        foreach ($coaches as $coach) {
            // Create regular weekly availability for each coach
            $this->createWeeklyAvailability($coach);

            // Create some blocked time slots
            $this->createBlockedTimes($coach, $coaches);
        }
    }

    private function createWeeklyAvailability($coach)
    {
        // Different availability patterns for different coaches
        $availabilityPatterns = [
            'mike.coach@fitwnata.com' => [
                'Monday' => ['09:00', '17:00'],
                'Tuesday' => ['09:00', '17:00'],
                'Wednesday' => ['09:00', '17:00'],
                'Thursday' => ['09:00', '17:00'],
                'Friday' => ['09:00', '17:00'],
                'Saturday' => ['08:00', '14:00'],
            ],
            'sarah.coach@fitwnata.com' => [
                'Monday' => ['10:00', '18:00'],
                'Tuesday' => ['10:00', '18:00'],
                'Wednesday' => ['10:00', '18:00'],
                'Thursday' => ['10:00', '18:00'],
                'Friday' => ['10:00', '16:00'],
                'Saturday' => ['09:00', '13:00'],
                'Sunday' => ['10:00', '14:00'],
            ],
            'david.coach@fitwnata.com' => [
                'Monday' => ['08:00', '16:00'],
                'Tuesday' => ['08:00', '16:00'],
                'Wednesday' => ['08:00', '16:00'],
                'Thursday' => ['08:00', '16:00'],
                'Friday' => ['08:00', '16:00'],
                'Saturday' => ['10:00', '15:00'],
            ],
        ];

        $pattern = $availabilityPatterns[$coach->email] ?? $availabilityPatterns['mike.coach@fitwnata.com'];

        foreach ($pattern as $dayOfWeek => $times) {
            CoachAvailabilities::updateOrCreate(
                [
                    'coach_id' => $coach->id,
                    'day_of_week' => $dayOfWeek,
                    'is_blocked' => false,
                ],
                [
                    'coach_id' => $coach->id,
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $times[0],
                    'end_time' => $times[1],
                    'is_blocked' => false,
                    'notes' => 'Regular availability',
                ]
            );
        }
    }

    private function createBlockedTimes($coach, $coaches)
    {
        // Create some blocked dates for the next month
        $blockedDates = [
            // Vacation days
            now()->addDays(5)->format('Y-m-d'),
            now()->addDays(6)->format('Y-m-d'),
            now()->addDays(7)->format('Y-m-d'),

            // Conference/training days
            now()->addDays(12)->format('Y-m-d'),
            now()->addDays(19)->format('Y-m-d'),

            // Personal appointments
            now()->addDays(25)->format('Y-m-d'),
        ];

        foreach ($blockedDates as $index => $date) {
            // Only create some blocked dates for each coach to make it realistic
            if ($index % 2 === array_search($coach->email, ['mike.coach@fitwnata.com', 'sarah.coach@fitwnata.com', 'david.coach@fitwnata.com'])) {
                CoachAvailabilities::create([
                    'coach_id' => $coach->id,
                    'blocked_date' => $date,
                    'is_blocked' => true,
                    'notes' => $this->getBlockedReason($index),
                ]);
            }
        }

        // Create some specific time blocks (partial day blocks)
        $timeBlocks = [
            [
                'date' => now()->addDays(3)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '17:00',
                'reason' => 'Personal appointment'
            ],
            [
                'date' => now()->addDays(8)->format('Y-m-d'),
                'start_time' => '09:00',
                'end_time' => '12:00',
                'reason' => 'Training session'
            ],
            [
                'date' => now()->addDays(15)->format('Y-m-d'),
                'start_time' => '13:00',
                'end_time' => '15:00',
                'reason' => 'Medical appointment'
            ],
        ];

        foreach ($timeBlocks as $index => $block) {
            // Assign different blocks to different coaches
            if ($index % count($coaches) === array_search($coach, $coaches->toArray()) % count($coaches)) {
                CoachAvailabilities::create([
                    'coach_id' => $coach->id,
                    'blocked_date' => $block['date'],
                    'start_time' => $block['start_time'],
                    'end_time' => $block['end_time'],
                    'is_blocked' => true,
                    'notes' => $block['reason'],
                ]);
            }
        }
    }

    private function getBlockedReason($index)
    {
        $reasons = [
            'Vacation',
            'Personal time off',
            'Professional development',
            'Conference attendance',
            'Training workshop',
            'Family commitment'
        ];

        return $reasons[$index % count($reasons)];
    }
}
