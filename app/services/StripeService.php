<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Subscription;

class StripeService{

	public function __construct(){

		Stripe::setApiKey(env('STRIPE_SECRET'));
	}

	public function createCustomer($user){

		try {
			return Customer::create(['email' => $user->email]);
		} catch (\Exception $e) {
			
			throw new \Exception("Error creating customer: " . $e->getMessage());
		}
	}

	public function createPaymentMethod($cardData)
	{
		try {
			return PaymentMethod::create($cardData);
		} catch (\Exception $e) {
			throw new \Exception("Error creating payment method: " . $e->getMessage());
		}
	}

	public function attachPaymentMethodToCustomer($customerId, $paymentMethodId)
	{
		try {
			$paymentMethod = PaymentMethod::retrieve($paymentMethodId);
			$paymentMethod->attach(['customer' => $customerId]);
			Customer::update($customerId, [
				'invoice_settings' => [
					'default_payment_method' => $paymentMethodId,
				],
			]);
		} catch (\Exception $e) {
			throw new \Exception("Error attaching payment method: " . $e->getMessage());
		}
	}

	public function createSubscription($customerId, $planPriceId)
	{
		try {
			return Subscription::create([
				'customer' => $customerId,
				'items' => [['price' => $planPriceId]],
			]);
		} catch (\Exception $e) {
			throw new \Exception("Error creating subscription: " . $e->getMessage());
		}
	}

	public function cancelSubscription($subscriptionId)
	{
		try {
			$subscription = Subscription::retrieve($subscriptionId);
			$subscription->cancel();
		} catch (\Exception $e) {
			throw new \Exception("Error canceling subscription: " . $e->getMessage());
		}
	}

	public function getSubscription($subscriptionId)
	{
		try {
			return Subscription::retrieve($subscriptionId);
		} catch (\Exception $e) {
			throw new \Exception("Error retrieving subscription: " . $e->getMessage());
		}
	}

	public function updateSubscription($subscriptionId, $planPriceId)
	{
		try {
			$subscription = Subscription::retrieve($subscriptionId);
			return $subscription->update(['items' => [['id' => $subscription->items->data[0]->id, 'price' => $planPriceId]]]);
		} catch (\Exception $e) {
			throw new \Exception("Error updating subscription: " . $e->getMessage());
		}
	}
}

