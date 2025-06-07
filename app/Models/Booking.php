<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_app_id',
        'service_id',
        'professional_id', // Nuevo campo
        'price',
        'duration',
        'address',
        'service_day',
        'status',
        'stripe_payment_intent_id',
        'platform_fee',
        'stripe_transfer_id',
        'additional_details',
        'promo_code_id',
        'has_issue',
        'refund_amount',
        'cancelled_at',
        'refund_id',
    ];

    // Cliente que realiza la reserva
    public function user()
    {
        return $this->belongsTo(UserApp::class, 'user_app_id');
    }

    // Servicio reservado
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Profesional que ofrece el servicio (nuevo)
    public function professional()
    {
        return $this->belongsTo(UserApp::class, 'professional_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Alias para el cliente (opcional)
    public function userApp()
    {
        return $this->belongsTo(UserApp::class, 'user_app_id');
    }
}
