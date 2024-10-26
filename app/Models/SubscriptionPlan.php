<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model{
	
	use HasFactory;

	protected $fillable = [
		'name',
		'stripe_product_id',
		'stripe_price_id',
		'interval',
		'amount',
		'currency'
	];

	public function subscriptionPlan(){
		
		return $this->hasOne(UserSubscription::class, 'plan_id', 'id')->where('user_id', auth()->id());
	}
}

