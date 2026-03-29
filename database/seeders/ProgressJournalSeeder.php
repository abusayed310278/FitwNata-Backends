<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProgressJournal;
use Carbon\Carbon;

class ProgressJournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get customers and coaches
        $customers = User::role('customer')->get();
        $coaches = User::role('coach')->get();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run UserSeeder first.');
            return;
        }

        foreach ($customers as $customer) {
            $this->createProgressEntriesForUser($customer, $coaches);
        }
    }

    private function createProgressEntriesForUser($customer, $coaches)
    {
        $entryTypes = ['workout', 'nutrition', 'wellness', 'measurements', 'goals', 'coach_note'];

        // Create entries over the past 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i);

            // Not every day has entries - make it realistic
            if (rand(1, 3) === 1) continue;

            $numEntries = rand(1, 3);

            for ($j = 0; $j < $numEntries; $j++) {
                $entryType = $entryTypes[array_rand($entryTypes)];

                $entry = $this->generateEntryData($customer, $entryType, $date, $coaches);
                if ($entry) {
                    ProgressJournal::create($entry);
                }
            }
        }
    }

    private function generateEntryData($customer, $entryType, $date, $coaches)
    {
        $baseEntry = [
            'user_id' => $customer->id,
            'entry_date' => $date->format('Y-m-d'),
            'entry_type' => $entryType,
            'created_at' => $date,
            'updated_at' => $date,
        ];

        switch ($entryType) {
            case 'workout':
                return array_merge($baseEntry, [
                    'title' => $this->getRandomWorkoutTitle(),
                    'content' => $this->getRandomWorkoutContent(),
                    'mood_rating' => rand(3, 5),
                    'energy_level' => rand(3, 5),
                ]);

            case 'nutrition':
                return array_merge($baseEntry, [
                    'title' => 'Daily Nutrition Log',
                    'content' => $this->getRandomNutritionContent(),
                    'mood_rating' => rand(2, 5),
                    'energy_level' => rand(2, 5),
                ]);

            case 'wellness':
                return array_merge($baseEntry, [
                    'title' => 'Wellness Check-in',
                    'content' => $this->getRandomWellnessContent(),
                    'mood_rating' => rand(2, 5),
                    'energy_level' => rand(2, 5),
                ]);

            case 'measurements':
                return array_merge($baseEntry, [
                    'title' => 'Body Measurements',
                    'content' => $this->getRandomMeasurementContent(),
                ]);

            case 'goals':
                return array_merge($baseEntry, [
                    'title' => 'Goal Progress Update',
                    'content' => $this->getRandomGoalContent(),
                    'mood_rating' => rand(3, 5),
                ]);

            case 'coach_note':
                if ($coaches->isEmpty()) return null;

                return array_merge($baseEntry, [
                    'title' => 'Coach Feedback',
                    'content' => $this->getRandomCoachNoteContent(),
                    'coach_id' => $coaches->random()->id,
                ]);

            default:
                return null;
        }
    }

    private function getRandomWorkoutTitle()
    {
        $titles = [
            'Morning Strength Training',
            'Cardio Session',
            'HIIT Workout',
            'Yoga Flow',
            'Upper Body Focus',
            'Lower Body Blast',
            'Full Body Circuit',
            'Core and Flexibility',
            'Running Session',
            'CrossFit WOD',
        ];

        return $titles[array_rand($titles)];
    }

    private function getRandomWorkoutContent()
    {
        $contents = [
            'Completed a solid 45-minute strength training session. Felt strong today and managed to increase my squat weight by 5lbs. Form felt good throughout.',
            'Great cardio session on the treadmill. Ran for 30 minutes at a steady pace. Heart rate stayed in target zone. Feeling energized!',
            'HIIT session was challenging but rewarding. 20 minutes of high-intensity intervals. Sweated a lot but pushed through.',
            'Relaxing yoga flow to start the day. Focused on flexibility and mindfulness. Body feels more loose and relaxed.',
            'Upper body workout focusing on chest, shoulders, and arms. Did bench press, shoulder raises, and tricep dips. Muscles feel worked!',
            'Leg day was intense! Squats, lunges, and calf raises. Legs are definitely going to be sore tomorrow.',
            'Full body circuit training. Mixed cardio and strength exercises. Great way to work multiple muscle groups.',
            'Core-focused workout with planks, crunches, and Russian twists. Also did some stretching afterward.',
            'Went for a run in the park. Beautiful weather made it enjoyable. Completed 5K in good time.',
            'CrossFit workout was brutal but fun. Burpees, box jumps, and kettlebell swings. Team atmosphere was motivating.',
        ];

        return $contents[array_rand($contents)];
    }

    private function getRandomNutritionContent()
    {
        $contents = [
            'Breakfast: Oatmeal with berries and protein powder. Lunch: Grilled chicken salad. Dinner: Salmon with quinoa and vegetables. Stayed within calorie goals.',
            'Had a cheat meal today but balanced it with healthy choices for other meals. Drank plenty of water throughout the day.',
            'Meal prep Sunday! Prepared healthy meals for the week. Feeling organized and ready to stick to my nutrition plan.',
            'Tried a new healthy recipe today. Quinoa bowl with roasted vegetables was delicious and filling. Will definitely make again.',
            'Struggled with late-night cravings but managed to choose a healthy snack instead of junk food. Small victories count!',
            'Hit my protein target today with lean meats and supplements. Feeling satisfied and energized from good food choices.',
            'Cooked at home instead of ordering takeout. Grilled vegetables and lean protein. Saving money and eating healthier.',
            'Increased my vegetable intake today. Had salads with lunch and dinner. Body feels lighter and more energized.',
        ];

        return $contents[array_rand($contents)];
    }

    private function getRandomWellnessContent()
    {
        $contents = [
            'Got 8 hours of sleep last night. Feeling rested and ready for the day. Sleep quality is improving with my new routine.',
            'Practiced meditation for 10 minutes this morning. Feeling centered and less anxious. Mindfulness is becoming a habit.',
            'Stress levels were high today due to work, but managed to do some deep breathing exercises. Need to prioritize stress management.',
            'Feeling more flexible after consistent stretching routine. Back pain is decreasing and mobility is improving.',
            'Had a great day overall. Energy levels are stable and mood is positive. Healthy habits are paying off.',
            'Took a rest day from intense workouts. Sometimes the body needs recovery. Did gentle stretching instead.',
            'Hydration on point today! Drank my target amount of water. Skin feels better and energy is more consistent.',
            'Spent time outdoors in nature. Fresh air and sunlight always boost my mood and mental clarity.',
        ];

        return $contents[array_rand($contents)];
    }

    private function getRandomMeasurementContent()
    {
        $contents = [
            'Weight: 150 lbs (down 2 lbs from last week). Waist: 32 inches. Feeling good about the progress.',
            'Body fat percentage decreased by 1%. Muscle mass is increasing according to the scale. Hard work is paying off.',
            'Measurements staying consistent. Focus is shifting from weight loss to body composition improvement.',
            'Lost another inch around my waist! Clothes are fitting better and confidence is growing.',
            'Weight fluctuated this week but overall trend is positive. Not getting discouraged by daily variations.',
            'Strength gains are more important than scale weight right now. Can lift heavier and feel stronger.',
        ];

        return $contents[array_rand($contents)];
    }

    private function getRandomGoalContent()
    {
        $contents = [
            'Getting closer to my goal of running a 5K without stopping. Can now run for 20 minutes straight.',
            'Strength goal progress: Can now bench press my body weight! Next target is 1.25x body weight.',
            'Flexibility goal: Can almost touch my toes! Daily stretching is showing results.',
            'Weight loss goal: Halfway to my target. Losing 1-2 lbs per week consistently.',
            'Nutrition goal: Successfully meal prepped for 3 weeks in a row. Building sustainable habits.',
            'Sleep goal: Averaged 7.5 hours of sleep this week. Quality is improving with better sleep hygiene.',
            'Stress management goal: Meditated 5 days this week. Finding it easier to stay calm under pressure.',
        ];

        return $contents[array_rand($contents)];
    }

    private function getRandomCoachNoteContent()
    {
        $contents = [
            'Great progress this week! Form is improving on squats. Focus on maintaining proper posture during overhead movements.',
            'Excellent dedication to the nutrition plan. Suggest adding more variety in vegetable choices for better micronutrient profile.',
            'Recovery seems to be improving. Consider adding one more rest day per week to prevent overtraining.',
            'Cardiovascular endurance is noticeably better. Ready to increase intensity in next phase of training.',
            'Flexibility work is paying off. Continue daily stretching routine and consider adding yoga sessions.',
            'Weight loss is on track. Lets focus on maintaining muscle mass while in caloric deficit.',
            'Mental approach to training is much more positive. Confidence is building with each successful workout.',
            'Sleep quality improvements are reflected in training performance. Keep prioritizing rest and recovery.',
        ];

        return $contents[array_rand($contents)];
    }
}
