<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_app_id',
        'service_id',
        'price',
        'duration',
        'address',
        'service_day',
        'status',
        'stripe_payment_intent_id',
        'additional_details',
        'promo_code_id',
    ];

    public function user()
    {
        return $this->belongsTo(UserApp::class, 'user_app_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

}
