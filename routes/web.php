<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página de bienvenida
Route::get('/', function () {
    return view('admin.login');
});

// Login y logout (admin)
Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
Route::post('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Panel de administración (todo bajo /admin, protegido por auth)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard principal
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // Gestión de usuarios de la app
    Route::resource('users-app', App\Http\Controllers\Admin\UserAppController::class);

    // Gestión de países, ubicaciones, FAQs, direcciones, categorías, servicios
    Route::resource('countries', App\Http\Controllers\Admin\CountryController::class)->except('show');
    Route::resource('locations', App\Http\Controllers\Admin\LocationController::class)->except('show');
    Route::resource('faqs', App\Http\Controllers\Admin\FaqController::class)->except('show');
    Route::resource('addresses', App\Http\Controllers\Admin\AddressController::class)->except('show');
    Route::resource('service-categories', App\Http\Controllers\Admin\ServiceCategoryController::class)->except('show');
    Route::resource('services', App\Http\Controllers\Admin\ServiceController::class)->except('show');

    // Chats privados entre usuarios
    Route::get('conversations', [App\Http\Controllers\Admin\ConversationController::class, 'index'])->name('conversations.index');
    Route::get('conversations/{conversation}', [App\Http\Controllers\Admin\ConversationController::class, 'show'])->name('conversations.show');

    // Chats de soporte (admin <-> usuario)
    Route::get('support-chats', [App\Http\Controllers\Admin\SupportChatController::class, 'index'])->name('support-chats.index');
    Route::get('support-chats/{conversation}', [App\Http\Controllers\Admin\SupportChatController::class, 'show'])->name('support-chats.show');
    Route::post('support-chats/{conversation}/reply', [App\Http\Controllers\Admin\SupportChatController::class, 'reply'])->name('support-chats.reply');
    Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class)->except('show');
Route::post('bookings/{booking}/cancel', [App\Http\Controllers\Admin\BookingController::class, 'cancel'])->name('bookings.cancel');


    Route::resource('promo-codes', App\Http\Controllers\Admin\PromoCodeController::class)->except('show');
    Route::resource('languages', App\Http\Controllers\Admin\LanguageController::class)->except('show');

});
Route::middleware(['auth'])->prefix('admin/stripe')->name('admin.stripe.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\StripeController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\Admin\StripeController::class, 'show'])->name('show');
    Route::post('/{id}/refund', [App\Http\Controllers\Admin\StripeController::class, 'refund'])->name('refund');
});

Route::get('/politica-de-privacidad', function () {
    return view('public.privacy-policy');
})->name('privacy-policy.public');
