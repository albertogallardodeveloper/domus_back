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
use App\Http\Controllers\Api\StripeController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Subida de foto de perfil
Route::post('/upload/profile-picture', [ProfilePictureController::class, 'upload']);

// 🔐 Autenticación y verificación
Route::post('/login',                  [AuthController::class, 'login']);
Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);
Route::post('/verify-code',            [AuthController::class, 'verifyCode']);
Route::post('/verify-email-code',      [AuthController::class, 'verifyCode']);
Route::post('/verify-email',           [AuthController::class, 'verifyEmail']);
Route::post('/resend-verification',    [AuthController::class, 'resendVerification']);

// 🔒 Password reset (no requiere token)
Route::post('/forgot-password', [UserAppController::class, 'forgotPassword']);
Route::post('/reset-password',  [UserAppController::class, 'resetPassword']);

// Rutas que requieren estar autenticado con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Obtener usuario logueado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 👤 CRUD de usuarios
    Route::apiResource('users-app', UserAppController::class);

    // Añadir dirección a usuario
    Route::get('/users-app/{id}/addresses', [UserAppAddressController::class, 'index']);
    Route::post('/users-app/{id}/addresses', [UserAppController::class, 'addAddress']);

    // 💳 Guardar método de cobro (IBAN / payout)
    Route::post(
        '/users-app/{id}/payout-method',
        [UserAppController::class, 'updatePayoutMethod']
    );

    // 🌐 Listas globales
    Route::get('/languages', [UserAppController::class, 'languages']);
    Route::get('/locations', [UserAppController::class, 'locations']);

    // 📍 Países y ubicaciones
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('locations', LocationController::class);

    // 🌐 Idiomas completos
    Route::apiResource('languages', LanguageController::class);

    // ❓ FAQs
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

    // 💳 Stripe: crear intent y webhook
    Route::post('/create-payment-intent', [StripePaymentController::class, 'createPaymentIntent']);
    Route::post('/stripe/webhook',        [StripeWebhookController::class, 'handle']);

    // 🏠 Direcciones de usuario
    Route::get('/user-addresses',  [AddressController::class, 'index']);
    Route::post('/user-addresses', [AddressController::class, 'store']);

    // 📆 Bookings
    Route::post('/bookings',                         [BookingController::class, 'store']);
    Route::get('/user/{userId}/bookings',            [BookingController::class, 'getByUser']);
    Route::post('/bookings/{id}/confirm-service',    [BookingController::class, 'confirmService']);
    Route::post('/bookings/{id}/report-issue',       [BookingController::class, 'reportIssue']);
    Route::post('/bookings/{booking}/cancel',        [BookingController::class, 'cancel']);

    // 💬 Chat protegido
    Route::prefix('chat')->group(function () {
        Route::post('/support',                  [ChatController::class, 'getOrCreateSupportConversation']);
        Route::get('/conversations/{id}/messages',[ChatController::class, 'getMessages']);
        Route::post('/conversations/{id}/messages',[ChatController::class, 'sendMessage']);
    });

    // Rutas protegidas de reviews por rol
    Route::get('/reviews/professional', [ReviewController::class, 'reviewsForProfessional']);
    Route::get('/reviews/client',       [ReviewController::class, 'reviewsByClient']);
});

// 💬 Chat y bookings sin token (lectura pública)
Route::get('/chat/conversations/{conversation}/messages', [ChatController::class, 'index']);
Route::get('/bookings/{id}/conversation',                 [ChatController::class, 'getSupportConversationForBooking']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chat/booking-conversation/{id}', [BookingController::class, 'getOrCreateBookingConversation']);
    Route::post('/chat/user-conversation',       [ChatController::class, 'getOrCreatePrivateConversation']);
});

// Aceptar/rechazar booking (público)
Route::post('/bookings/{id}/accept', [BookingController::class, 'acceptBooking']);
Route::post('/bookings/{id}/reject', [BookingController::class, 'rejectBooking']);

// Consultas profesionales de bookings
Route::get('/bookings/professional/{id}/pending', function ($id) {
    return \App\Models\Booking::with(['user','service'])
        ->whereHas('service', fn($q)=> $q->where('user_app_id',$id))
        ->where('status','pending')
        ->orderBy('service_day')->get();
});
Route::get('/bookings/professional/{id}/accepted', function ($id) {
    return \App\Models\Booking::with(['user','service'])
        ->where('professional_id', $id) // CAMBIADO: busca por el profesional asignado, no por el servicio
        ->where('status','confirmed')
        ->orderBy('service_day')->get();
});

Route::get('/bookings/professional/{id}/all', function ($id) {
    return \App\Models\Booking::with(['user','service'])
        ->whereHas('service', fn($q)=> $q->where('user_app_id',$id))
        ->orderBy('service_day')->get();
});

Route::post('/stripe/connect/onboarding-link', [StripeController::class, 'createOnboardingLink'])->middleware('auth:api');
