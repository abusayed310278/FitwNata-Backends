<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\Workout;
use App\Models\MealPlan;
use App\Models\Article;
use App\Models\Recipe;
use App\Models\Subscription;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Total Users
        $totalUsers = User::role('customer')->count();
        $latestSubscriptions = Subscription::where('stripe_status','active')->latest()->take(5)->get();
        $totalActiveSubscriptions = Subscription::where('stripe_status','active')->where('status', 'active')->count();

        $newUsersThisMonth = User::whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $currentMonth)
                                ->count();

        // Revenue Data
        $totalRevenue = Order::sum('total_amount');
        $revenueThisMonth = Order::whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $currentMonth)
                                ->sum('total_amount');

        // Content Statistics
        $totalWorkouts = Workout::count();
        $totalMealPlans = MealPlan::count();
        $totalArticles = Article::count();
        $totalRecipes = Recipe::count();
        $totalContent = $totalWorkouts + $totalMealPlans + $totalArticles + $totalRecipes;
        $contentThisMonth = Workout::whereYear('created_at', $currentYear)
                                  ->whereMonth('created_at', $currentMonth)
                                  ->count() +
                           MealPlan::whereYear('created_at', $currentYear)
                                   ->whereMonth('created_at', $currentMonth)
                                   ->count() +
                           Article::whereYear('created_at', $currentYear)
                                  ->whereMonth('created_at', $currentMonth)
                                  ->count() +
                           Recipe::whereYear('created_at', $currentYear)
                                 ->whereMonth('created_at', $currentMonth)
                                 ->count();

        // Coach Statistics
        $totalCoaches = User::role('coach')->count();
        $activeCoachesThisWeek = User::role('coach')
                                   ->where('updated_at', '>=', Carbon::now()->subWeek())
                                   ->count();

        // Appointments
        $pendingAppointments = Appointment::where('status', 'pending')->count();

        // E-commerce Statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->count();
        $lowStockProducts = Product::whereBetween('stock_quantity', [1, 10])->count();
        $totalCategories = ProductCategory::count();

        // Orders Statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $ordersThisMonth = Order::whereYear('created_at', $currentYear)
                               ->whereMonth('created_at', $currentMonth)
                               ->count();

        // Revenue Chart Data (last 6 months)
        $revenueLabels = [];
        $revenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenueLabels[] = $date->format('M');
            $revenueData[] = Order::whereYear('created_at', $date->year)
                                 ->whereMonth('created_at', $date->month)
                                 ->sum('total_amount');
        }

        $monthlyActiveUsersLabels = [];
        $monthlyActiveUsersData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $monthlyActiveUsersLabels[] = $date->format('M');

            $monthlyActiveUsersData[] = User::role('customer')
                ->where('is_active', true)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        // dd($monthlyActiveUsersData);
        $latestSubscriptions = Subscription::where('stripe_status', 'active')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = $latestSubscriptions->map(function ($sub) {
            return [
                'icon' => $sub->user?->profile_photo_url ?? asset('assets/images/default.png'),
                'name' => $sub->user?->name ?? 'Unknown User',
                'message' => 'Started a new subscription',
                'time' => $sub->created_at->diffForHumans(),
            ];
        });

        $systemAlerts = Order::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get(['id', 'order_number', 'user_id', 'status', 'order_date', 'created_at', 'total_amount']);

        return view('dashboard', get_defined_vars());
    }

    public function assignDocToPlans(Request $request, $type, $id)
    {
        // dynamic model resolution
        $modelClass = [
            'article' => Article::class,
            'workout' => Workout::class,
        ][$type] ?? null;

        if (!$modelClass) {
            return back()->withErrors('Invalid type');
        }

        $item = $modelClass::findOrFail($id);

        // sync selected plans
        $item->plans()->sync($request->input('plans', []));

        return back()->with('success', ucfirst($type) . ' plans updated successfully!');
    }
}
