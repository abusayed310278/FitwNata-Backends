<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Plan;
use Stripe\SetupIntent;
use Stripe\StripeClient;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseApiController;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends BaseApiController
{
    /**
     * Get available plans.
     */
    public function plans(): JsonResponse
    {
        $plans = Plan::active()->orderBy('price')->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'price' => $plan->price,
                'formatted_price' => $plan->formatted_price,
                'interval' => $plan->interval,
                'type' => $plan->type,
                'features' => $plan->features ?? [],
                'is_popular' => $plan->is_popular,
                'stripe_plan_id' => $plan->stripe_plan_id,
            ];
        });

        return $this->success($plans, 'Plans retrieved successfully');
    }

    /**
     * Get current subscription.
     */
    public function current(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $stripe = new StripeClient(config('services.stripe.secret'));

            $localSubscription = Subscription::with('plan')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where('stripe_status', 'active')
                ->orderby('id', 'DESC')
                ->first();
            
            $plan = $localSubscription?->plan;

            // If customer has free plan
            if(!$user->stripe_customer_id || $plan->type == 'free')
            {
                $subscription = Subscription::with('plan')
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('stripe_status', 'active')
                    ->orderBy('id', 'DESC')
                    ->first();

                $plan = $subscription?->plan;
                
                return $this->success([
                    'subscription' => [
                        'id'             => $subscription->id,
                        'stripe_id'      => $subscription->stripe_id,
                        'status'         => $subscription->status,
                        'trial_ends_at'  => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                        'ends_at'        => $subscription->current_period_end ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                        'created_at'     => date('Y-m-d H:i:s', $subscription->created),
                    ],
                    'plan' => $plan ? [
                        'id'       => $plan->id,
                        'name'     => $plan->name,
                        'price'    => $plan->price,
                        'interval' => $plan->interval,
                    ] : null,
                    'status'           => $subscription->status,
                    'has_subscription' => true
                ], 'Current subscription retrieved');
            }

            // Get subscriptions from Stripe
            $subscriptions = $stripe->subscriptions->all([
                'customer' => $user->stripe_customer_id,
                'status'   => 'all',
                'limit'    => 1, // latest subscription
            ]);

            if (count($subscriptions->data) === 0) {
                return $this->success([
                    'subscription'      => null,
                    'plan'              => null,
                    'status'            => 'inactive',
                    'has_subscription'  => false
                ], 'No active subscription');
            }

            $subscription = $subscriptions->data[0];

            if ($subscription->status == 'canceled') {
                $subscription = Subscription::with('plan')
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('stripe_status', 'active')
                    ->first();

                $plan = $subscription?->plan;

                return $this->success([
                    'subscription' => [
                        'id'             => $subscription->id,
                        'stripe_id'      => $subscription->id,
                        'status'         => $subscription->status,
                        'trial_ends_at'  => $subscription->trial_end
                            ? ($subscription->trial_end instanceof \Carbon\Carbon
                                ? $subscription->trial_end->format('Y-m-d H:i:s')
                                : date('Y-m-d H:i:s', $subscription->trial_end))
                            : null,
                        'ends_at'        => $subscription->current_period_end
                            ? ($subscription->current_period_end instanceof \Carbon\Carbon
                                ? $subscription->current_period_end->format('Y-m-d H:i:s')
                                : date('Y-m-d H:i:s', $subscription->current_period_end))
                            : null,
                        'created_at'     => $subscription->created_at instanceof \Carbon\Carbon
                            ? $subscription->created_at->format('Y-m-d H:i:s')
                            : date('Y-m-d H:i:s', $subscription->created_at),
                    ],
                    'plan' => $plan ? [
                        'id'       => $plan->id,
                        'name'     => $plan->name,
                        'price'    => $plan->price,
                        'interval' => $plan->interval,
                    ] : null,
                    'status'           => $subscription->status,
                    'has_subscription' => true
                ], 'Current subscription retrieved');
            }

            // Get plan details from local DB
            $plan = Plan::where('stripe_plan_id', $subscription->items->data[0]->price->id ?? null)->first();

            return $this->success([
                'subscription' => [
                    'id'             => $subscription->id,
                    'stripe_id'      => $subscription->id,
                    'status'         => $subscription->status,
                    'trial_ends_at'  => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                    'ends_at'        => $subscription->current_period_end ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                    'created_at'     => date('Y-m-d H:i:s', $subscription->created),
                ],
                'plan' => $plan ? [
                    'id'       => $plan->id,
                    'name'     => $plan->name,
                    'price'    => $plan->price,
                    'interval' => $plan->interval,
                ] : null,
                'status'           => $subscription->status,
                'has_subscription' => true
            ], 'Current subscription retrieved');

        } catch (\Exception $e) {
            return $this->serverError('Failed to retrieve subscription: ' . $e->getMessage());
        }
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = $request->user();
            $plan = Plan::findOrFail($request->plan_id);

            $current_subscription = Subscription::with('plan')->where('user_id', $user->id)
            ->where('stripe_status', 'active')
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->first();

            if($current_subscription)
            {
                if($current_subscription->plan->id == $request->plan_id)
                {
                    return response()->json([
                        'status' => false, 
                        'message' => 'You are already subscribe to ' .$current_subscription->plan->name . '. Please select any other plan',
                    ]);
                }
            }

            // For free plans, create subscription without payment
            if ($plan->isFree()) {
                // Create a local subscription record for free plan
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'stripe_id' => 'free_' . uniqid(),
                    'status' => 'active',
                    'stripe_status'  => 'active',
                    'ends_at' => null,
                ]);

                return $this->success([
                    'subscription' => $subscription,
                    'plan' => $plan,
                    'message' => 'Free plan activated successfully'
                ], 'Subscription created successfully');
            }

            $stripe = new StripeClient(config('services.stripe.secret'));

            $subscriptions = $stripe->subscriptions->all([
                'customer' => $user->stripe_customer_id,
                'status' => 'active', // 'active', 'canceled', 'incomplete' etc. use kar sakte ho
            ]);

            if (count($subscriptions->data) > 0) {
                return $this->swap($request);
                return $this->error('You already have an active subscription');
            }

            $stripe->paymentMethods->attach(
                $request->payment_method,
                ['customer' => $user->stripe_customer_id]
            );

            // Set as default payment method
            $stripe->customers->update($user->stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method,
                ],
            ]);

            // Create subscription
            $subscription = $stripe->subscriptions->create([
                'customer' => $user->stripe_customer_id,
                'items' => [[
                    'price' => $plan->stripe_plan_id,
                ]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);


            $endDate   = Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString();

            // Create local subscription record
            $localSubscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_id' => $subscription->id,
                'status' => $subscription->status,
                'stripe_status' => $subscription->status,
                'trial_ends_at' => $endDate,
                'ends_at' => $endDate,
            ]);

            return $this->success([
                'subscription' => $localSubscription,
                'plan' => $plan,
                'stripe_subscription' => [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                ]
            ], 'Subscription created successfully');

        } catch (IncompletePayment $exception) {
            return $this->error('Payment requires additional confirmation', [
                'payment_intent' => [
                    'id' => $exception->payment->id,
                    'client_secret' => $exception->payment->client_secret,
                ]
            ]);
        } catch (Exception $e) {
            return $this->serverError('Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Subscribe to a plan.
     */
    public function _subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = $request->user();
            $plan = Plan::findOrFail($request->plan_id);

            // Check if user already has active subscription
            if ($user->subscribed()) {
                return $this->error('You already have an active subscription');
            }

            // For free plans, create subscription without payment
            if ($plan->isFree()) {
                // Create a local subscription record for free plan
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'stripe_id' => 'free_' . uniqid(),
                    'status' => 'active',
                    'ends_at' => null, // Free plans don't expire
                ]);

                return $this->success([
                    'subscription' => $subscription,
                    'plan' => $plan,
                    'message' => 'Free plan activated successfully'
                ], 'Subscription created successfully');
            }

            // Add payment method to user
            $user->addPaymentMethod($request->payment_method);

            // Create Stripe subscription
            $subscription = $user->newSubscription('default', $plan->stripe_plan_id)
                ->create($request->payment_method);

            // Create local subscription record
            $localSubscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_id' => $subscription->id,
                'status' => $subscription->stripe_status,
                'trial_ends_at' => $subscription->trial_ends_at,
                'ends_at' => $subscription->ends_at,
            ]);

            return $this->success([
                'subscription' => $localSubscription,
                'plan' => $plan,
                'stripe_subscription' => [
                    'id' => $subscription->id,
                    'status' => $subscription->stripe_status,
                ]
            ], 'Subscription created successfully');

        } catch (IncompletePayment $exception) {
            return $this->error('Payment requires additional confirmation', [
                'payment_intent' => [
                    'id' => $exception->payment->id,
                    'client_secret' => $exception->payment->client_secret,
                ]
            ]);
        } catch (Exception $e) {
            return $this->serverError('Failed to create subscription: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $current_subscription = Subscription::with('plan')
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('stripe_status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->first();

            $plan = $current_subscription?->plan;

            if($plan->type == 'free')
            {
                return response()->json([
                    'status' => false,
                    'message' => 'You are currently subscribe to free plan which cannot be cancelled.',
                ]);
            }

            $stripe = new StripeClient(config('services.stripe.secret'));

            // Stripe par se subscriptions nikaalo
            $subscriptions = $stripe->subscriptions->all([
                'customer' => $user->stripe_customer_id,
                'status'   => 'active',
                'limit'    => 1, // ek hi active subscription le aayega
            ]);

            if (count($subscriptions->data) === 0) {
                return $this->error('No active subscription found');
            }

            $subscription = $subscriptions->data[0]; // first active subscription

            // Cancel immediately or at period end
            $cancelImmediately = $request->boolean('cancel_immediately', false);

            if ($cancelImmediately) {
                $canceled = $stripe->subscriptions->cancel($subscription->id, []);
                $message = 'Subscription canceled immediately';
            } else {
                $canceled = $stripe->subscriptions->update($subscription->id, [
                    'cancel_at_period_end' => true,
                ]);
                $message = 'Subscription will be canceled at the end of the billing period';
            }

            // Update local subscription record
            $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'status'  => $cancelImmediately ? 'canceled' : 'active',
                    'ends_at' => $cancelImmediately
                        ? now()
                        : \Carbon\Carbon::createFromTimestamp($canceled->current_period_end),
                ]);
            }

            return $this->success(null, $message);

        } catch (Exception $e) {
            return $this->serverError('Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    public function resume(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $stripe = new StripeClient(config('services.stripe.secret'));

            // Pehle Stripe se subscriptions nikaal lo
            $subscriptions = $stripe->subscriptions->all([
                'customer' => $user->stripe_customer_id,
                'status'   => 'all', // include canceled / incomplete / active
                'limit'    => 1,
            ]);

            if (count($subscriptions->data) === 0) {
                return $this->error('No subscription found');
            }

            $subscription = $subscriptions->data[0];

            // Check if subscription canceled_at_period_end par hai
            if (!$subscription->cancel_at_period_end) {
                return $this->error('No canceled subscription found to resume');
            }

            // Resume subscription -> cancel_at_period_end ko false set karo
            $resumed = $stripe->subscriptions->update($subscription->id, [
                'cancel_at_period_end' => false,
            ]);

            // Update local subscription record
            $localSubscription = Subscription::where('user_id', $user->id)
                ->where('stripe_id', $subscription->id)
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'status'  => 'active',
                    'ends_at' => null,
                ]);
            }

            return $this->success(null, 'Subscription resumed successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to resume subscription: ' . $e->getMessage());
        }
    }

    /**
     * Update payment method.
     */

    public function updatePayment(Request $request): JsonResponse
    {
         \Log::warning('Validation failed for updatePayment: ' . json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            \Log::warning('Validation failed for updatePayment: ' . json_encode($validator->errors()));
            return $this->validationError($validator->errors());
        }

        try {
            $user = $request->user();

            \Log::info('Updating payment method for user ID: ' . $user->id);
            \Log::info('Payment method ID: ' . $request->payment_method);

            // Validate that the payment method ID looks like a valid Stripe payment method ID
            if (!str_starts_with($request->payment_method, 'pm_')) {
                \Log::warning('Invalid payment method ID format: ' . $request->payment_method);
                return $this->error('Invalid payment method ID format. Must start with "pm_"');
            }

            // Check if payment method exists and is attached to user
            $paymentMethod = $user->findPaymentMethod($request->payment_method);
            if (!$paymentMethod) {
                \Log::warning('Payment method not found for user. Attempting to add it.');
                // Try to add the payment method
                try {
                    $user->addPaymentMethod($request->payment_method);
                    \Log::info('Payment method added successfully');
                    // Reload the payment method to ensure it's attached
                    $paymentMethod = $user->findPaymentMethod($request->payment_method);
                    if (!$paymentMethod) {
                        \Log::error('Payment method still not found after adding');
                        return $this->error('Failed to attach payment method to user account');
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to add payment method: ' . $e->getMessage());
                    return $this->serverError('Failed to add payment method: ' . $e->getMessage());
                }
            } else {
                \Log::info('Payment method found and already attached to user');
            }

            // Add and set as default payment method
            $user->updateDefaultPaymentMethod($request->payment_method);

            \Log::info('Payment method set as default successfully');

            return $this->success(null, 'Payment method updated successfully');

        } catch (Exception $e) {
            \Log::error('Failed to update payment method: ' . $e->getMessage());
            return $this->serverError('Failed to update payment method: ' . $e->getMessage());
        }
    }

    /**
     * Create a setup intent for saving payment methods.
     */

    public function createSetupIntent(Request $request): JsonResponse {
        try {

            $user = $request->user();
            $stripe = new StripeClient(config('services.stripe.secret'));
            if (!$user->stripe_customer_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name'  => $user->name ?? $user->email,
                ]);

                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            $setupIntent = \Stripe\SetupIntent::create([
                'usage' => 'on_session',
                'customer' => $user->stripe_customer_id,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ], [
                'api_key' => config('services.stripe.secret')
            ]);

            $responseData = [
                'client_secret' => $setupIntent->client_secret,
                'setup_intent_id' => $setupIntent->id,
                'customer_id' => $user->stripe_customer_id,
            ];

            // Return the client secret and customer ID
            return $this->success($responseData, 'Setup Intent created successfully');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API Error for user ID ' . ($request->user()->id ?? 'unknown') . ': ' . $e->getMessage());
            \Log::error('Stripe API Error Trace: ' . $e->getTraceAsString());
            return $this->serverError('Stripe API Error: ' . $e->getMessage());
        } catch (Exception $e) {
            \Log::error('Setup Intent Creation Error for user ID ' . ($request->user()->id ?? 'unknown') . ': ' . $e->getMessage());
            \Log::error('Setup Intent Creation Error Trace: ' . $e->getTraceAsString());
            return $this->serverError('Failed to create setup intent: ' . $e->getMessage());
        }
    }

    /**
     * Get the latest payment method for the user.
     */
    public function getLatestPaymentMethod(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $stripe = new StripeClient(config('services.stripe.secret'));

             if (!$user->stripe_customer_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name'  => $user->name ?? $user->email,
                ]);

                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            $paymentMethods = $stripe->paymentMethods->all([
                'customer' => $user->stripe_customer_id,
                'type'     => 'card',
                'limit'    => 1,
            ]);

            if (count($paymentMethods->data) > 0) {
                $latestPaymentMethod = $paymentMethods->data[0];

                // Stripe se customer retrieve karo to check default payment method
                $customer = $stripe->customers->retrieve($user->stripe_customer_id);

                $isDefault = $customer->invoice_settings->default_payment_method === $latestPaymentMethod->id;

                $method = [
                    'id'        => $latestPaymentMethod->id,
                    'type'      => $latestPaymentMethod->type,
                    'brand'     => $latestPaymentMethod->card->brand ?? null,
                    'last4'     => $latestPaymentMethod->card->last4 ?? null,
                    'exp_month' => $latestPaymentMethod->card->exp_month ?? null,
                    'exp_year'  => $latestPaymentMethod->card->exp_year ?? null,
                    'is_default'=> $isDefault,
                ];

                return $this->success($method, 'Latest payment method retrieved successfully');
            } else {
                return $this->success(null, 'No payment methods found');
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API Error for user ID ' . ($request->user()->id ?? 'unknown') . ': ' . $e->getMessage());
            \Log::error('Stripe API Error Trace: ' . $e->getTraceAsString());
            return $this->serverError('Stripe API Error: ' . $e->getMessage());
        } catch (Exception $e) {
            \Log::error('Failed to retrieve latest payment method: ' . $e->getMessage());
            \Log::error('Error Trace: ' . $e->getTraceAsString());
            return $this->serverError('Failed to retrieve latest payment method: ' . $e->getMessage());
        }
    }

    /**
     * Get payment Methods.
     */
    public function paymentMethods(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $stripe = new StripeClient(config('services.stripe.secret'));

            $paymentMethods = $stripe->paymentMethods->all([
                'customer' => $user->stripe_customer_id,
                'type'     => 'card',
            ]);

            if (!empty($paymentMethods->data)) {
                $methods = collect($paymentMethods->data)->map(function ($method) use ($user) {
                    $isDefault = $method->id === optional($user->defaultPaymentMethod())->id;

                    return [
                        'id'        => $method->id,
                        'type'      => $method->type,
                        'brand'     => $method->card?->brand,
                        'last4'     => $method->card?->last4,
                        'exp_month' => $method->card?->exp_month,
                        'exp_year'  => $method->card?->exp_year,
                        'is_default'=> $isDefault,
                    ];
                });

                return $this->success($methods, 'All payment methods retrieved successfully');
            } else {
                return $this->success([], 'No payment methods found');
            }

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve payment methods: ' . $e->getMessage());
            return $this->serverError('Failed to retrieve payment methods: ' . $e->getMessage());
        }
        // try {
        //     $user = $request->user();

        //     \Log::info('Retrieving payment methods for user ID: ' . $user->id);

        //     $paymentMethods = $user->paymentMethods();
        //     \Log::info('Found ' . $paymentMethods->count() . ' payment methods');

        //     $defaultPaymentMethod = $user->defaultPaymentMethod();
        //     \Log::info('Default payment method ID: ' . ($defaultPaymentMethod ? $defaultPaymentMethod->id : 'none'));

        //     $methods = $paymentMethods->map(function ($method) use ($user) {
        //         \Log::info('Processing payment method: ' . $method->id . ', Type: ' . $method->type);
        //         $isDefault = $method->id === optional($user->defaultPaymentMethod())->id;
        //         \Log::info('Is default: ' . ($isDefault ? 'yes' : 'no'));

        //         return [
        //             'id' => $method->id,
        //             'type' => $method->type,
        //             'brand' => $method->card?->brand,
        //             'last4' => $method->card?->last4,
        //             'exp_month' => $method->card?->exp_month,
        //             'exp_year' => $method->card?->exp_year,
        //             'is_default' => $isDefault,
        //         ];
        //     });

        //     \Log::info('Returning ' . $methods->count() . ' payment methods in response');
        //     return $this->success($methods, 'Payment methods retrieved successfully');
        // } catch (Exception $e) {
        //     \Log::error('Failed to retrieve payment methods: ' . $e->getMessage());
        //     return $this->serverError('Failed to retrieve payment methods: ' . $e->getMessage());
        // }
    }

    /**
     * Get billing history.
     */
    public function billingHistory(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $invoices = collect($user->invoices())->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'amount' => $invoice->total(),
                    'currency' => $invoice->currency,
                    'status' => $invoice->status,
                    'date' => $invoice->date(),
                    'download_url' => $invoice->hosted_invoice_url,
                ];
            });

            return $this->success($invoices, 'Billing history retrieved');

        } catch (Exception $e) {
            return $this->serverError('Failed to retrieve billing history: ' . $e->getMessage());
        }
    }

    /**
     * Swap subscription plan.
     */
    // public function swap(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'plan_id' => 'required|exists:plans,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->validationError($validator->errors());
    //     }

    //     try {
    //         $user = $request->user();
    //         $newPlan = Plan::findOrFail($request->plan_id);

    //         if (!$user->subscribed()) {
    //             return $this->error('No active subscription found');
    //         }

    //         // Swap to new plan
    //         $subscription = $user->subscription()->swap($newPlan->stripe_plan_id);

    //         // Update local subscription record
    //         $localSubscription = Subscription::where('user_id', $user->id)
    //             ->where('status', 'active')
    //             ->first();

    //         if ($localSubscription) {
    //             $localSubscription->update([
    //                 'plan_id' => $newPlan->id,
    //             ]);
    //         }

    //         return $this->success([
    //             'subscription' => $subscription,
    //             'new_plan' => $newPlan,
    //         ], 'Plan changed successfully');

    //     } catch (Exception $e) {
    //         return $this->serverError('Failed to change plan: ' . $e->getMessage());
    //     }
    // }

    // public function swap(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'plan_id' => 'required|exists:plans,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->validationError($validator->errors());
    //     }

    //     try {
    //         $user = $request->user();
    //         $newPlan = Plan::findOrFail($request->plan_id);

    //         $localSubscription = Subscription::where('user_id', $user->id)
    //             ->whereIn('status', ['active', 'trialing'])
    //             ->latest()
    //             ->first();

    //         if (!$localSubscription) {
    //             return $this->error('No active subscription found');
    //         }

    //         $stripe = new StripeClient(config('services.stripe.secret'));

    //         /**
    //          * CASE 1: Swapping TO a FREE plan
    //          */
    //         if ($newPlan->isFree() || empty($newPlan->stripe_plan_id)) {
    //             // Cancel the Stripe subscription immediately
    //             try {
    //                 $stripe->subscriptions->cancel($localSubscription->stripe_id, []);
    //             } catch (\Exception $e) {
    //                 // Log but continue — even if already canceled
    //                 \Log::warning('Stripe cancel failed or already canceled: ' . $e->getMessage());
    //             }

    //             // Update local subscription
    //             $localSubscription->update([
    //                 'plan_id' => $newPlan->id,
    //                 'stripe_status' => 'active',
    //                 'status' => 'active', // free plan active locally
    //                 'stripe_id' => 'free_' . uniqid(),
    //                 'ends_at' => null,
    //             ]);

    //             return $this->success([
    //                 'subscription' => $localSubscription,
    //                 'new_plan' => $newPlan,
    //             ], 'Switched to free plan successfully');
    //         }

    //         /**
    //          * CASE 2: Swapping TO a PAID plan
    //          */
    //         $stripeSubscription = $stripe->subscriptions->retrieve($localSubscription->stripe_id);

    //         $stripe->subscriptions->update($localSubscription->stripe_id, [
    //             'cancel_at_period_end' => false,
    //             'items' => [[
    //                 'id'    => $stripeSubscription->items->data[0]->id,
    //                 'price' => $newPlan->stripe_plan_id,
    //             ]],
    //         ]);

    //         $localSubscription->update([
    //             'plan_id' => $newPlan->id,
    //             'stripe_status' => 'active',
    //         ]);

    //         return $this->success([
    //             'subscription' => $localSubscription,
    //             'new_plan' => $newPlan,
    //         ], 'Plan changed successfully');

    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         return $this->serverError('Stripe API error: ' . $e->getMessage());
    //     } catch (Exception $e) {
    //         return $this->serverError('Failed to change plan: ' . $e->getMessage());
    //     }
    // }

    // public function swap(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'plan_id' => 'required|exists:plans,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->validationError($validator->errors());
    //     }

    //     try {
    //         $user = Auth::user();
    //         $newPlan = Plan::findOrFail($request->plan_id);

    //         $localSubscription = Subscription::where('user_id', $user->id)
    //             ->where('status', 'active')
    //             ->where('stripe_status', 'active')
    //             ->orderBy('id', 'DESC')
    //             ->first();

    //         if (!$localSubscription) {
    //             return $this->error('No active subscription found');
    //         }

    //         // Check if the user is already on the new plan
    //         if ($localSubscription->plan_id == $newPlan->id) {
    //             return $this->error('You are already subscribed to this plan');
    //         }

    //         $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

    //         /**
    //          * 🟩 CASE 1: Swapping TO a FREE plan
    //          */
    //         if ($newPlan->isFree() || empty($newPlan->stripe_plan_id)) {
    //             // Cancel Stripe subscription if exists
    //             if (str_starts_with($localSubscription->stripe_id, 'sub_')) {
    //                 try {
    //                     $stripe->subscriptions->cancel($localSubscription->stripe_id, []);
    //                 } catch (\Exception $e) {
    //                     \Log::warning('Stripe cancel failed or already canceled: ' . $e->getMessage());
    //                 }
    //             }

    //             $localSubscription->update([
    //                 'plan_id'       => $newPlan->id,
    //                 'stripe_id'     => 'free_' . uniqid(),
    //                 'stripe_status' => 'active',
    //                 'status'        => 'active',
    //                 'ends_at'       => null,
    //             ]);

    //             return $this->success([
    //                 'subscription' => $localSubscription,
    //                 'new_plan' => $newPlan,
    //             ], 'Switched to free plan successfully');
    //         }

    //         /**
    //          * CASE 2: Swapping FROM a FREE plan TO a PAID plan
    //          */
    //         if (str_starts_with($localSubscription->stripe_id, 'free_')) {
    //             // Create a new Stripe subscription for this user
    //             $newStripeSubscription = $stripe->subscriptions->create([
    //                 'customer' => $user->stripe_customer_id,
    //                 'items' => [[ 'price' => $newPlan->stripe_plan_id ]],
    //                 'expand' => ['latest_invoice.payment_intent'],
    //             ]);

    //             $endDate = \Carbon\Carbon::createFromTimestamp($newStripeSubscription->current_period_end)->toDateTimeString();

    //             $localSubscription->update([
    //                 'plan_id'       => $newPlan->id,
    //                 'stripe_id'     => $newStripeSubscription->id,
    //                 'stripe_status' => $newStripeSubscription->status,
    //                 'status'        => 'active',
    //                 'trial_ends_at' => $endDate,
    //                 'ends_at'       => $endDate,
    //             ]);

    //             return $this->success([
    //                 'subscription' => $localSubscription,
    //                 'new_plan' => $newPlan,
    //             ], 'Switched to paid plan successfully');
    //         }

    //         /**
    //          * 🟦 CASE 3: Swapping BETWEEN PAID plans
    //          */
    //         $stripeSubscription = $stripe->subscriptions->retrieve($localSubscription->stripe_id);

    //         $stripe->subscriptions->update($localSubscription->stripe_id, [
    //             'cancel_at_period_end' => false,
    //             'items' => [[
    //                 'id'    => $stripeSubscription->items->data[0]->id,
    //                 'price' => $newPlan->stripe_plan_id,
    //             ]],
    //         ]);

    //         $localSubscription->update([
    //             'plan_id'       => $newPlan->id,
    //             'stripe_status' => 'active',
    //         ]);

    //         return $this->success([
    //             'subscription' => $localSubscription,
    //             'new_plan' => $newPlan,
    //         ], 'Plan changed successfully');

    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         return $this->serverError('Stripe API error: ' . $e->getMessage());
    //     } catch (\Exception $e) {
    //         return $this->serverError('Failed to change plan: ' . $e->getMessage());
    //     }
    // }

    public function swap(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = Auth::user();
            $newPlan = Plan::findOrFail($request->plan_id);
            
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            // Ensure Stripe customer exists
            if (!$user->stripe_customer_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name'  => $user->name ?? $user->email,
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }
            
            // Ensure customer has a default payment method
            $customer = $stripe->customers->retrieve($user->stripe_customer_id);
            if (!$customer->invoice_settings->default_payment_method) {
                if ($request->filled('payment_method')) {
                    try {
                        // Attach payment method if not attached
                        $stripe->paymentMethods->attach(
                            $request->payment_method,
                            ['customer' => $user->stripe_customer_id]
                        );
            
                        // Set as default
                        $stripe->customers->update($user->stripe_customer_id, [
                            'invoice_settings' => [
                                'default_payment_method' => $request->payment_method,
                            ],
                        ]);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to attach or set default payment method: ' . $e->getMessage());
                        return $this->error('Please provide a valid payment method to proceed.');
                    }
                } else {
                    return $this->error('No default payment method found. Please add one.');
                }
            }

            $localSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('stripe_status', 'active')
                ->orderBy('id', 'DESC')
                ->first();

            if (!$localSubscription) {
                return $this->error('No active subscription found');
            }

            // Check if the user is already on the new plan
            if ($localSubscription->plan_id == $newPlan->id) {
                return $this->error('You are already subscribed to this plan');
            }

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            /**
             * ðŸŸ© CASE 1: Swapping TO a FREE plan
             */
            if ($newPlan->isFree() || empty($newPlan->stripe_plan_id)) {
                // Cancel Stripe subscription if exists
                if (str_starts_with($localSubscription->stripe_id, 'sub_')) {
                    try {
                        $stripe->subscriptions->cancel($localSubscription->stripe_id, []);
                    } catch (\Exception $e) {
                        \Log::warning('Stripe cancel failed or already canceled: ' . $e->getMessage());
                    }
                }

                $localSubscription->update([
                    'plan_id'       => $newPlan->id,
                    'stripe_id'     => 'free_' . uniqid(),
                    'stripe_status' => 'active',
                    'status'        => 'active',
                    'ends_at'       => null,
                ]);

                return $this->success([
                    'subscription' => $localSubscription,
                    'new_plan' => $newPlan,
                ], 'Switched to free plan successfully');
            }

            /**
             * CASE 2: Swapping FROM a FREE plan TO a PAID plan
             */
            if (str_starts_with($localSubscription->stripe_id, 'free_')) {
                // Create a new Stripe subscription for this user
                $newStripeSubscription = $stripe->subscriptions->create([
                    'customer' => $user->stripe_customer_id,
                    'items' => [[ 'price' => $newPlan->stripe_plan_id ]],
                    'expand' => ['latest_invoice.payment_intent'],
                ]);

                $endDate = \Carbon\Carbon::createFromTimestamp($newStripeSubscription->current_period_end)->toDateTimeString();

                $localSubscription->update([
                    'plan_id'       => $newPlan->id,
                    'stripe_id'     => $newStripeSubscription->id,
                    'stripe_status' => $newStripeSubscription->status,
                    'status'        => 'active',
                    'trial_ends_at' => $endDate,
                    'ends_at'       => $endDate,
                ]);

                return $this->success([
                    'subscription' => $localSubscription,
                    'new_plan' => $newPlan,
                ], 'Switched to paid plan successfully');
            }

            /**
             * ðŸŸ¦ CASE 3: Swapping BETWEEN PAID plans
             */
            $stripeSubscription = $stripe->subscriptions->retrieve($localSubscription->stripe_id);

            $stripe->subscriptions->update($localSubscription->stripe_id, [
                'cancel_at_period_end' => false,
                'items' => [[
                    'id'    => $stripeSubscription->items->data[0]->id,
                    'price' => $newPlan->stripe_plan_id,
                ]],
            ]);

            $localSubscription->update([
                'plan_id'       => $newPlan->id,
                'stripe_status' => 'active',
            ]);

            return $this->success([
                'subscription' => $localSubscription,
                'new_plan' => $newPlan,
            ], 'Plan changed successfully');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return $this->serverError('Stripe API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->serverError('Failed to change plan: ' . $e->getMessage());
        }
    }

    public function setDefaultPaymentMethod(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            $paymentMethod = $user->findPaymentMethod($id);

            if (!$paymentMethod) {
                return $this->error('Payment method not found', 404);
            }

            $user->updateDefaultPaymentMethod($paymentMethod->id);

            return $this->success(null, 'Default payment method updated successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to set default payment method: ' . $e->getMessage());
        }
    }

    /**
     * Delete a payment method from the user's account.
     */
    // public function deletePaymentMethod(Request $request, $id): JsonResponse
    // {
    //     try {
    //         $user = $request->user();
    //         $paymentMethod = $user->findPaymentMethod($id);

    //         if (!$paymentMethod) {
    //             return $this->error('Payment method not found', 404);
    //         }

    //         $paymentMethod->delete();

    //         return $this->success(null, 'Payment method deleted successfully');
    //     } catch (\Exception $e) {
    //         return $this->serverError('Failed to delete payment method: ' . $e->getMessage());
    //     }
    // }

    public function deletePaymentMethod(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            $stripe = new StripeClient(config('services.stripe.secret'));

            // dd($user);

            // Validate customer exists
            if (!$user->stripe_customer_id) {
                return $this->error('User does not have a Stripe customer account', 400);
            }

            // Retrieve customer
            $customer = $stripe->customers->retrieve($user->stripe_customer_id);

            // If it's the default, remove it first
            if ($customer->invoice_settings->default_payment_method === $id) {
                $stripe->customers->update($user->stripe_customer_id, [
                    'invoice_settings' => ['default_payment_method' => null]
                ]);
            }

            // Detach the payment method
            $stripe->paymentMethods->detach($id);

            return $this->success(null, 'Payment method deleted successfully');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return $this->serverError('Stripe API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->serverError('Failed to delete payment method: ' . $e->getMessage());
        }
    }
}
