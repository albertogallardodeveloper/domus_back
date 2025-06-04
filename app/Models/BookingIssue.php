<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_app_id',
        'message'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(UserApp::class, 'user_app_id');
    }
}
