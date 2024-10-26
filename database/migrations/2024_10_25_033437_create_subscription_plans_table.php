<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscription_plans', function (Blueprint $table) {
			$table->id();
			$table->string('name');                      
			$table->string('stripe_product_id');
			$table->string('stripe_price_id')->unique();
			$table->string('interval');                  
			$table->decimal('amount', 8, 2);             
			$table->string('currency', 3);               
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('subscription_plans');
	}
}
