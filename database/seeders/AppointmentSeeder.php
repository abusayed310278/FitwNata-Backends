<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get coaches and customers
        $coaches = User::role('coach')->get();
        $nutritionists = User::role('nutritionist')->get();
        $customers = User::role('customer')->get();

        if ($coaches->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('No coaches or customers found. Please run UserSeeder first.');
            return;
        }

        $appointments = [
            // Upcoming appointments
            [
                'customer_email' => 'alice@example.com',
                'coach_email' => 'mike.coach@fitwnata.com',
                'appointment_type' => 'fitness_consultation',
                'scheduled_at' => now()->addDays(2)->setTime(10, 0),
                'status' => 'confirmed',
                'notes' => 'Initial fitness assessment and goal setting session',
                'duration_minutes' => 60,
            ],
            [
                'customer_email' => 'bob@example.com',
                'coach_email' => 'sarah.coach@fitwnata.com',
                'appointment_type' => 'personal_training',
                'scheduled_at' => now()->addDays(1)->setTime(14, 30),
                'status' => 'confirmed',
                'notes' => 'Upper body strength training session',
                'duration_minutes' => 45,
            ],
            [
                'customer_email' => 'carol@example.com',
                'nutritionist_email' => 'emily.nutritionist@fitwnata.com',
                'appointment_type' => 'nutrition_consultation',
                'scheduled_at' => now()->addDays(3)->setTime(11, 0),
                'status' => 'confirmed',
                'notes' => 'Dietary plan review and adjustments for back pain management',
                'duration_minutes' => 45,
            ],
            [
                'customer_email' => 'daniel@example.com',
                'coach_email' => 'david.coach@fitwnata.com',
                'appointment_type' => 'fitness_consultation',
                'scheduled_at' => now()->addDays(4)->setTime(9, 0),
                'status' => 'pending',
                'notes' => 'Running technique analysis and training plan development',
                'duration_minutes' => 60,
            ],
            [
                'customer_email' => 'eva@example.com',
                'coach_email' => 'mike.coach@fitwnata.com',
                'appointment_type' => 'personal_training',
                'scheduled_at' => now()->addDays(5)->setTime(16, 0),
                'status' => 'confirmed',
                'notes' => 'Dance-inspired cardio workout session',
                'duration_minutes' => 45,
            ],
            [
                'customer_email' => 'frank@example.com',
                'nutritionist_email' => 'robert.nutritionist@fitwnata.com',
                'appointment_type' => 'nutrition_consultation',
                'scheduled_at' => now()->addDays(6)->setTime(13, 30),
                'status' => 'confirmed',
                'notes' => 'Powerlifting nutrition optimization',
                'duration_minutes' => 60,
            ],
            [
                'customer_email' => 'grace@example.com',
                'coach_email' => 'sarah.coach@fitwnata.com',
                'appointment_type' => 'personal_training',
                'scheduled_at' => now()->addDays(7)->setTime(10, 30),
                'status' => 'pending',
                'notes' => 'Yoga and mindfulness session',
                'duration_minutes' => 60,
            ],

            // Past completed appointments
            [
                'customer_email' => 'henry@example.com',
                'coach_email' => 'david.coach@fitwnata.com',
                'appointment_type' => 'fitness_consultation',
                'scheduled_at' => now()->subDays(3)->setTime(15, 0),
                'status' => 'completed',
                'notes' => 'Athletic performance assessment completed successfully',
                'duration_minutes' => 60,
            ],
            [
                'customer_email' => 'isabella@example.com',
                'nutritionist_email' => 'emily.nutritionist@fitwnata.com',
                'appointment_type' => 'nutrition_consultation',
                'scheduled_at' => now()->subDays(5)->setTime(12, 0),
                'status' => 'completed',
                'notes' => 'Diabetic-friendly meal plan created and discussed',
                'duration_minutes' => 45,
            ],
            [
                'customer_email' => 'jack@example.com',
                'coach_email' => 'mike.coach@fitwnata.com',
                'appointment_type' => 'personal_training',
                'scheduled_at' => now()->subDays(1)->setTime(8, 0),
                'status' => 'completed',
                'notes' => 'CrossFit technique review and programming',
                'duration_minutes' => 45,
            ],
            [
                'customer_email' => 'alice@example.com',
                'coach_email' => 'sarah.coach@fitwnata.com',
                'appointment_type' => 'follow_up',
                'scheduled_at' => now()->subDays(7)->setTime(11, 30),
                'status' => 'completed',
                'notes' => 'Progress check and program adjustments made',
                'duration_minutes' => 30,
            ],

            // Cancelled appointments
            [
                'customer_email' => 'bob@example.com',
                'nutritionist_email' => 'robert.nutritionist@fitwnata.com',
                'appointment_type' => 'nutrition_consultation',
                'scheduled_at' => now()->subDays(2)->setTime(14, 0),
                'status' => 'cancelled',
                'notes' => 'Cancelled due to client emergency',
                'duration_minutes' => 45,
            ],
            [
                'customer_email' => 'carol@example.com',
                'coach_email' => 'david.coach@fitwnata.com',
                'appointment_type' => 'personal_training',
                'scheduled_at' => now()->addDays(1)->setTime(17, 0),
                'status' => 'cancelled',
                'notes' => 'Cancelled due to coach illness',
                'duration_minutes' => 45,
            ],

            // No-show appointments
            [
                'customer_email' => 'daniel@example.com',
                'coach_email' => 'sarah.coach@fitwnata.com',
                'appointment_type' => 'personal_training',
                'scheduled_at' => now()->subDays(4)->setTime(16, 30),
                'status' => 'no_show',
                'notes' => 'Client did not show up for scheduled session',
                'duration_minutes' => 45,
            ],

            // Rescheduled appointments
            [
                'customer_email' => 'eva@example.com',
                'nutritionist_email' => 'emily.nutritionist@fitwnata.com',
                'appointment_type' => 'nutrition_consultation',
                'scheduled_at' => now()->addDays(8)->setTime(15, 30),
                'status' => 'rescheduled',
                'notes' => 'Moved from original time due to client work conflict',
                'duration_minutes' => 45,
            ],
        ];

        foreach ($appointments as $appointmentData) {
            // Find customer
            $customer = User::where('email', $appointmentData['customer_email'])->first();
            if (!$customer) continue;

            // Find coach or nutritionist
            $professional = null;
            if (isset($appointmentData['coach_email'])) {
                $professional = User::where('email', $appointmentData['coach_email'])->first();
                $coachId = $professional ? $professional->id : null;
                $nutritionistId = null;
            } else {
                $professional = User::where('email', $appointmentData['nutritionist_email'])->first();
                $coachId = null;
                $nutritionistId = $professional ? $professional->id : null;
            }

            if (!$professional) continue;

            Appointment::updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'coach_id' => $coachId,
                    'nutritionist_id' => $nutritionistId,
                    'scheduled_at' => $appointmentData['scheduled_at'],
                ],
                [
                    'user_id' => $customer->id,
                    'coach_id' => $coachId,
                    'nutritionist_id' => $nutritionistId,
                    'appointment_type' => $appointmentData['appointment_type'],
                    'scheduled_at' => $appointmentData['scheduled_at'],
                    'status' => $appointmentData['status'],
                    'notes' => $appointmentData['notes'],
                    'duration_minutes' => $appointmentData['duration_minutes'],
                ]
            );
        }

        $this->command->info('Created ' . count($appointments) . ' appointments');
    }
}
