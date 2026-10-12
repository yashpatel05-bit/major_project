<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CatalogApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\AddressApiController;
use App\Http\Controllers\Api\FeedbackApiController;
use App\Http\Controllers\Api\DistanceApiController;

use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| RESTful API Routes for Android Mobile Application
|--------------------------------------------------------------------------
*/

// Chatbot API
Route::post('/chat', [ChatbotController::class, 'chat']);

// Auth APIs
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);
Route::get('/profile/{id}', [AuthApiController::class, 'getProfile']);
Route::post('/profile/{id}', [AuthApiController::class, 'updateProfile']);

// Catalog APIs
Route::get('/categories', [CatalogApiController::class, 'getCategories']);
Route::get('/menu', [CatalogApiController::class, 'getMenuItems']);
Route::get('/menu/{id}', [CatalogApiController::class, 'getMenuItem']);

// Orders & Checkout APIs
Route::post('/checkout', [OrderApiController::class, 'checkout']);
Route::post('/verify-payment', [OrderApiController::class, 'verifyPayment']);
Route::get('/orders/user/{userId}', [OrderApiController::class, 'getUserOrders']);
Route::get('/orders/{id}', [OrderApiController::class, 'getOrderDetails']);

// Table Booking APIs
Route::get('/tables', [BookingApiController::class, 'getTables']);
Route::post('/bookings', [BookingApiController::class, 'createBooking']);
Route::get('/bookings/user/{userId}', [BookingApiController::class, 'getUserBookings']);
Route::post('/bookings/{id}/cancel', [BookingApiController::class, 'cancelBooking']);

// Address Management APIs
Route::get('/addresses/user/{userId}', [AddressApiController::class, 'getUserAddresses']);
Route::post('/addresses', [AddressApiController::class, 'storeAddress']);
Route::delete('/addresses/{id}', [AddressApiController::class, 'deleteAddress']);

// Feedback APIs
Route::post('/feedback', [FeedbackApiController::class, 'submitFeedback']);

// Distance Matrix API
Route::post('/calculate-distance', [DistanceApiController::class, 'calculateDistance']);
