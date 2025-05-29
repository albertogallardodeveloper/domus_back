<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\Api\UserAppController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\UserAppAddressController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\PromoCodeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Ruta protegida para obtener usuario autenticado (opcional si usas login con Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Usuarios de la app
Route::apiResource('users-app', UserAppController::class); // POST: /api/users-app

// Países y ubicaciones
Route::apiResource('countries', CountryController::class);
Route::apiResource('locations', LocationController::class);

// Idiomas
Route::apiResource('languages', LanguageController::class);

// Categorías de servicio y servicios
Route::apiResource('service-categories', ServiceCategoryController::class);
Route::apiResource('services', ServiceController::class);

// Preguntas frecuentes
Route::apiResource('faqs', FaqController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email-code', [AuthController::class, 'verifyCode']);
Route::get('/services/by-category/{id}', [ServiceController::class, 'byCategory']);
Route::apiResource('reviews', ReviewController::class);
Route::get('/services/{id}/reviews', [ReviewController::class, 'byService']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-addresses', [AddressController::class, 'index']);
    Route::post('/user-addresses', [AddressController::class, 'store']);
});

Route::get('/users-app/{id}/addresses', [UserAppAddressController::class, 'index']);
Route::post('/users-app/{id}/addresses', [UserAppController::class, 'addAddress']);

Route::post('/create-payment-intent', [StripePaymentController::class, 'createPaymentIntent']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::get('user/{userId}/bookings', [\App\Http\Controllers\Api\BookingController::class, 'getByUser']);
Route::apiResource('promo-codes', PromoCodeController::class);
Route::get('/promo-codes/validate/{code}', [PromoCodeController::class, 'validateCode']);
