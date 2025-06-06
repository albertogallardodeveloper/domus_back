<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserAppController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\UserAppAddressController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\PromoCodeController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ProfilePictureController;

Route::post('/upload/profile-picture', [ProfilePictureController::class, 'upload']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔐 Usuario autenticado (solo si usas Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 👤 Usuarios de la app
Route::apiResource('users-app', UserAppController::class);
Route::get('/users-app/{id}/addresses', [UserAppAddressController::class, 'index']);
Route::post('/users-app/{id}/addresses', [UserAppController::class, 'addAddress']);

// 📍 Países y ubicaciones
Route::apiResource('countries', CountryController::class);
Route::apiResource('locations', LocationController::class);

// 🌐 Idiomas
Route::apiResource('languages', LanguageController::class);

// ❓ Preguntas frecuentes
Route::apiResource('faqs', FaqController::class);

// 🛠️ Servicios y categorías
Route::apiResource('service-categories', ServiceCategoryController::class);
Route::apiResource('services', ServiceController::class);
Route::get('/services/by-category/{id}', [ServiceController::class, 'byCategory']);

// ⭐ Reseñas
Route::apiResource('reviews', ReviewController::class);
Route::get('/services/{id}/reviews', [ReviewController::class, 'byService']);

// 🏷️ Códigos promocionales
Route::apiResource('promo-codes', PromoCodeController::class);
Route::get('/promo-codes/validate/{code}', [PromoCodeController::class, 'validateCode']);

// 💳 Stripe
Route::post('/create-payment-intent', [StripePaymentController::class, 'createPaymentIntent']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

// 🔐 Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email-code', [AuthController::class, 'verifyCode']);

// 🏠 Direcciones del usuario (autenticado)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-addresses', [AddressController::class, 'index']);
    Route::post('/user-addresses', [AddressController::class, 'store']);

    // 📆 Bookings
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/user/{userId}/bookings', [BookingController::class, 'getByUser']);
    Route::post('/bookings/{id}/confirm-service', [BookingController::class, 'confirmService']);
    Route::post('/bookings/{id}/report-issue', [BookingController::class, 'reportIssue']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // 💬 Chat (con soporte y entre usuarios)
    Route::prefix('chat')->group(function () {
        Route::post('/support', [ChatController::class, 'getOrCreateSupportConversation']); // POST con user_app_id
        Route::get('/conversations/{id}/messages', [ChatController::class, 'getMessages']);
        Route::post('/conversations/{id}/messages', [ChatController::class, 'sendMessage']);
    });
});

Route::middleware('auth:sanctum')->get('/bookings/{id}/conversation', [BookingController::class, 'getSupportConversation']);


Route::get('/chat/conversations/{conversation}/messages', [MessageController::class, 'index']);
Route::middleware('auth:sanctum')->get('/chat/booking-conversation/{id}', [BookingController::class, 'getOrCreateBookingConversation']);
Route::middleware('auth:sanctum')->post('/chat/user-conversation', [ChatController::class, 'getOrCreatePrivateConversation']);
Route::get('/bookings/{id}/conversation', [ChatController::class, 'getSupportConversationForBooking']);
Route::post('/bookings/{id}/accept', [BookingController::class, 'acceptBooking']);
Route::post('/bookings/{id}/reject', [BookingController::class, 'rejectBooking']);
Route::get('/bookings/professional/{id}/pending', function ($id) {
    return \App\Models\Booking::with(['user', 'service'])
        ->whereHas('service', function ($q) use ($id) {
            $q->where('user_app_id', $id);
        })
        ->where('status', 'pending')
        ->orderBy('service_day')
        ->get();
});
Route::get('/bookings/professional/{id}/accepted', function ($id) {
    return \App\Models\Booking::with(['user', 'service'])
        ->whereHas('service', function ($q) use ($id) {
            $q->where('user_app_id', $id);
        })
        ->where('status', 'confirmed')
        ->orderBy('service_day')
        ->get();
});
// routes/api.php
Route::get('/bookings/professional/{id}/all', function ($id) {
    return \App\Models\Booking::with(['user', 'service'])
        ->whereHas('service', function ($q) use ($id) {
            $q->where('user_app_id', $id);
        })
        ->orderBy('service_day')
        ->get();
});

Route::get('/languages', [UserAppController::class, 'languages']);
Route::get('/locations', [UserAppController::class, 'locations']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reviews/professional', [ReviewController::class, 'reviewsForProfessional']);
    Route::get('/reviews/client', [ReviewController::class, 'reviewsByClient']);
});
Route::apiResource('reviews', ReviewController::class)->only(['index', 'store']);
