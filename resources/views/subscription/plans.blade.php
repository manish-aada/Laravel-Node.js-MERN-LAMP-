@extends('layouts.app')
@section('content')
<div class="container">
	<h2 class="mt-5 text-center">Choose Your Subscription Plan</h2>
	<div class="row">
		@if($plans->isEmpty())
		<div class="col-12">
			<p class="text-center">No subscription plans found.</p>
		</div>
		@else
		@foreach($plans as $plan)
		<div class="col-md-4">
			<div class="card mb-4">
				<div class="card-body">
					<h5 class="card-title">{{ $plan->name }}</h5>
					<p class="card-text">${{ number_format($plan->amount, 2) }} {{ $plan->currency }} per {{ $plan->interval }}</p>
					@if(isset($plan->subscriptionPlan) && $plan->subscriptionPlan->status == 'active')
					<button class="btn btn-danger mt-2 cancel-btn" data-plan-id="{{ $plan->id }}">
					Cancel Subscription
					</button>
					@else
					<button class="btn btn-primary subscribe-btn" data-plan-id="{{ $plan->id }}">
					Subscribe
					</button>
					@endif
				</div>
			</div>
		</div>
		@endforeach
		@endif
	</div>
	<!-- Payment Form -->
	<div class="container">
		<div class="row">
			<div id="payment-form" class="col-6 d-none mt-4">
				<h5>Payment Details</h5>
				<div id="card-element"></div>
				<button id="submit" class="btn btn-success mt-2">
				<span class="button-text">Pay and Subscribe</span>
				<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</div>
	<!-- Response message -->
	<div id="response-message" class="alert d-none mt-3"></div>
</div>
@endsection
@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
	$(document).ready(function() {
	 const stripe = Stripe('pk_test_51PGeyySIuchlY99zf5gotE0pccx2ak3y28hGz0Cbm4vZigzrQ1ku91qC78C9GyWkginFSEcltnnMZ9zuxmd0Pqvv00ZpxYNsgl'); 
	 const elements = stripe.elements();
	 const cardElement = elements.create('card');
	 cardElement.mount('#card-element');
	
	 let selectedPlanId = null;
	
	 function clearStripeInput() {
	  cardElement.clear();
	 }
	
	$('.subscribe-btn').click(function() {
	  selectedPlanId = $(this).data('plan-id');
	  $('#payment-form').removeClass('d-none'); 
	  clearStripeInput(); 
	});
	
	 
	 $('#submit').click(async function() {
	  $(this).prop('disabled', true); 
	  $('.spinner-border').removeClass('d-none'); 
	
	  const { paymentMethod, error } = await stripe.createPaymentMethod({
		type: 'card',
		card: cardElement,
	  });
	
	  if (error) {
		$('#response-message').removeClass('d-none alert-success').addClass('alert-danger').text(error.message);
		$('#submit').prop('disabled', false);
		$('.spinner-border').addClass('d-none'); 
	  } else {
		
		$.ajax({
			url: "{{ route('subscribe') }}",
			method: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				plan_id: selectedPlanId,
				payment_method_id: paymentMethod.id 
			},
			success: function(response) {
				$('#payment-form').addClass('d-none');
				$('#response-message').removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
			},
			error: function(xhr) {
				$('#response-message').removeClass('d-none alert-success').addClass('alert-danger').text(xhr.responseJSON.message);
			},
			complete: function() {
				$('#submit').prop('disabled', false); 
				$('.spinner-border').addClass('d-none'); 
				setTimeout(function() {
					location.reload();
				}, 3000); 
	
			}
		});
	  }
	 });
	
	
	 $('.cancel-btn').click(function() {
	  const cancelPlanId = $(this).data('plan-id'); 
	
	  $.ajax({
		url: "{{ route('cancel.subscription') }}",
		method: "POST",
		data: {
			_token: "{{ csrf_token() }}",
			plan_id: cancelPlanId 
		},
		success: function(response) {
			$('#response-message').removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
			setTimeout(function() {
				location.reload();
			}, 3000); 
	
		},
		error: function(xhr) {
			$('#response-message').removeClass('d-none alert-success').addClass('alert-danger').text(xhr.responseJSON.message);
		}
	  });
	 });
	});
</script>
@endsection