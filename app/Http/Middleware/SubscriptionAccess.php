<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredTier = 'free'): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentication required',
                'subscription_required' => true,
                'required_tier' => $requiredTier
            ], 401);
        }

        $userTier = $this->getUserSubscriptionTier($user);

        if (!$this->hasAccess($userTier, $requiredTier)) {
            return response()->json([
                'message' => 'Premium subscription required to access this content',
                'current_tier' => $userTier,
                'required_tier' => $requiredTier,
                'subscription_required' => true,
                'upgrade_url' => '/subscription/plans'
            ], 403);
        }

        // Add subscription info to request for controllers to use
        $request->merge([
            'user_subscription_tier' => $userTier,
            'subscription_access' => true
        ]);

        return $next($request);
    }

    /**
     * Get user's subscription tier.
     */
    private function getUserSubscriptionTier($user): string
    {
        // Check if user has active subscription using Cashier
        if ($user->subscribed()) {
            $subscription = $user->subscription();

            // Get the plan from the subscription
            $plan = \App\Models\Plan::where('stripe_plan_id', $subscription->stripe_price)->first();

            if ($plan) {
                return $plan->isPremium() ? 'premium' : 'free';
            }
        }

        // Check local subscription table as fallback
        $localSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->with('plan')
            ->first();

        if ($localSubscription && $localSubscription->plan) {
            return $localSubscription->plan->isPremium() ? 'premium' : 'free';
        }

        return 'free';
    }

    /**
     * Check if user tier has access to required tier.
     */
    private function hasAccess(string $userTier, string $requiredTier): bool
    {
        $tiers = ['free', 'premium'];

        $userLevel = array_search($userTier, $tiers);
        $requiredLevel = array_search($requiredTier, $tiers);

        return $userLevel !== false && $requiredLevel !== false && $userLevel >= $requiredLevel;
    }
}
