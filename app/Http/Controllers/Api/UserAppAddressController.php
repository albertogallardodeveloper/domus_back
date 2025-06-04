<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserApp;
use Illuminate\Http\Request;

class UserAppAddressController extends Controller
{
    public function index($id)
    {
        $user = UserApp::with('addresses')->findOrFail($id);
        return response()->json($user->addresses);
    }
}
