<?php

namespace App\Http\Controllers\Admins\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // User Analytics
        $userGrowth = $this->getUserGrowthData();
        $usersByRole = $this->getUsersByRole();

        // Revenue Analytics
        $revenueData = $this->getRevenueData();
        $subscriptionMetrics = $this->getSubscriptionMetrics();

        // Content Analytics
        $contentMetrics = $this->getContentMetrics();

        // Appointment Analytics
        $appointmentMetrics = $this->getAppointmentMetrics();

        return view('admins.report.index', compact(
            'userGrowth',
            'usersByRole',
            'revenueData',
            'subscriptionMetrics',
            'contentMetrics',
            'appointmentMetrics'
        ));
    }

    public function exportUsers(Request $request)
    {
        $users = User::with(['roles', 'subscriptions'])
                    ->when($request->role, function($query, $role) {
                        return $query->role($role);
                    })
                    ->when($request->from_date, function($query, $date) {
                        return $query->whereDate('created_at', '>=', $date);
                    })
                    ->when($request->to_date, function($query, $date) {
                        return $query->whereDate('created_at', '<=', $date);
                    })
                    ->get();

        $filename = 'users_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Role', 'Status', 'Subscriptions',
                'Created At', 'Last Login'
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name . ' ' . $user->last_name,
                    $user->email,
                    $user->roles->pluck('name')->implode(', '),
                    $user->email_verified_at ? 'Active' : 'Suspended',
                    $user->subscriptions->count(),
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRevenue(Request $request)
    {
        $orders = Order::with('user')
                      ->when($request->from_date, function($query, $date) {
                          return $query->whereDate('created_at', '>=', $date);
                      })
                      ->when($request->to_date, function($query, $date) {
                          return $query->whereDate('created_at', '<=', $date);
                      })
                      ->get();

        $filename = 'revenue_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Order ID', 'Customer', 'Amount', 'Status', 'Date'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name,
                    $order->total_amount,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSubscriptions(Request $request)
    {
        $subscriptions = Subscription::with(['user', 'plan'])
                                   ->when($request->status, function($query, $status) {
                                       return $query->where('status', $status);
                                   })
                                   ->when($request->from_date, function($query, $date) {
                                       return $query->whereDate('created_at', '>=', $date);
                                   })
                                   ->get();

        $filename = 'subscriptions_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscriptions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Customer', 'Plan', 'Status', 'Started', 'Ends'
            ]);

            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->id,
                    $subscription->user->name,
                    $subscription->plan->name,
                    $subscription->status,
                    $subscription->created_at->format('Y-m-d'),
                    $subscription->ends_at?->format('Y-m-d') ?? 'Ongoing',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getUserGrowthData()
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->where('created_at', '>=', Carbon::now()->subDays(30))
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
    }

    private function getUsersByRole()
    {
        return DB::table('model_has_roles')
                 ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                 ->select('roles.name', DB::raw('COUNT(*) as count'))
                 ->groupBy('roles.name')
                 ->get();
    }

    private function getRevenueData()
    {
        return Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
                   ->where('created_at', '>=', Carbon::now()->subDays(30))
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
    }

    private function getSubscriptionMetrics()
    {
        return [
            'active' => Subscription::where('status', 'active')->count(),
            'canceled' => Subscription::where('status', 'canceled')->count(),
            'total_revenue' => Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                                         ->where('subscriptions.status', 'active')
                                         ->sum('plans.price'),
        ];
    }

    private function getContentMetrics()
    {
        return [
            'articles' => Article::count(),
            'published_articles' => Article::whereNotNull('published_at')->count(),
            'recent_articles' => Article::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];
    }

    private function getAppointmentMetrics()
    {
        return [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'today' => Appointment::whereDate('scheduled_at', today())->count(),
        ];
    }
}
