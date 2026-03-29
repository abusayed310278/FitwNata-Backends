<?php

namespace App\Http\Controllers\Api;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Models\UserWeightHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseApiController;

class UserProfileController extends BaseApiController
{
    /**
     * Get user profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('userProfile');

        // Create or get user profile if it doesn't exist
        if (!$user->userProfile) {
            $user->userProfile()->create([
                'user_id' => $user->id,
                'username' => $user->name . '_' . $user->id,
            ]);
        }

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_photo_url' => $user->profile_photo_url,
                'created_at' => $user->created_at,
            ],
            'profile' => $user->userProfile
        ]);
    }

    /**
     * Update user profile.
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|min:3|max:50|unique:user_profiles,username,' . ($request->user()->userProfile->id ?? 0),
            'gender' => 'sometimes|in:male,female,other',
            'date_of_birth' => 'sometimes|date',
            'health_conditions' => 'sometimes|array',
            'preferred_workout_types' => 'sometimes|array',
            'training_location' => 'sometimes|in:home,gym,outdoors,outdoor,studio,crossfit_box,no_preference',
            'fitness_goals' => 'sometimes|array',
            'training_level' => 'sometimes|in:beginner,intermediate,advanced,expert',
            'weekly_training_objective' => 'sometimes|string|max:255',
            'equipment_availability' => 'sometimes|array',
            'nutrition_knowledge_level' => 'sometimes|in:beginner,intermediate,advanced,expert',
            'preferred_recipe_type' => 'sometimes|in:quick_easy,high_protein,healthy_balanced,energy_boosting,balanced_macros,plant_based,performance_nutrition,diabetic_friendly,scientifically_based,western,local,both',
            'weight_kg' => 'sometimes|numeric|min:30|max:300',
            'height_cm' => 'sometimes|numeric|min:100|max:250',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        if($request->has('phone'))
        {
            $user->update(['phone' => $request->phone]);
        }

        // Create user profile if it doesn't exist
        if (!$user->userProfile) {
            $user->userProfile()->create([
                'user_id' => $user->id,
                'username' => $request->username ?? ($user->name . '_' . $user->id),
            ]);
        }

        UserWeightHistory::create([
            'user_id' => $user->id,
            'old_value' => $user->userProfile->weight_kg,
            'new_value' => $request->weight_kg,
            'updated_by' => Auth::Id(),
        ]);

        // Update user profile with provided data
        $user->userProfile->update($request->except(['name', 'profile_photo_url']));

        // Reload the user with updated profile
        $user->load('userProfile');

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_url' => $user->profile_photo_url,
                'created_at' => $user->created_at,
            ],
            'profile' => $user->userProfile
        ], 'Profile updated successfully');
    }

    /**
     * Get user preferences.
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = $request->user()->load('userProfile');

        // Create or get user profile if it doesn't exist
        if (!$user->userProfile) {
            $user->userProfile()->create([
                'user_id' => $user->id,
                'username' => $user->name . '_' . $user->id,
            ]);
        }

        return $this->success([
            'preferences' => [
                'preferred_workout_types' => $user->userProfile->preferred_workout_types,
                'training_location' => $user->userProfile->training_location,
                'fitness_goals' => $user->userProfile->fitness_goals,
                'training_level' => $user->userProfile->training_level,
                'weekly_training_objective' => $user->userProfile->weekly_training_objective,
                'equipment_availability' => $user->userProfile->equipment_availability,
                'nutrition_knowledge_level' => $user->userProfile->nutrition_knowledge_level,
                'preferred_recipe_type' => $user->userProfile->preferred_recipe_type,
            ]
        ], 'Preferences retrieved successfully');
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'preferred_workout_types' => 'sometimes|array',
            'training_location' => 'sometimes|in:home,gym,outdoors,outdoor,studio,crossfit_box,no_preference',
            'fitness_goals' => 'sometimes|array',
            'training_level' => 'sometimes|in:beginner,intermediate,advanced,expert',
            'weekly_training_objective' => 'sometimes|string|max:255',
            'equipment_availability' => 'sometimes|array',
            'nutrition_knowledge_level' => 'sometimes|in:beginner,intermediate,advanced,expert',
            'preferred_recipe_type' => 'sometimes|in:quick_easy,high_protein,healthy_balanced,energy_boosting,balanced_macros,plant_based,performance_nutrition,diabetic_friendly,scientifically_based,western,local,both',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Create user profile if it doesn't exist
        if (!$user->userProfile) {
            $user->userProfile()->create([
                'user_id' => $user->id,
                'username' => $user->name . '_' . $user->id,
            ]);
        }

        // Update user profile preferences
        $user->userProfile->update($request->only([
            'preferred_workout_types',
            'training_location',
            'fitness_goals',
            'training_level',
            'weekly_training_objective',
            'equipment_availability',
            'nutrition_knowledge_level',
            'preferred_recipe_type',
        ]));

        // Reload the user with updated profile
        $user->load('userProfile');

        return $this->success([
            'preferences' => [
                'preferred_workout_types' => $user->userProfile->preferred_workout_types,
                'training_location' => $user->userProfile->training_location,
                'fitness_goals' => $user->userProfile->fitness_goals,
                'training_level' => $user->userProfile->training_level,
                'weekly_training_objective' => $user->userProfile->weekly_training_objective,
                'equipment_availability' => $user->userProfile->equipment_availability,
                'nutrition_knowledge_level' => $user->userProfile->nutrition_knowledge_level,
                'preferred_recipe_type' => $user->userProfile->preferred_recipe_type,
            ]
        ], 'Preferences updated successfully');
    }

    /**
     * Upload avatar.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_photo_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        if ($request->hasFile('profile_photo_url')) {
            $user->profile_photo_url = uploadFile($request->file('profile_photo_url'),'images/profile-photos');
            $user->save();

            if ($user->userProfile) {
                $user->userProfile->update(['profile_image_url' => $user->profile_photo_url]);
            }

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'created_at' => $user->created_at,
                ],
                'profile' => $user->userProfile
            ], 'Avatar uploaded successfully');
        }

        return $this->error('No file uploaded');
    }
}
