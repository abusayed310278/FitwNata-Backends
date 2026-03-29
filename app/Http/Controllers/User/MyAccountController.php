<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class MyAccountController extends Controller
{
    /**
     * Show My Account dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $currentSubscription = null;
        $currentPlan = null;
        $isSubscribed = false;

        // Check if user has active subscription using Cashier
        if ($user->subscribed()) {
            $subscription = $user->subscription();
            $currentPlan = Plan::where('stripe_plan_id', $subscription->stripe_price)->first();
            $isSubscribed = true;

            $currentSubscription = [
                'id' => $subscription->id,
                'status' => $subscription->stripe_status,
                'trial_ends_at' => $subscription->trial_ends_at,
                'ends_at' => $subscription->ends_at,
                'created_at' => $subscription->created_at,
            ];
        } else {
            // Check local subscription as fallback
            $localSubscription = $user->subscriptions()
                ->where('status', 'active')
                ->with('plan')
                ->first();

            if ($localSubscription) {
                $currentPlan = $localSubscription->plan;
                $isSubscribed = true;
                $currentSubscription = $localSubscription;
            }
        }

        $availablePlans = Plan::active()
            ->where('id', '!=', $currentPlan->id ?? 0)
            ->orderBy('price')
            ->get();

        return view('user.my-account.index', compact(
            'user',
            'currentSubscription',
            'currentPlan',
            'isSubscribed',
            'availablePlans'
        ));
    }

    /**
     * Show subscription management.
     */
    public function subscription()
    {
        $user = Auth::user();
        $plans = Plan::active()->orderBy('price')->get();

        $currentSubscription = null;
        $currentPlan = null;

        if ($user->subscribed()) {
            $subscription = $user->subscription();
            $currentPlan = Plan::where('stripe_plan_id', $subscription->stripe_price)->first();
            $currentSubscription = $subscription;
        }

        return view('user.my-account.subscription', compact(
            'user',
            'plans',
            'currentSubscription',
            'currentPlan'
        ));
    }

    /**
     * Show billing history.
     */
    public function billing()
    {
        $user = Auth::user();
        $invoices = [];

        try {
            if ($user->subscribed()) {
                $invoices = collect($user->invoices())->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'amount' => $invoice->total(),
                        'currency' => strtoupper($invoice->currency),
                        'status' => ucfirst($invoice->status),
                        'date' => $invoice->date(),
                        'download_url' => $invoice->hosted_invoice_url,
                    ];
                })->toArray();
            }
        } catch (Exception $e) {
            // Handle case where user doesn't have billing setup
        }

        return view('user.my-account.billing', compact('user', 'invoices'));
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required_unless:plan_id,' . Plan::where('price', 0)->first()?->id,
        ]);

        try {
            $user = Auth::user();
            $plan = Plan::findOrFail($request->plan_id);

            if ($user->subscribed()) {
                return back()->with('error', 'You already have an active subscription.');
            }

            // Handle free plan
            if ($plan->isFree()) {
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'stripe_id' => 'free_' . uniqid(),
                    'status' => 'active',
                    'ends_at' => null,
                ]);

                return back()->with('success', 'Free plan activated successfully!');
            }

            // Handle paid subscription with Stripe
            if (!$request->payment_method) {
                return back()->with('error', 'Payment method is required for paid plans.');
            }

            $user->addPaymentMethod($request->payment_method);
            $subscription = $user->newSubscription('default', $plan->stripe_plan_id)
                ->create($request->payment_method);

            // Create local subscription record
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_id' => $subscription->id,
                'status' => $subscription->stripe_status,
                'trial_ends_at' => $subscription->trial_ends_at,
                'ends_at' => $subscription->ends_at,
            ]);

            return back()->with('success', 'Subscription activated successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to activate subscription: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->subscribed()) {
                return back()->with('error', 'No active subscription found.');
            }

            $subscription = $user->subscription();

            $cancelImmediately = $request->boolean('cancel_immediately', false);

            if ($cancelImmediately) {
                $subscription->cancelNow();
                $message = 'Subscription canceled immediately.';
            } else {
                $subscription->cancel();
                $message = 'Subscription will be canceled at the end of the billing period.';
            }

            // Update local subscription record
            $localSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'status' => $cancelImmediately ? 'canceled' : 'active',
                    'ends_at' => $subscription->ends_at,
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Resume subscription.
     */
    public function resume(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->subscription() || !$user->subscription()->canceled()) {
                return back()->with('error', 'No canceled subscription found.');
            }

            $subscription = $user->subscription()->resume();

            // Update local subscription record
            $localSubscription = Subscription::where('user_id', $user->id)
                ->where('stripe_id', $subscription->id)
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'status' => 'active',
                    'ends_at' => null,
                ]);
            }

            return back()->with('success', 'Subscription resumed successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to resume subscription: ' . $e->getMessage());
        }
    }

    /**
     * Change subscription plan.
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        try {
            $user = Auth::user();
            $newPlan = Plan::findOrFail($request->plan_id);

            if (!$user->subscribed()) {
                return back()->with('error', 'No active subscription found.');
            }

            // Swap to new plan
            $subscription = $user->subscription()->swap($newPlan->stripe_plan_id);

            // Update local subscription record
            $localSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'plan_id' => $newPlan->id,
                ]);
            }

            return back()->with('success', 'Plan changed successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to change plan: ' . $e->getMessage());
        }
    }

    /**
     * Update payment method.
     */
    public function updatePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            $user->addPaymentMethod($request->payment_method);
            $user->updateDefaultPaymentMethod($request->payment_method);

            return back()->with('success', 'Payment method updated successfully!');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to update payment method: ' . $e->getMessage());
        }
    }
}
