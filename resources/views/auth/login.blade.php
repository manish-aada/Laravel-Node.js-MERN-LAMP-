@extends('layouts.app')
@section('content')
<div class="container mt-5">
	<h2 class="text-center">Login</h2>
	<!-- Flash Messages -->
	@if(session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif

	@if(session('error'))
		<div class="alert alert-danger">
			{{ session('error') }}
		</div>
	@endif
	<form action="{{ route('login') }}" method="POST">
		@csrf
		<div class="form-group">
			<label for="email">Email</label>
			<input type="email" class="form-control" id="email" name="email" required>
			@error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" class="form-control" id="password" name="password" required>
			@error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
		</div>
		<button type="submit" class="btn btn-primary btn-block">Login</button>
	</form>
	<div class="text-center mt-3">
		<p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
	</div>
</div>
@endsection