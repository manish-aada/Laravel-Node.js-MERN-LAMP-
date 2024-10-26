<?php

namespace App\Http\Controllers;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Auth;

class SubscriptionController extends Controller
{
	protected $stripeService;

	public function __construct(StripeService $stripeService){
		$this->stripeService = $stripeService;
	}

	public function showPlans(){
		$user = Auth::user();
		$plans = SubscriptionPlan::with('subscriptionPlan')->get();
		return view('subscription.plans', compact('plans'));
	}


	public function subscribe(Request $request){
	
		$request->validate([
			'plan_id' => 'required|exists:subscription_plans,id',
			'payment_method_id' => 'required|string', // Validate only payment method ID
		]);

		$plan = SubscriptionPlan::find($request->plan_id);
		$user = Auth::user();

		$customer = $user->stripe_customer_id ?? $this->stripeService->createCustomer($user);

		$this->stripeService->attachPaymentMethodToCustomer($customer->id, $request->payment_method_id);

		$subscription = $this->stripeService->createSubscription($customer->id, $plan->stripe_price_id);

		UserSubscription::create([
			'user_id' => $user->id,
			'plan_id' => $plan->id,
			'stripe_subscription_id' => $subscription->id,
			'status' => 'active',
			'start_date' => now(),
			'end_date' => now()->addDays($this->getPlanDurationDays($plan)),
		]);
		return response()->json(['message' => 'Subscribed successfully!']);
	}

	public function cancel(Request $request){

		$subscription = UserSubscription::where('user_id', Auth::id())
										 ->where('status', 'active')
										 ->first();

		if ($subscription && $subscription->stripe_subscription_id) {
			$this->stripeService->cancelSubscription($subscription->stripe_subscription_id);
			$subscription->delete();
			return response()->json(['message' => 'Subscription canceled successfully!']);
		}
		return response()->json(['message' => 'Subscription not found.'], 404);
	}


	private function getPlanDurationDays($plan){
		switch ($plan->interval) {
			case 'week': return 7;
			case 'month': return 30;
			case 'year': return 365;
			case 'day': return 1;
			default: return 0;
		}
	}
}
