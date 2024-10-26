<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
	public function showRegistrationForm()
	{
		return view('auth.register');
	}

	public function register(Request $request){

		$validator = Validator::make($request->all(), [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|confirmed',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		try {
			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => Hash::make($request->password),
			]);

			$request->session()->flash('success', 'Registration successful! You are now log in.');
			return redirect()->route('login');
		} catch (\Exception $e) {
			\Log::error('Registration error: ' . $e->getMessage());

			$request->session()->flash('error', 'Registration failed due to an unexpected error. Please try again.');
			return redirect()->back()->withInput();
		}
	}


	public function showLoginForm()
	{
		return view('auth.login');
	}

	public function login(Request $request){

	$validator = Validator::make($request->all(), [
		'email' => 'required|string|email',
		'password' => 'required|string',
	]);

	if ($validator->fails()) {
		return redirect()->back()->withErrors($validator)->withInput();
	}

	try {
		
		$userExists = User::where('email', $request->email)->exists();

		if (!$userExists) {
			$request->session()->flash('error', 'This email is not registered.');
			return back()->withErrors(['email' => 'This email is not registered.'])->withInput();
		}

		if (Auth::attempt($request->only('email', 'password'))) {
			$request->session()->flash('success', 'You have successfully logged in!');
			return redirect()->route('plans');
		} else {
			$request->session()->flash('error', 'Invalid email or password.');
			return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
		}
	} catch (\Exception $e) {
		Log::error('Login error: ' . $e->getMessage());

		$request->session()->flash('error', 'An unexpected error occurred. Please try again later.');
		return redirect()->back()->withInput();
	}
}



	public function logout()
	{
		Auth::logout();
		return redirect()->route('login');
	}
}

