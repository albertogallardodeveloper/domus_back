<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['type', 'booking_id'];

    public function participants()
    {
        return $this->belongsToMany(UserApp::class, 'conversation_user_app');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
