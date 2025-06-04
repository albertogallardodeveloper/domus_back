<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'service_id', 'user_app_id', 'rating', 'comment'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(UserApp::class, 'user_app_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

}
