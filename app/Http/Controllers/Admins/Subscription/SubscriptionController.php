<?php

namespace App\Http\Controllers\Admins\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionController extends Controller
{
    public function index()
    {
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $totalRevenue = Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                                  ->sum('plans.price');

        return view('admins.subscription.index', compact('totalSubscriptions', 'activeSubscriptions', 'totalRevenue'));
    }

    public function create()
    {
        $users = User::doesntHave('subscriptions')->get();
        $plans = Plan::all();
        return view('admins.subscription.create', compact('users', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'stripe_id' => 'required|string|unique:subscriptions',
            'status' => 'required|string',
            'trial_ends_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        Subscription::create($validated);

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription created successfully!');
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        return view('admins.subscription.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $plans = Plan::all();
        return view('admins.subscription.edit', compact('subscription', 'plans'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|string',
            'trial_ends_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $subscription->update($validated);

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription updated successfully!');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('subscription.index')
            ->with('success', 'Subscription deleted successfully!');
    }

    public function getSubscriptions(Request $request)
    {
        if ($request->ajax()) {
            $subscriptions = Subscription::with(['user', 'plan']);

            // ✅ Apply status filter if provided
            $status = $request->get('status');
            if ($status && $status !== 'all') {
                // whitelist to avoid invalid values
                $allowed = ['active','canceled','past_due','unpaid'];
                if (in_array($status, $allowed, true)) {
                    $subscriptions->where('status', $status);
                }
            }

            return DataTables::of($subscriptions)
                ->addIndexColumn()
                ->editColumn('user_id', function($row) {
                    return '
                        <div>
                            <strong>'.$row->user->name.'</strong><br>
                            <small class="text-muted">'.$row->user->email.'</small>
                        </div>';
                })
                ->editColumn('plan_id', function($row) {
                    return '
                        <div>
                            <strong>'.$row->plan->name.'</strong><br>
                            <small class="text-muted">$'.$row->plan->price.'/'.$row->plan->interval.'</small>
                        </div>';
                })
                ->editColumn('status', function($row) {
                    $colors = [
                        'active'   => 'success',
                        'canceled' => 'danger',
                        'past_due' => 'warning',
                        'unpaid'   => 'danger',
                    ];
                    $color = $colors[$row->status] ?? 'secondary';
                    return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
                })
                ->editColumn('created_at', fn($row) => optional($row->created_at)->format('d/m/Y'))
                ->editColumn('ends_at', fn($row) => $row->ends_at ? $row->ends_at->format('d/m/Y') : '—')
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('subscription.show', $row->id).'">
                                    <i class="ti-eye"></i> View
                                </a></li>
                                <li><a class="dropdown-item" href="'.route('subscription.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="'.route('subscription.destroy', $row->id).'" method="POST" class="d-inline">
                                        '.csrf_field().'
                                        '.method_field('DELETE').'
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure?\')">
                                            <i class="ti-trash"></i> Cancel
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['user_id', 'plan_id', 'status', 'actions'])
                ->make(true);
        }
    }

    public function cancelSubscription(Subscription $subscription)
    {
        $subscription->update(['status' => 'canceled', 'ends_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription canceled successfully!'
        ]);
    }
}
