<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['address'];

    public function users()
    {
        return $this->belongsToMany(UserApp::class, 'address_user_app');
    }
}
