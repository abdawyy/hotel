<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Language Switcher Route
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rooms Routes (Public)
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');
Route::post('/rooms/check-availability', [RoomController::class, 'checkAvailability'])->name('rooms.check-availability');

// Booking Routes
Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/confirmation/{id}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

// PayPal Payment Routes
Route::get('/booking/{id}/payment', [PayPalController::class, 'showPayment'])->name('paypal.payment');
Route::post('/paypal/create-order', [PayPalController::class, 'createOrder'])->name('paypal.create-order');
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder'])->name('paypal.capture-order');
Route::get('/paypal/capture', [PayPalController::class, 'capture'])->name('paypal.capture');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

// Stripe Payment Routes
Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent'])->name('stripe.create-payment-intent');
Route::post('/stripe/confirm-payment', [StripeController::class, 'confirmPayment'])->name('stripe.confirm-payment');
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

// Authentication Routes (Laravel Breeze)
require __DIR__.'/auth.php';

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::put('/user/profile', [UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    
    // User Booking Actions
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    
    // Profile Routes (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes (Protected by is_admin middleware)
Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Room Management Routes
    Route::resource('rooms', AdminRoomController::class);
    Route::post('/rooms/{roomId}/update-status', [AdminRoomController::class, 'updateRoomStatus'])->name('rooms.update-status');
    
    // Booking Management Routes
    Route::resource('bookings', AdminBookingController::class);
    Route::put('/bookings/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/booking-details/{id}/assign-room', [AdminBookingController::class, 'assignRoom'])->name('booking-details.assign-room');
    Route::post('/bookings/{id}/payment', [AdminBookingController::class, 'createPayment'])->name('bookings.create-payment');
    
    // Customer Management Routes
    Route::resource('customers', CustomerController::class);
    
    // Settings Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    
    // Payment Management Routes
    Route::resource('payments', PaymentController::class);
    Route::post('/payments/{id}/refund-paypal', [PayPalController::class, 'refund'])->name('payments.refund-paypal');
    Route::post('/payments/{id}/refund-stripe', [StripeController::class, 'refund'])->name('payments.refund-stripe');
    Route::get('/payments/{id}/status', [PayPalController::class, 'getPaymentStatus'])->name('payments.status');
    
    // Amenity Management Routes
    Route::resource('amenities', AmenityController::class);
    
    // Admin Management Routes
    Route::resource('admins', AdminController::class);
});
