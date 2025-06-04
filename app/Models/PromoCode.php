<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'discount_percent',
        'expires_at',
        'active',
        'max_redemptions',
        'redemptions',
    ];


    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function users()
    {
        return $this->belongsToMany(UserApp::class, 'promo_code_user')
            ->withPivot('booking_id', 'used_at')
            ->withTimestamps();
    }


}
