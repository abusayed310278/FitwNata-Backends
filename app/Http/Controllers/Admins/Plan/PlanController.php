<?php

namespace App\Http\Controllers\Admins\Plan;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class PlanController extends Controller
{
    /**
     * Display a listing of the plans.
     */
    public function index()
    {
        $totalPlans = Plan::count();
        $activePlans = Plan::where('is_active', true)->count();
        $inactivePlans = Plan::where('is_active', false)->count();
        $freePlans = Plan::where('price', 0)->count();
        $premiumPlans = Plan::where('price', '>', 0)->count();

        return view('admins.plan.index', compact(
            'totalPlans', 'activePlans', 'inactivePlans', 'freePlans', 'premiumPlans'
        ));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        return view('admins.plan.create');
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|in:month,year',
            'stripe_plan_id' => 'nullable|string|max:255|unique:plans,stripe_plan_id',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
        ]);

        // Convert features array to JSON and handle boolean fields
        $features = $validated['features'] ?? [];
        $validated['features'] = array_values(array_filter($features, function($feature) {
            return !empty(trim($feature));
        }));
        $validated['is_active'] = $request->has('is_active');
        $validated['is_popular'] = $request->has('is_popular');

        // Handle trial days - ensure it's properly set
        if (!isset($validated['trial_days']) || $validated['trial_days'] === null) {
            $validated['trial_days'] = 0;
        }

        try {
            Plan::create($validated);

            return redirect()->route('plan.index')
                ->with('success', 'Plan created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating plan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan)
    {
        $subscriptionsCount = $plan->subscriptions()->count();
        $activeSubscriptionsCount = $plan->subscriptions()
            ->where('stripe_status', 'active')
            ->count();

        return view('admins.plan.show', compact('plan', 'subscriptionsCount', 'activeSubscriptionsCount'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan)
    {
        return view('admins.plan.edit', compact('plan'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|in:month,year',
            'stripe_plan_id' => 'nullable|string|max:255|unique:plans,stripe_plan_id,' . $plan->id,
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
        ]);

        // Convert features array to JSON
        $features = $validated['features'] ?? [];
        $validated['features'] = array_values(array_filter($features, function($feature) {
            return !empty(trim($feature));
        }));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_popular'] = $request->boolean('is_popular');

        $plan->update($validated);

        return redirect()->route('plan.index')
            ->with('success', 'Plan updated successfully!');
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan)
    {
        try {
            // Check if plan has active subscriptions
            $activeSubscriptions = $plan->subscriptions()
                ->where('stripe_status', 'active')
                ->count();

            if ($activeSubscriptions > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete plan with active subscriptions. Please cancel all subscriptions first.'
                ], 400);
            }

            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Plan deleted successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plans data for DataTables.
     */
    public function getPlans(Request $request)
    {
        $plans = Plan::latest();

        return DataTables::of($plans)
            ->addIndexColumn()
            ->editColumn('name', function($row) {
                $badge = $row->is_popular ? '<span class="badge bg-warning text-dark ms-2">Popular</span>' : '';
                return $row->name . $badge;
            })
            ->editColumn('price', function($row) {
                if ($row->price == 0) {
                    return '<span class="text-success fw-bold">FREE</span>';
                }
                return '<span class="fw-bold">$' . number_format($row->price, 2) . '</span>';
            })
            ->editColumn('interval', function($row) {
                return ucfirst($row->interval);
            })
            ->editColumn('features', function($row) {
                $features = is_array($row->features) ? $row->features : [];
                $count = count($features);
                return '<span class="badge bg-info">' . $count . ' features</span>';
            })
            ->editColumn('is_active', function($row) {
                $checked = $row->is_active ? 'checked' : '';
                $status = $row->is_active ? 'Active' : 'Inactive';
                $badgeClass = $row->is_active ? 'bg-success' : 'bg-secondary';
                return '<span class="badge ' . $badgeClass . '">' . $status . '</span>';
            })
            ->editColumn('subscriptions_count', function($row) {
                $count = $row->subscriptions()->count();
                return '<span class="badge bg-primary">' . $count . ' subscriptions</span>';
            })
            ->addColumn('action', function($row) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('plan.show', $row->id) . '" class="btn btn-sm btn-outline-info" title="View">';
                $actions .= '<i class="fas fa-eye"></i></a>';
                $actions .= '<a href="' . route('plan.edit', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Edit">';
                $actions .= '<i class="fas fa-edit"></i></a>';
                $actions .= '<button onclick="deletePlan(' . $row->id . ')" class="btn btn-sm btn-outline-danger" title="Delete">';
                $actions .= '<i class="fas fa-trash"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['name', 'price', 'features', 'is_active', 'subscriptions_count', 'action'])
            ->make(true);
    }

    /**
     * Toggle plan status.
     */
    public function toggleStatus(Request $request, Plan $plan)
    {
        try {
            $plan->update([
                'is_active' => !$plan->is_active
            ]);

            $status = $plan->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Plan {$status} successfully!",
                'is_active' => $plan->is_active
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating plan status: ' . $e->getMessage()
            ], 500);
        }
    }
}
