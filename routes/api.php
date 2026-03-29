<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\MealPlanController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\CoachInfoController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ClientInfoController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\UserWorkoutController;
use App\Http\Controllers\Api\CoachMessageController;
use App\Http\Controllers\Api\CoachWorkoutController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserMealPlanController;
use App\Http\Controllers\Api\CoachExerciseController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\CoachDashboardController;
use App\Http\Controllers\Api\UserWorkoutRunController;
use App\Http\Controllers\Api\CustomerMessageController;
use App\Http\Controllers\Admins\Order\TrackingController;
use App\Http\Controllers\Api\NutritionistRecipeController;
use App\Http\Controllers\Api\NutritionistMealPlanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/guest-login', [AuthController::class, 'guestLogin']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/forgot-password-otp', [AuthController::class, 'sendPasswordOtp']);
    Route::post('/resend-password-otp', [AuthController::class, 'resendPasswordOtp']);
    Route::post('/verify-password-otp', [AuthController::class, 'verifyPasswordOtp']);
    Route::post('/reset-password-otp', [AuthController::class, 'resetPasswordWithOtp']);
    Route::post('/social-login', [AuthController::class, 'socialLogin']);

    // Public content (for app preview)
    Route::get('/content/featured', [ContentController::class, 'featured']);
    Route::get('/plans', [SubscriptionController::class, 'plans']);

    // Public order tracking
    Route::get('/tracking/{tracking_number}', [App\Http\Controllers\Admins\Order\TrackingController::class, 'trackingApi']);

    // Public product details
    Route::get('/product/{product}', [ShopController::class, 'productDetails']);

    // Public appointments data (for guest preview)
    Route::prefix('appointments')->group(function () {
        Route::get('/coaches', [AppointmentController::class, 'availableCoaches']);
        Route::get('/nutritionists', [AppointmentController::class, 'availableNutritionists']);
        Route::get('/coaches/{coach}/availability', [AppointmentController::class, 'availableProfessionals']);
        Route::get('/coach-availabilities/{coach}', [AppointmentController::class, 'availableProfessionals']);
    });

    Route::prefix('onboarding')->group(function () {
        Route::get('/questions', [OnboardingController::class, 'getQuestions']);
        Route::get('/recommendations', [OnboardingController::class, 'getRecommendations']);
        Route::post('/complete', [OnboardingController::class, 'completeOnboarding']);
    });
});

// Protected routes (require authentication)
Route::middleware(['auth:api'])->prefix('v1')->group(function () {

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Profile and Subscription Management
    Route::prefix('profile')->group(function () {
        // User Profile Management
        Route::get('/', [UserProfileController::class, 'show']);
        Route::put('/', [UserProfileController::class, 'update']);
        Route::post('/avatar', [UserProfileController::class, 'uploadAvatar']);
        Route::get('/preferences', [UserProfileController::class, 'getPreferences']);
        Route::put('/preferences', [UserProfileController::class, 'updatePreferences']);

        // Stripe payment methods removed.
    });

    // Stripe subscription/payment routes removed.

    // User Onboarding


    Route::prefix('onboarding')->group(function () {
        Route::post('/submit', [OnboardingController::class, 'submitAnswers']);
        Route::get('status', [AuthController::class, 'checkOnboarding']);
    });

    // Workout Management (Premium content with subscription check)
    Route::prefix('workouts')->middleware('subscription.access:premium')->group(function () {
        Route::get('/', [WorkoutController::class, 'index']);
        Route::get('/recommended', [WorkoutController::class, 'recommended']);
        Route::get('/today', [WorkoutController::class, 'today']);
        Route::get('/my-workouts', [WorkoutController::class, 'myWorkouts']);

        Route::get('/by-level/{level}', [WorkoutController::class, 'byLevel']);
        Route::get('/by-type/{type}', [WorkoutController::class, 'byType']);
        Route::get('/{workout}', [WorkoutController::class, 'show']);
        Route::post('/{excercise_id}/{workout_id}/complete', [WorkoutController::class, 'markComplete']);
        Route::get('/{workout}/exercises', [WorkoutController::class, 'exercises']);

        Route::get('/my-today', [WorkoutController::class, 'today']);
        Route::get('/popular', [WorkoutController::class, 'popularworkouts']);

    });

    Route::prefix('exercises')->middleware('subscription.access:premium')->group(function () {
        Route::get('/', [ExerciseController::class, 'index']);
        Route::get('/{exercise}', [ExerciseController::class, 'show']);
    });

    // Meal Plans & Recipes (Premium content with subscription check)
    // Route::prefix('nutrition')->middleware('subscription.access:premium')->group(function () {
    //     Route::get('/meal-plans', [MealPlanController::class, 'index']);
    //     Route::get('/meal-plans/recommended', [MealPlanController::class, 'recommended']);
    //     Route::get('/meal-plans/{mealPlan}', [MealPlanController::class, 'show']);
    //     Route::get('/recipes', [MealPlanController::class, 'recipes']);
    //     Route::get('/recipes/{recipe}', [MealPlanController::class, 'recipeDetails']);
    //     Route::get('/weekly-plan', [MealPlanController::class, 'weeklyPlan']);
    //     Route::post('/recipes/{recipe}/favorite', [MealPlanController::class, 'toggleFavorite']);
    // });

    Route::prefix('nutrition')->group(function () {
        Route::get('/meal-plans', [MealPlanController::class, 'index']);
        Route::get('/meal-plans/recommended', [MealPlanController::class, 'recommended']);
        Route::get('/meal-plans/{mealPlan}', [MealPlanController::class, 'show']);
        Route::get('/recipes', [MealPlanController::class, 'recipes']);
        Route::get('/recipes/{recipe}', [MealPlanController::class, 'recipeDetails']);
        Route::get('/weekly-plan', [MealPlanController::class, 'weeklyPlan']);
        Route::post('/recipes/{recipe}/favorite', [MealPlanController::class, 'toggleFavorite']);
    });

    // Appointment Booking
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/', [AppointmentController::class, 'store']);
        Route::get('/coaches', [AppointmentController::class, 'availableCoaches']);
        Route::get('/nutritionists', [AppointmentController::class, 'availableNutritionists']);

        Route::get('/coaches/{coach}/availability', [AppointmentController::class, 'availableProfessionals']);
        Route::get('/coach-availabilities/{coach}', [AppointmentController::class, 'availableProfessionals']);
        Route::get('/upcoming', [AppointmentController::class, 'upcoming']);
        Route::get('/history', [AppointmentController::class, 'history']);


        Route::get('/{appointment}', [AppointmentController::class, 'show']);
        Route::put('/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('/{appointment}', [AppointmentController::class, 'cancel']);

    });

    // E-commerce / Shop
    Route::prefix('shop')->group(function () {
        Route::get('/products', [ShopController::class, 'products']);
        Route::get('/products/{product}', [ShopController::class, 'productDetails']);
        Route::get('/categories', [ShopController::class, 'categories']);
        Route::get('/categories/{category}/products', [ShopController::class, 'productsByCategory']);
        Route::post('/cart/add', [ShopController::class, 'addToCart']);
        Route::get('/cart', [ShopController::class, 'cart']);
        Route::post('/cart/{item}/update', [ShopController::class, 'updateCartItem']);
        Route::delete('/cart/{item}', [ShopController::class, 'removeFromCart']);
        Route::post('/checkout', [ShopController::class, 'checkout']);
        Route::get('/orders', [ShopController::class, 'orders']);
        Route::get('/orders/{order}', [ShopController::class, 'orderDetails']);

        Route::group(['prefix' => 'reviews'], function () {
            Route::post('store', [ProductReviewController::class, 'store']);
        });
    });

    // Progress Tracking
    Route::prefix('progress')->group(function () {
        Route::get('/dashboard', [ProgressController::class, 'dashboard']);
        Route::get('/measurements', [ProgressController::class, 'measurements']);
        Route::post('/measurements', [ProgressController::class, 'addMeasurement']);
        Route::get('/journal', [ProgressController::class, 'journal']);
        Route::post('/journal', [ProgressController::class, 'addJournalEntry']);
        Route::put('/journal/{entry}', [ProgressController::class, 'updateJournalEntry']);
        Route::delete('/journal/{entry}', [ProgressController::class, 'deleteJournalEntry']);
        Route::get('/stats', [ProgressController::class, 'stats']);
        Route::get('/achievements', [ProgressController::class, 'achievements']);
    });

    // Content (Articles, Videos, Educational Material)
    Route::prefix('content')->group(function () {
        Route::get('/articles', [ContentController::class, 'articles']);
        Route::get('/articles/{article}', [ContentController::class, 'articleDetails']);
        Route::get('/videos', [ContentController::class, 'videos']);
        Route::get('/tips', [ContentController::class, 'tips']);
        Route::post('/articles/{article}/like', [ContentController::class, 'likeArticle']);
        Route::post('/articles/{article}/bookmark', [ContentController::class, 'bookmarkArticle']);
        Route::get('/bookmarks', [ContentController::class, 'bookmarks']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('/{notification}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
    });

    Route::group(['prefix' => 'dashboard'], function () {

        Route::get('/', [DashboardController::class, 'index']);
    });

    Route::group(['prefix' => 'workout'], function () {

        Route::get('/', [UserWorkoutController::class, 'index'])->name('index');
        Route::post('assignments/exercises/start',    [UserWorkoutRunController::class,'startExercise']);
        Route::post('assignments/exercises/complete', [UserWorkoutRunController::class,'completeExercise']);
        Route::post('assignments/exercises/skip',     [UserWorkoutRunController::class,'skipExercise']);
    });

    Route::group(['prefix' => 'meal-plans'], function () {

        Route::get('/', [UserMealPlanController::class, 'index'])->name('index');
    });

    Route::group(['prefix' => 'message'], function () {

        Route::group(['prefix' => 'coach'], function () {

            Route::post('send', [CoachMessageController::class, 'sendMessage']);
            Route::get('list', [CoachMessageController::class, 'list']);
        });

        Route::group(['prefix' => 'customer'], function () {

            Route::post('send', [CustomerMessageController::class, 'sendMessage']);
            Route::get('list', [CustomerMessageController::class, 'list']);
        });
    });

    Route::group(['prefix' => 'coach'], function () {

        Route::get('dashboard', [CoachDashboardController::class, 'index']);
        Route::get('client/info/{id}', [ClientInfoController::class, 'index']);
        Route::get('info', [CoachInfoController::class, 'profileInfo']);

        Route::group(['prefix' => 'exercise'], function () {

            Route::get('index', [CoachExerciseController::class, 'index']);
            Route::post('store', [CoachExerciseController::class, 'store']);
            Route::post('update', [CoachExerciseController::class, 'update']);
        });

        Route::group(['prefix' => 'workout'], function () {

            Route::get('index', [CoachWorkoutController::class, 'index']);
            Route::post('store', [CoachWorkoutController::class, 'store']);
            Route::post('update', [CoachWorkoutController::class, 'update']);
        });

        Route::group(['prefix' => 'recipes'], function () {

            Route::post('index', [NutritionistRecipeController::class, 'index']);
            Route::post('store', [NutritionistRecipeController::class, 'store']);
            Route::post('update', [NutritionistRecipeController::class, 'update']);
        });

        Route::group(['prefix' => 'mealplans'], function () {

            Route::post('index', [NutritionistMealPlanController::class, 'index']);
            Route::post('store', [NutritionistMealPlanController::class, 'store']);
            Route::post('update', [NutritionistMealPlanController::class, 'update']);
        });

        Route::post('mealplans/{id}/assign-recipes', [NutritionistMealPlanController::class, 'assignRecipes']);
    });
});
