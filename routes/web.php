<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
	return view('welcome');
});

Route::middleware('auth')->group(function () {
	Route::get('/plans', [SubscriptionController::class, 'showPlans'])->name('plans');
	Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
	Route::post('/cancel-subscription', [SubscriptionController::class, 'cancel'])->name('cancel.subscription');
});


Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');    