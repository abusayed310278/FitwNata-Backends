<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\User;
use App\Models\Workout;
use App\Models\MealPlan;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseApiController;

class OnboardingController extends BaseApiController
{
    /**
     * Get onboarding questions.
     */
    public function getQuestions(): JsonResponse
    {
        $questions = [
            [
                'id' => 'username',
                'type' => 'text',
                'title' => 'Create a username',
                'required' => true,
                'validation' => 'min:3|max:50|unique:user_profiles'
            ],
            [
                'id' => 'gender',
                'type' => 'select',
                'title' => 'Gender',
                'options' => ['male', 'female', 'other'],
                'required' => true
            ],
            [
                'id' => 'health_conditions',
                'type' => 'multiselect',
                'title' => 'Current health condition(s)',
                'options' => ['diabetes', 'hypertension', 'heart_disease', 'arthritis', 'none'],
                'required' => false
            ],
            [
                'id' => 'preferred_workout_types',
                'type' => 'multiselect',
                'title' => 'Preferred type of workout',
                'options' => ['abs', 'cardio', 'toning', 'strength', 'yoga', 'pilates'],
                'required' => true
            ],

            [
                'id' => 'training_location',
                'type' => 'select',
                'title' => 'Training location',
                'options' => ['home', 'gym', 'outdoors', 'no_preference'],
                'required' => true
            ],
            [
                'id' => 'fitness_goals',
                'type' => 'multiselect',
                'title' => 'Personal fitness goals',
                'options' => ['weight_loss', 'muscle_gain', 'endurance', 'flexibility', 'general_fitness'],
                'required' => true
            ],
            [
                'id' => 'training_level',
                'type' => 'select',
                'title' => 'Current training level',
                'options' => ['beginner', 'intermediate', 'advanced'],
                'required' => true
            ],
            [
                'id' => 'weekly_training_objective',
                'type' => 'number',
                'title' => 'Weekly training objective (sessions per week)',
                'min' => 1,
                'max' => 7,
                'required' => true
            ],
            [
                'id' => 'equipment_availability',
                'type' => 'multiselect',
                'title' => 'Equipment availability',
                'options' => ['dumbbells', 'barbell', 'yoga_mat', 'resistance_bands', 'none'],
                'required' => false
            ],
            [
                'id' => 'nutrition_knowledge_level',
                'type' => 'select',
                'title' => 'Nutrition knowledge level',
                'options' => ['beginner', 'intermediate', 'advanced'],
                'required' => true
            ],
            [
                'id' => 'preferred_recipe_type',
                'type' => 'select',
                'title' => 'Preferred recipe type',
                'options' => ['western', 'local', 'both'],
                'required' => true
            ]
        ];

        return $this->success($questions, 'Onboarding questions retrieved successfully');
    }

    /**
     * Submit onboarding answers.
     */
    // public function submitAnswers(Request $request): JsonResponse
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'username' => 'required|string|min:3|max:50|unique:user_profiles',
    //             'gender' => 'required|in:male,female,other',
    //             'health_conditions' => 'required|array|min:1',
    //             'preferred_workout_types' => 'required|array|min:1',
    //             'training_location' => 'required|in:home,gym,outdoors,no_preference',
    //             // 'fitness_goals' => 'required|array|min:1',
    //             'training_level' => 'required|in:beginner,intermediate,advanced',
    //             // 'weekly_training_objective' => 'required|integer|min:1|max:7',
    //             // 'equipment_availability' => 'array',
    //             'nutrition_knowledge_level' => 'required|in:beginner,intermediate,advanced',
    //             // 'preferred_recipe_type' => 'required|in:western,local,both',
    //             'weight_kg' => 'nullable|numeric|min:30|max:300',
    //             'height_cm' => 'nullable|numeric|min:100|max:250',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->validationError($validator->errors());
    //         }

    //         $user = Auth::user();

    //         // dd($user);

    //         // Create user profile
    //         $userProfile = UserProfile::create([
    //             'user_id' => $user->id,
    //             'username' => $request->username,
    //             'gender' => $request->gender,
    //             'health_conditions' => json_encode($request->health_conditions ?? []),
    //             'preferred_workout_types' => json_encode($request->preferred_workout_types),
    //             'training_location' => $request->training_location,
    //             'fitness_goals' => json_encode($request->fitness_goals),
    //             'training_level' => $request->training_level,
    //             'weekly_training_objective' => $request->weekly_training_objective,
    //             'equipment_availability' => json_encode($request->equipment_availability ?? []),
    //             'nutrition_knowledge_level' => $request->nutrition_knowledge_level,
    //             'preferred_recipe_type' => $request->preferred_recipe_type,
    //             'weight_kg' => $request->weight_kg,
    //             'height_cm' => $request->height_cm,
    //         ]);

    //         return $this->success([
    //             'profile' => $userProfile,
    //             'message' => 'Profile created successfully'
    //         ], 'Onboarding data saved successfully');

    //     } catch (\Exception $e) {
    //         return $this->serverError('Failed to save onboarding data');
    //     }
    // }

    public function submitAnswers(Request $request): JsonResponse
    {
        try {
            $user    = Auth::user();
            $profile = UserProfile::where('user_id', $user->id)->first();

            $validator = Validator::make($request->all(), [
                'username' => [
                    'required','string','min:3','max:50',
                    Rule::unique('user_profiles','username')->ignore($profile?->id),
                ],
                'gender'                    => 'required|in:male,female,other',
                'health_conditions'         => 'required|array|min:1',
                'preferred_workout_types'   => 'required|array|min:1',
                'training_location'         => 'required|in:home,gym,outdoors,no_preference',
                'training_level'            => 'required|in:beginner,intermediate,advanced',
                'nutrition_knowledge_level' => 'required|in:beginner,intermediate,advanced',
                'weight_kg'                 => 'nullable|numeric|min:30|max:300',
                'height_cm'                 => 'nullable|numeric|min:100|max:250',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $data = [
                'user_id'                   => $user->id,
                'username'                  => $request->string('username'),
                'gender'                    => $request->string('gender'),
                'health_conditions'         => $request->input('health_conditions', []),
                'preferred_workout_types'   => $request->input('preferred_workout_types', []),
                'training_location'         => $request->string('training_location'),
                'fitness_goals'             => $request->input('fitness_goals', []),         
                'training_level'            => $request->string('training_level'),
                'weekly_training_objective' => $request->input('weekly_training_objective'),
                'equipment_availability'    => $request->input('equipment_availability', []),
                'nutrition_knowledge_level' => $request->string('nutrition_knowledge_level'),
                'preferred_recipe_type'     => $request->input('preferred_recipe_type'),
                'weight_kg'                 => $request->input('weight_kg'),
                'height_cm'                 => $request->input('height_cm'),
            ];

            if ($profile) {
                $profile->update($data);
                $msg = 'Profile updated successfully';
            } else {
                $profile = UserProfile::create($data);
                $msg = 'Profile created successfully';
            }

            return response()->json([
                'status'  => true,
                'message' => 'Onboarding data saved successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Onboarding submitAnswers error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->serverError('Failed to save onboarding data');
        }
    }

    /**
     * Get personalized recommendations.
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profile = $user->userProfile;

            if (!$profile) {
                return $this->error('Complete onboarding first');
            }

            // Get recommended plans based on profile with better recommendations
            $recommendedPlans = Plan::active()->orderBy('price')->get()->map(function ($plan) use ($profile) {
                // Add recommendation logic based on user profile
                $recommendation_score = $this->calculatePlanScore($plan, $profile);

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'formatted_price' => $plan->formatted_price,
                    'interval' => $plan->interval,
                    'type' => $plan->type,
                    'features' => $plan->features ?? $this->getDefaultFeatures($plan),
                    'is_popular' => $plan->is_popular,
                    'is_recommended' => $recommendation_score > 7, // Recommend if score > 7
                    'recommendation_score' => $recommendation_score,
                    'why_recommended' => $this->getRecommendationReason($plan, $profile),
                ];
            })->sortByDesc('recommendation_score')->values();

            // Get recommended workouts
            $workouts = Workout::where('level', $profile->training_level)->take(5)->get();

            // Get recommended meal plans
            $mealPlans = MealPlan::take(3)->get();

            return $this->success([
                'subscription_plans' => $recommendedPlans,
                'recommended_workouts' => $workouts,
                'recommended_meal_plans' => $mealPlans,
                'onboarding_complete' => false, // Still need to select subscription
            ], 'Recommendations generated successfully');

        } catch (\Exception $e) {
            \Log::error($e);
            return $this->serverError('Failed to generate recommendations');
        }
    }

    /**
     * Complete onboarding process.
     */
    public function completeOnboarding(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'selected_plan_id' => 'nullable|exists:plans,id',
            'payment_method' => 'required_with:selected_plan_id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = $request->user();
            $profile = $user->userProfile;

            if (!$profile) {
                return $this->error('Please complete the questionnaire first');
            }

            // Handle subscription selection if provided
            $subscriptionData = null;
            if ($request->selected_plan_id) {
                $plan = Plan::findOrFail($request->selected_plan_id);

                // Check if user already has subscription
                if ($user->subscribed() || $user->subscriptions()->where('status', 'active')->exists()) {
                    return $this->error('You already have an active subscription');
                }

                if ($plan->isFree()) {
                    // Create free subscription
                    $subscription = \App\Models\Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'stripe_id' => 'free_' . uniqid(),
                        'status' => 'active',
                        'ends_at' => null,
                    ]);

                    $subscriptionData = [
                        'subscription_id' => $subscription->id,
                        'plan' => $plan,
                        'status' => 'active',
                        'type' => 'free'
                    ];
                } else {
                    // Create paid subscription with Stripe
                    if (!$request->payment_method) {
                        return $this->error('Payment method is required for paid plans');
                    }

                    try {
                        $user->addPaymentMethod($request->payment_method);
                        $stripeSubscription = $user->newSubscription('default', $plan->stripe_plan_id)
                            ->create($request->payment_method);

                        // Create local subscription record
                        $subscription = \App\Models\Subscription::create([
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'stripe_id' => $stripeSubscription->id,
                            'status' => $stripeSubscription->stripe_status,
                            'trial_ends_at' => $stripeSubscription->trial_ends_at,
                            'ends_at' => $stripeSubscription->ends_at,
                        ]);

                        $subscriptionData = [
                            'subscription_id' => $subscription->id,
                            'stripe_subscription_id' => $stripeSubscription->id,
                            'plan' => $plan,
                            'status' => $stripeSubscription->stripe_status,
                            'type' => 'paid'
                        ];
                    } catch (\Laravel\Cashier\Exceptions\IncompletePayment $exception) {
                        return $this->error('Payment requires additional confirmation', [
                            'payment_intent' => [
                                'id' => $exception->payment->id,
                                'client_secret' => $exception->payment->client_secret,
                            ]
                        ]);
                    }
                }
            }

            // Mark onboarding as completed by adding a flag to user profile
            $profile->update(['onboarding_completed' => true]);

            return $this->success([
                'onboarding_completed' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $profile
                ],
                'subscription' => $subscriptionData,
                'access_level' => $subscriptionData ? ($subscriptionData['plan']->isPremium() ? 'premium' : 'free') : 'free',
            ], 'Onboarding completed successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to complete onboarding: ' . $e->getMessage());
        }
    }

    /**
     * Calculate recommendation score for a plan based on user profile.
     */
    private function calculatePlanScore($plan, $profile): int
    {
        $score = 5; // Base score

        // Increase score based on fitness goals
        $fitnessGoals = json_decode($profile->fitness_goals, true) ?? [];
        if (in_array('weight_loss', $fitnessGoals) || in_array('muscle_gain', $fitnessGoals)) {
            $score += 2;
        }

        // Increase score for advanced users on premium plans
        if ($profile->training_level === 'advanced' && $plan->isPremium()) {
            $score += 3;
        }

        // Increase score for beginners on free plans
        if ($profile->training_level === 'beginner' && $plan->isFree()) {
            $score += 2;
        }

        // Boost popular plans
        if ($plan->is_popular) {
            $score += 1;
        }

        return min($score, 10); // Cap at 10
    }

    /**
     * Get default features for a plan.
     */
    private function getDefaultFeatures($plan): array
    {
        if ($plan->isFree()) {
            return [
                'Basic workout plans',
                'Limited meal plans',
                'Progress tracking',
                'Community support'
            ];
        }

        return [
            'Unlimited workout plans',
            'Premium meal plans & recipes',
            'Advanced progress tracking',
            'Coach consultations',
            'Priority support',
            'Advanced analytics'
        ];
    }

    /**
     * Get recommendation reason for a plan.
     */
    private function getRecommendationReason($plan, $profile): string
    {
        if ($profile->training_level === 'beginner' && $plan->isFree()) {
            return 'Perfect for beginners to get started';
        }

        if ($profile->training_level === 'advanced' && $plan->isPremium()) {
            return 'Advanced features for serious fitness enthusiasts';
        }

        if ($plan->is_popular) {
            return 'Most popular choice among users';
        }

        return 'Great value for your fitness goals';
    }
}
