<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Language;
use App\Models\Location;
use App\Models\Address;

class UserApp extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users_app';

    /**
     * Los campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'profile_picture',
        'email_verified',
        'email_verification_code',
        'phone_number',
        'services',
        'is_client',
        'is_professional',
        'privacy_policy',
        'terms_conditions',
        'stripe_account_id',
        'payout_account_id',
        'payout_iban',
    ];

    /**
     * Los campos ocultos para la serialización.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Los casts de atributos nativos.
     */
    protected $casts = [
        'email_verified'    => 'boolean',
        'is_client'         => 'boolean',
        'is_professional'   => 'boolean',
        'privacy_policy'    => 'boolean',
        'terms_conditions'  => 'boolean',
        'services'          => 'array',
    ];

    /**
     * Relación muchos a muchos con idiomas.
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'language_user_app');
    }

    /**
     * Relación muchos a muchos con ubicaciones.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_user_app');
    }

    /**
     * Relación muchos a muchos con direcciones.
     */
    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'address_user_app');
    }

    /**
     * Relación uno a muchos con reseñas.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relación uno a muchos con bookings.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_app_id');
    }

    /**
     * Relación muchos a muchos con códigos promocionales.
     */
    public function promoCodes()
    {
        return $this->belongsToMany(PromoCode::class, 'promo_code_user')->withTimestamps();
    }

    /**
     * Relación muchos a muchos con conversaciones.
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user_app');
    }

    /**
     * Relación uno a muchos con mensajes enviados.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
