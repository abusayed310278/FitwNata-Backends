<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admins\Order\OrderController;
use App\Http\Controllers\Admins\Product\ProductController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public tracking page
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/support', function () {
    return view('support');
});

Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');


Route::get('/track-order', function () {
    return view('tracking');
})->name('public.tracking');



Route::get('admin/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified','role:admin'])->name('dashboard');
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::middleware(['auth','role:admin'])->group(function () {

        Route::prefix('admin')->group(function () {


Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            Route::put('/users/{user}/profile', [ProfileController::class, 'updateProfile'])->name('user.profile.update');
            Route::put('/users/{user}/password', [ProfileController::class, 'updatePassword'])->name('user.password.update');



            Route::resource('staff','Admins\Staff\StaffController');
             Route::post('/coach/update/status/{coach}', 'Admins\Coach\CoachController@updateStatus')->name('coach.update.status');
            Route::resource('coach','Admins\Coach\CoachController');
            Route::resource('nutritionist','Admins\Nutritionist\NutritionistController');
            Route::resource('product','Admins\Product\ProductController');
            Route::resource('categories','Admins\Product\CategoryController');
            Route::resource('order','Admins\Order\OrderController');

            Route::resource('subscription','Admins\Subscription\SubscriptionController');
            Route::resource('appointment','Admins\Appointment\AppointmentController');
            Route::resource('progress','Admins\Progress\ProgressController');
            Route::resource('measurements','Admins\Measurements\MeasurementController');
            // Route::resource('role','Admins\Staff\RoleController');
            Route::resource('report','Admins\Report\ReportController');
            Route::resource('plan','Admins\Plan\PlanController');
            Route::get('ajax-tags-list','Admins\TagController@list')->name('tags.list');
            Route::resource('tags','Admins\TagController');

            // My Account Routes
            Route::prefix('my-account')->name('my-account.')->group(function () {
                Route::get('/', [\App\Http\Controllers\User\MyAccountController::class, 'index'])->name('index');
                Route::get('/subscription', [\App\Http\Controllers\User\MyAccountController::class, 'subscription'])->name('subscription');
                Route::get('/billing', [\App\Http\Controllers\User\MyAccountController::class, 'billing'])->name('billing');
                Route::post('/subscribe', [\App\Http\Controllers\User\MyAccountController::class, 'subscribe'])->name('subscribe');
                Route::post('/cancel', [\App\Http\Controllers\User\MyAccountController::class, 'cancel'])->name('cancel');
                Route::post('/resume', [\App\Http\Controllers\User\MyAccountController::class, 'resume'])->name('resume');
                Route::post('/change-plan', [\App\Http\Controllers\User\MyAccountController::class, 'changePlan'])->name('change-plan');
                Route::post('/update-payment', [\App\Http\Controllers\User\MyAccountController::class, 'updatePayment'])->name('update-payment');
            });




            Route::get('ajax-staff-list','Admins\Staff\StaffController@getUsers')->name('staff.user_list');
            Route::get('ajax-coach-list','Admins\Coach\CoachController@getCoaches')->name('coach.user_list');
            Route::get('ajax-nutritionist-list','Admins\Nutritionist\NutritionistController@getNutritionists')->name('nutritionist.user_list');
            Route::get('ajax-product-list','Admins\Product\ProductController@getProducts')->name('product.list');
            Route::get('product/{product}/reviews', [ProductController::class, 'getProductReviews'])->name('product.reviews');
            Route::get('ajax-category-list','Admins\Product\CategoryController@getCategories')->name('categories.list');
            Route::get('ajax-order-list','Admins\Order\OrderController@getOrders')->name('order.list');

            Route::get('ajax-plan-list','Admins\Plan\PlanController@getPlans')->name('plan.getPlans');
            Route::post('plan/{plan}/toggle-status','Admins\Plan\PlanController@toggleStatus')->name('plan.toggle-status');


            Route::get('ajax-subscription-list','Admins\Subscription\SubscriptionController@getSubscriptions')->name('subscription.list');
            Route::get('ajax-subscription-data','Admins\Subscription\SubscriptionController@getSubscriptions')->name('subscription.data');
            Route::get('ajax-appointment-list','Admins\Appointment\AppointmentController@getAppointments')->name('appointment.getAppointments');

            Route::get('ajax-progress-list','Admins\Progress\ProgressController@getProgressJournals')->name('progress.getProgressJournals');
            Route::get('ajax-measurements-list','Admins\Measurements\MeasurementController@getMeasurements')->name('measurements.getMeasurements');
            Route::get('ajax-recipe-list','Admins\Content\ContentController@getRecipes')->name('content.recipes.list');
            Route::post('staff/{staff}/reset-password','Admins\Staff\StaffController@resetPassword')->name('staff.reset-password');
            Route::post('staff/{staff}/toggle-status','Admins\Staff\StaffController@toggleStatus')->name('staff.toggle-status');
            // Bulk Actions for Staff
            Route::post('staff/bulk-delete', 'Admins\Staff\StaffController@bulkDelete')->name('staff.bulk_delete');
            Route::get('staff/bulk-export', 'Admins\Staff\StaffController@bulkExport')->name('staff.bulk_export');

            Route::post('appointment/{appointment}/update-status','Admins\Appointment\AppointmentController@updateStatus')->name('appointment.update-status');

            Route::get('measurements/user/{user}','Admins\Measurements\MeasurementController@userProgress')->name('measurements.user.progress');
            Route::post('product/{product}/update-stock','Admins\Product\ProductController@updateStock')->name('product.update-stock');
            Route::post('order/{order}/update-status','Admins\Order\OrderController@updateStatus')->name('order.update-status');
            Route::get('order/{order}/invoice','Admins\Order\OrderController@printInvoice')->name('order.invoice');
            Route::get('orders/{format}/export', [OrderController::class, 'export'])->name('order.export');
            Route::get('order/{order}/tracking','Admins\Order\TrackingController@show')->name('order.tracking');
            Route::post('order/{order}/tracking','Admins\Order\TrackingController@update')->name('order.tracking.update');
            Route::post('order/{order}/tracking-update','Admins\Order\TrackingController@addUpdate')->name('order.tracking.add-update');
            Route::get('generate-tracking-number','Admins\Order\TrackingController@generateTrackingNumber')->name('generate.tracking');
            Route::get('reports/export-users','Admins\Report\ReportController@exportUsers')->name('reports.export-users');
            Route::get('reports/export-revenue','Admins\Report\ReportController@exportRevenue')->name('reports.export-revenue');
            Route::get('reports/export-subscriptions','Admins\Report\ReportController@exportSubscriptions')->name('reports.export-subscriptions');

        });
    });
});


require __DIR__.'/auth.php';
require __DIR__.'/coach.php';
