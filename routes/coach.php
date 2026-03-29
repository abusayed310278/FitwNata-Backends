<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Nutritionist\RecipeController;
use App\Http\Controllers\Coach\Article\ArticleController;
use App\Http\Controllers\Coach\Workout\WorkoutController;
use App\Http\Controllers\Nutritionist\MealPlanController;
use App\Http\Controllers\Coach\Exercise\ExerciseController;



Route::post('assign/docs/plans/{type}/{id}', [DashboardController::class, 'assignDocToPlans'])->name('assign.doc.plans');

Route::group(['namespace' => 'App\Http\Controllers'], function () {
        Route::prefix('coach/')->middleware(['role:coach|nutritionist'])->group(function () {
            Route::get('dashboard', 'Coach\CoachPanelController@dashboard')->name('coach.dashboard');

            // Appointments Management
            Route::prefix('appointments')->group(function () {
                Route::get('/', 'Coach\AppointmentController@index')->name('coach.appointments.index');
                Route::get('/calendar', 'Coach\AppointmentController@calendar')->name('coach.appointments.calendar');
                Route::get('/events', 'Coach\AppointmentController@getEvents')->name('coach.appointments.events');
                Route::post('/{appointment}/approve', 'Coach\AppointmentController@approve')->name('coach.appointments.approve');
                Route::post('/{appointment}/reschedule', 'Coach\AppointmentController@reschedule')->name('coach.appointments.reschedule');
                Route::post('/{appointment}/cancel', 'Coach\AppointmentController@cancel')->name('coach.appointments.cancel');
                Route::post('/{appointment}/complete', 'Coach\AppointmentController@complete')->name('coach.appointments.complete');
                Route::get('/data', 'Coach\AppointmentController@getAppointments')->name('coach.appointments.data');
            });

            // Availability Settings
            Route::prefix('availability')->group(function () {
                Route::get('/', 'Coach\AvailabilityController@index')->name('coach.availability.index');
                Route::post('/update', 'Coach\AvailabilityController@update')->name('coach.availability.update');
                Route::post('/block-time', 'Coach\AvailabilityController@blockTime')->name('coach.availability.block');
                Route::delete('/unblock/{id}', 'Coach\AvailabilityController@unblock')->name('coach.availability.unblock');
            });

            // Content Manager
            Route::prefix('content')->group(function () {
                Route::get('/', 'Coach\ContentController@index')->name('coach.content.index');
                Route::get('/create', 'Coach\ContentController@create')->name('coach.content.create');
                Route::post('/', 'Coach\ContentController@store')->name('coach.content.store');
                Route::get('/{article}/edit', 'Coach\ContentController@edit')->name('coach.content.edit');
                Route::put('/{article}', 'Coach\ContentController@update')->name('coach.content.update');
                Route::delete('/{article}', 'Coach\ContentController@destroy')->name('coach.content.destroy');
                Route::post('/upload-media', 'Coach\ContentController@uploadMedia')->name('coach.content.upload-media');
                Route::get('/data', 'Coach\ContentController@getContent')->name('coach.content.data');
            });

            // Clients Management
            Route::prefix('clients')->group(function () {
                Route::get('/', 'Coach\ClientController@index')->name('coach.clients.index');
                Route::get('/{user}', 'Coach\ClientController@show')->name('coach.clients.show');
                Route::post('/{user}/notes', 'Coach\ClientController@addNote')->name('coach.clients.add-note');
                Route::get('/{user}/history', 'Coach\ClientController@history')->name('coach.clients.history');
                Route::post('/{user}/schedule-followup', 'Coach\ClientController@scheduleFollowup')->name('coach.clients.schedule-followup');
                Route::get('/data/list', 'Coach\ClientController@getClients')->name('coach.clients.data');
            });



            Route::name('coach.')->group(function () {
                Route::get('ajax-workout-list',[WorkoutController::class,'getWorkouts'])->name('workout.getWorkouts');
                Route::get('ajax-exercise-list',[ExerciseController::class,'getExercises'])->name('exercise.getExercises');
                Route::resource('workout',WorkoutController::class);
                Route::resource('exercise',ExerciseController::class);
            });

            Route::get('ajax-article-list','Coach\Article\ArticleController@getArticles')->name('article.data');
            Route::post('articles/{article}/publish','Coach\Article\ArticleController@publish')->name('article.publish');
            Route::post('articles/{article}/unpublish','Coach\Article\ArticleController@unpublish')->name('article.unpublish');
            Route::post('article/{article}/toggle-publish','Coach\Article\ArticleController@togglePublish')->name('article.toggle-publish');
            Route::post('article/bulk-action','Coach\Article\ArticleController@bulkAction')->name('article.bulk');

            Route::resource('article',ArticleController::class);
        });
});

Route::prefix('nutritionist')
    ->middleware(['role:nutritionist'])
    ->name('nutritionist.')
    ->group(function () {

        // Nutritionist Dashboard
        Route::get('/dashboard', function () {
            return view('nutritionist.dashboard');
        })->name('dashboard');

        // Meal Plans
        Route::resource('mealplans', MealPlanController::class)
            ->parameters(['mealplans' => 'mealPlan']);

        // Recipes
        Route::resource('recipes', RecipeController::class);

        // Assign recipes to a meal plan
        Route::get('mealplans/{mealPlan}/assign-recipes', [MealPlanController::class, 'assignRecipes'])
            ->name('mealplans.assign');

        Route::post('mealplans/{mealPlan}/assign-recipes', [MealPlanController::class, 'storeAssignedRecipes'])
            ->name('mealplans.assign.store');
    });