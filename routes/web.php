<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelTableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PreOrderController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GuestBookingController;

/*
|--------------------------------------------------------------------------
| Public Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');

/*
|--------------------------------------------------------------------------
| Public Chatbot (works for guests AND logged-in users)
|--------------------------------------------------------------------------
*/
Route::post('/chatbot/message', [ChatbotController::class, 'send'])
    ->middleware('throttle:30,1')
    ->name('chatbot.message');

/*
|--------------------------------------------------------------------------
| Guest Booking (no login)
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/book',  [GuestBookingController::class, 'create'])->name('guest.book.create');
    Route::post('/book', [GuestBookingController::class, 'store'])->name('guest.book.store');
    Route::get('/booking/{token}', [GuestBookingController::class, 'show'])->name('guest.book.show');
});

Route::get('/book', [GuestBookingController::class, 'create'])
    ->name('guest.book.create');

Route::post('/book', [GuestBookingController::class, 'store'])
    ->name('guest.book.store');

Route::get('/guest-booking/search', [GuestBookingController::class, 'search'])
    ->name('guest.book.search');

Route::get('/book/confirmation/{token}', [GuestBookingController::class, 'show'])
    ->name('guest.book.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // ═══════════════════════════════════════════
    //  Profile
    // ═══════════════════════════════════════════
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',          [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',        [ProfileController::class, 'update'])->name('update');
        Route::put('/password',  [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/',       [ProfileController::class, 'destroy'])->name('destroy');
    });


    // ═══════════════════════════════════════════
    //  Hotels
    // ═══════════════════════════════════════════
    Route::prefix('hotel')->name('hotel.')->group(function () {
        Route::get('/',  [HotelController::class, 'index'])->name('index');
        Route::post('/', [HotelController::class, 'store'])->name('store');
        Route::put('/',  [HotelController::class, 'update'])->name('update');
    });


    // ═══════════════════════════════════════════
    //  Tables
    // ═══════════════════════════════════════════
    Route::prefix('tables')->name('tables.')->group(function () {
        Route::get('/',        [HotelTableController::class, 'index'])->name('index');
        Route::get('/create',  [HotelTableController::class, 'create'])->name('create');
        Route::post('/',       [HotelTableController::class, 'store'])->name('store');
    });


    // ═══════════════════════════════════════════
    //  Reservations
    //  Custom routes MUST come before the resource
    // ═══════════════════════════════════════════
    Route::post('/reservations/search', [ReservationController::class, 'search'])
        ->name('reservations.search');

    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])
        ->name('reservations.cancel');

    Route::resource('reservations', ReservationController::class);


    // ═══════════════════════════════════════════
    //  Products
    // ═══════════════════════════════════════════
    Route::prefix('products')->name('products.')->group(function () {

        // View (view products)
        Route::middleware('permission:view products')->group(function () {
            Route::get('/',           [ProductController::class, 'index'])->name('index');
            Route::get('/{product}',  [ProductController::class, 'show'])->name('show');
        });

        // Create (create products)
        Route::middleware('permission:create products')->group(function () {
            Route::get('/create',     [ProductController::class, 'create'])->name('create');
            Route::post('/',          [ProductController::class, 'store'])->name('store');
        });

        // Edit (edit products)
        Route::middleware('permission:edit products')->group(function () {
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}',      [ProductController::class, 'update'])->name('update');
        });

        // Delete (delete products)
        Route::delete('/{product}', [ProductController::class, 'destroy'])
            ->middleware('permission:delete products')
            ->name('destroy');
    });


    // ═══════════════════════════════════════════
    //  Pre-Orders
    //  Custom routes MUST come before the {preOrder} wildcard
    // ═══════════════════════════════════════════
    Route::prefix('pre-orders')->name('pre-orders.')->group(function () {

        // List
        Route::get('/', [PreOrderController::class, 'index'])
            ->middleware('permission:view pre-orders')
            ->name('index');

        // Create
        Route::get('/create', [PreOrderController::class, 'create'])
            ->middleware('permission:create pre-orders')
            ->name('create');

        Route::post('/', [PreOrderController::class, 'store'])
            ->middleware('permission:create pre-orders')
            ->name('store');

        // Edit / Update
        Route::get('/{preOrder}/edit', [PreOrderController::class, 'edit'])
            ->middleware('permission:edit pre-orders')
            ->name('edit');

        Route::put('/{preOrder}', [PreOrderController::class, 'update'])
            ->middleware('permission:edit pre-orders')
            ->name('update');

        // Delete
        Route::delete('/{preOrder}', [PreOrderController::class, 'destroy'])
            ->middleware('permission:delete pre-orders')
            ->name('destroy');

        // Show — LAST so /create isn't captured as {preOrder}
        Route::get('/{preOrder}', [PreOrderController::class, 'show'])
            ->middleware('permission:view pre-orders')
            ->name('show');
    });


    // ═══════════════════════════════════════════
    //  Users
    // ═══════════════════════════════════════════
    Route::resource('users', UserController::class);



});


/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze / Jetstream / UI)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
