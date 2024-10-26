<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model{
	
	use HasFactory;

	protected $fillable = [
		'user_id',
		'plan_id',
		'stripe_subscription_id',
		'status',
		'start_date',
		'end_date',
	];
}
