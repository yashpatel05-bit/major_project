<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProfileController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DeliveryPersonController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('customer.home');
Route::get('/menu', [MenuController::class, 'index'])->name('customer.menu');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('customer.cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('customer.cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('customer.cart.update');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('customer.cart.remove');

// Authenticated Customer Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('customer.checkout.place');
    
    Route::get('/table-booking', [CustomerBookingController::class, 'index'])->name('customer.booking');
    Route::post('/table-booking', [CustomerBookingController::class, 'store'])->name('customer.booking.store');

    Route::get('/my-orders', [CustomerOrderController::class, 'index'])->name('customer.orders');
    Route::get('/my-orders/{id}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/feedback', [CustomerOrderController::class, 'submitFeedback'])->name('customer.feedback.store');

    Route::get('/profile', [ProfileController::class, 'index'])->name('customer.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('customer.profile.update');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('customer.profile.address');
});

/*
|--------------------------------------------------------------------------
| Admin Management Dashboard Routes (/admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::post('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::get('/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Menu Items Management
    Route::get('/menu', [MenuItemController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuItemController::class, 'store'])->name('menu.store');
    Route::post('/menu/{id}', [MenuItemController::class, 'update'])->name('menu.update');
    Route::get('/menu/delete/{id}', [MenuItemController::class, 'destroy'])->name('menu.destroy');

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Customer Management
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');

    // Table Bookings Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{id}', [AdminBookingController::class, 'update'])->name('bookings.update');

    // Payments Tracking
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Delivery Personnel Management
    Route::get('/delivery-persons', [DeliveryPersonController::class, 'index'])->name('delivery.index');
    Route::post('/delivery-persons', [DeliveryPersonController::class, 'store'])->name('delivery.store');
    Route::post('/delivery-persons/{id}', [DeliveryPersonController::class, 'update'])->name('delivery.update');
    Route::get('/delivery-persons/delete/{id}', [DeliveryPersonController::class, 'destroy'])->name('delivery.destroy');

    // Feedback Management
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

    // Invoice Generation
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');

    // Café Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
