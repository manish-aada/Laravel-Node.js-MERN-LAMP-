<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\SubscriptionPlan;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class SyncStripePlans extends Command
{
	protected $signature = 'stripe:sync-plans';
	protected $description = 'Sync Stripe products and prices to local subscription_plans table';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		Stripe::setApiKey(env('STRIPE_SECRET'));

		try {
			$products = Product::all(['limit' => 100]); // Adjust limit as needed
			foreach ($products->data as $product) {
				$prices = Price::all(['product' => $product->id]);

				foreach ($prices->data as $price) {
					SubscriptionPlan::updateOrCreate(
						['stripe_price_id' => $price->id],
						[
							'name' => $product->name,
							'stripe_product_id' => $product->id,
							'stripe_price_id' => $price->id,
							'interval' => $price->recurring->interval, // e.g., monthly, yearly
							'amount' => $price->unit_amount / 100, // Convert from cents to dollars
							'currency' => $price->currency,
						]
					);
				}
			}
			$this->info('Subscription plans synced successfully.');
		} catch (\Exception $e) {
			\Log::error('Failed to sync Stripe plans: ' . $e->getMessage());
			$this->error('Failed to sync Stripe plans. Check logs for details.');
		}
	}
}
