<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin users to assign as authors
        $admins = User::role(['admin', 'coach', 'nutritionist'])->get();
        if ($admins->isEmpty()) {
            // Create a default admin if none exists
            $admin = User::create([
                'name' => 'Dr.',
                'last_name' => 'Fitness Expert',
                'display_name' => 'Dr. Fitness Expert',
                'email' => 'expert@fitwnata.com',
                'password' => bcrypt('password'),
            ]);
            $admin->assignRole('admin');
            $admins = collect([$admin]);
        }

        $articles = [
            [
                'title' => 'The Ultimate Guide to Starting Your Fitness Journey',
                'body' => '<p>Starting a fitness journey can feel overwhelming, but with the right approach, anyone can achieve their health and wellness goals. This comprehensive guide will walk you through the essential steps to begin your transformation.</p>

<h3>Setting Realistic Goals</h3>
<p>The first step in any successful fitness journey is setting clear, achievable goals. Whether you want to lose weight, build muscle, improve cardiovascular health, or simply feel more energetic, having specific objectives will keep you motivated and on track.</p>

<h3>Creating a Balanced Routine</h3>
<p>A well-rounded fitness routine should include cardiovascular exercise, strength training, and flexibility work. Aim for at least 150 minutes of moderate-intensity exercise per week, as recommended by health professionals.</p>

<h3>Nutrition Fundamentals</h3>
<p>Exercise is just one part of the equation. Proper nutrition plays a crucial role in achieving your fitness goals. Focus on whole foods, adequate protein intake, and staying hydrated throughout your journey.</p>

<h3>Tracking Progress</h3>
<p>Keep a record of your workouts, measurements, and how you feel. Progress isn\'t always measured by the scale – improvements in energy, strength, and mood are equally important indicators of success.</p>',
                'published_at' => now()->subDays(7),
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => '10 Nutrition Myths Debunked by Science',
                'body' => '<p>The world of nutrition is filled with conflicting information and popular myths that can derail your health goals. Let\'s examine ten common nutrition myths and what science actually tells us.</p>

<h3>Myth 1: Carbs Are Bad</h3>
<p>Complex carbohydrates are essential for energy and brain function. The key is choosing the right types – whole grains, fruits, and vegetables over refined sugars and processed foods.</p>

<h3>Myth 2: Fat Makes You Fat</h3>
<p>Healthy fats are crucial for hormone production, vitamin absorption, and satiety. Include sources like avocados, nuts, olive oil, and fatty fish in your diet.</p>

<h3>Myth 3: Supplements Replace Real Food</h3>
<p>While supplements can fill nutritional gaps, they cannot replace the complex nutrients and fiber found in whole foods. Focus on a varied, balanced diet first.</p>

<h3>The Truth About Meal Timing</h3>
<p>When you eat matters less than what and how much you eat. Focus on consistent, balanced meals that work with your lifestyle and energy needs.</p>',
                'published_at' => now()->subDays(5),
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Building Mental Resilience Through Exercise',
                'body' => '<p>Exercise isn\'t just about physical transformation – it\'s one of the most powerful tools for building mental strength and emotional resilience. Understanding this connection can revolutionize your approach to fitness.</p>

<h3>The Science Behind Exercise and Mental Health</h3>
<p>Regular physical activity triggers the release of endorphins, often called "feel-good" hormones. These natural chemicals help reduce stress, anxiety, and symptoms of depression while boosting mood and self-esteem.</p>

<h3>Stress Management Through Movement</h3>
<p>Exercise serves as a healthy outlet for stress and tension. Whether it\'s a intense HIIT session or a gentle yoga flow, physical activity helps process difficult emotions and clear mental fog.</p>

<h3>Building Confidence and Self-Efficacy</h3>
<p>Each workout completed and goal achieved builds confidence that extends far beyond the gym. This sense of accomplishment and self-efficacy transfers to other areas of life, creating a positive cycle of growth.</p>

<h3>Mindful Movement Practices</h3>
<p>Incorporating mindfulness into your workouts – focusing on breath, form, and how your body feels – can enhance both physical and mental benefits while reducing the risk of injury.</p>',
                'published_at' => now()->subDays(3),
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Recovery: The Missing Piece in Your Fitness Puzzle',
                'body' => '<p>Many fitness enthusiasts focus intensely on workouts and nutrition while overlooking a critical component of success: recovery. Proper recovery is where the magic happens – it\'s when your body adapts, grows stronger, and prepares for future challenges.</p>

<h3>Understanding Active vs. Passive Recovery</h3>
<p>Active recovery involves light movement like walking, gentle yoga, or swimming that promotes blood flow without adding stress. Passive recovery includes complete rest, sleep, and relaxation techniques.</p>

<h3>The Science of Sleep and Muscle Growth</h3>
<p>During deep sleep, your body releases growth hormone and repairs muscle tissue. Aim for 7-9 hours of quality sleep to optimize recovery and performance.</p>

<h3>Nutrition for Recovery</h3>
<p>Post-workout nutrition is crucial for replenishing energy stores and providing the building blocks for muscle repair. Focus on protein and carbohydrates within 2 hours of training.</p>

<h3>Managing Stress and Recovery</h3>
<p>Chronic stress elevates cortisol levels, which can impair recovery and promote muscle breakdown. Incorporate stress management techniques like meditation, deep breathing, or gentle stretching.</p>',
                'published_at' => now()->subDays(2),
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Meal Prep Mastery: Save Time and Eat Well',
                'body' => '<p>Meal preparation is a game-changer for maintaining a healthy diet while managing a busy lifestyle. With proper planning and techniques, you can enjoy nutritious, delicious meals throughout the week without daily cooking stress.</p>

<h3>Planning Your Prep Strategy</h3>
<p>Start by choosing 3-4 versatile proteins, 2-3 complex carbohydrates, and a variety of vegetables. This foundation allows for multiple meal combinations throughout the week.</p>

<h3>Batch Cooking Techniques</h3>
<p>Cook large quantities of proteins like chicken, salmon, or legumes that can be used in different ways. Roast a variety of vegetables and cook grains in bulk for easy mixing and matching.</p>

<h3>Storage and Food Safety</h3>
<p>Proper storage is crucial for food safety and maintaining quality. Use glass containers when possible, label everything with dates, and understand which foods keep well and which are best eaten fresh.</p>

<h3>Making It Sustainable</h3>
<p>Start small with prepping just 2-3 meals per week and gradually increase as you build the habit. Focus on foods you actually enjoy eating to ensure long-term success.</p>',
                'published_at' => now()->subDays(1),
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Strength Training for Beginners: Building a Strong Foundation',
                'body' => '<p>Strength training is one of the most effective ways to build muscle, increase bone density, boost metabolism, and improve overall health. If you\'re new to lifting weights, this guide will help you start safely and effectively.</p>

<h3>Benefits Beyond Muscle Building</h3>
<p>Strength training improves insulin sensitivity, supports bone health, enhances cognitive function, and can help prevent age-related muscle loss. It\'s truly a fountain of youth exercise.</p>

<h3>Starting with Bodyweight</h3>
<p>Before adding external weights, master bodyweight movements like squats, push-ups, lunges, and planks. These exercises teach proper movement patterns and build foundational strength.</p>

<h3>Progressive Overload Principle</h3>
<p>Gradually increase the challenge by adding weight, reps, or sets over time. This progressive approach ensures continuous improvement while minimizing injury risk.</p>

<h3>Form Over Everything</h3>
<p>Perfect form is more important than heavy weights. Focus on controlled movements, proper breathing, and feeling the target muscles working. Consider working with a qualified trainer initially.</p>',
                'published_at' => now(),
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Hydration: Your Secret Weapon for Performance',
                'body' => '<p>Water is often overlooked in fitness discussions, yet proper hydration is fundamental to every bodily function. Understanding hydration needs can significantly impact your energy, performance, and recovery.</p>

<h3>Why Hydration Matters</h3>
<p>Water regulates body temperature, lubricates joints, transports nutrients, and removes waste products. Even mild dehydration can impair cognitive function and physical performance.</p>

<h3>Daily Hydration Needs</h3>
<p>While the "8 glasses a day" rule is a good starting point, actual needs vary based on body size, activity level, climate, and individual factors. Monitor your urine color as a simple hydration indicator.</p>

<h3>Exercise Hydration Strategy</h3>
<p>Drink 16-20 ounces of water 2-3 hours before exercise, 8 ounces during warm-up, and 7-10 ounces every 10-20 minutes during activity. Post-workout, drink 16-24 ounces for every pound lost through sweat.</p>

<h3>Electrolyte Balance</h3>
<p>For workouts longer than an hour or in hot conditions, consider electrolyte replacement. Natural options include coconut water or adding a pinch of sea salt to your water.</p>',
                'published_at' => now()->addDays(1),
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Creating Your Home Gym on Any Budget',
                'body' => '<p>You don\'t need expensive equipment or a gym membership to get an effective workout. With creativity and smart shopping, you can create a functional home gym that meets your fitness needs regardless of budget or space constraints.</p>

<h3>Minimal Equipment, Maximum Results</h3>
<p>Start with versatile, space-efficient equipment: resistance bands, a yoga mat, adjustable dumbbells, and a stability ball. These items can provide a full-body workout and store easily.</p>

<h3>Bodyweight Training Options</h3>
<p>Master bodyweight exercises that require no equipment: push-ups, squats, lunges, planks, and mountain climbers. These movements can be modified for any fitness level and combined into effective circuits.</p>

<h3>DIY Equipment Solutions</h3>
<p>Get creative with household items: use water jugs as weights, stairs for cardio, towels for sliding exercises, and walls for wall sits. A little imagination goes a long way.</p>

<h3>Space-Saving Storage</h3>
<p>Choose equipment that serves multiple purposes or stores compactly. Under-bed storage, wall mounts, and collapsible equipment help maintain a clutter-free living space.</p>',
                'published_at' => null, // Draft article
                'featured_image' => 'https://images.unsplash.com/photo-1534835870752-96a875c7caea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($articles as $articleData) {
            $author = $admins->random();

            // Generate slug from title
            $slug = \Illuminate\Support\Str::slug($articleData['title']);

            Article::updateOrCreate(
                ['title' => $articleData['title']],
                array_merge($articleData, [
                    'author_id' => $author->id,
                    'slug' => $slug
                ])
            );
        }
    }
}
