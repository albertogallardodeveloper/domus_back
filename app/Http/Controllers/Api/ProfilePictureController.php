<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller as BaseController;

class ProfilePictureController extends BaseController
{
    public function upload(Request $request)
    {
        // Valida que haya archivo
        $request->validate([
            'file' => 'required|image|max:4096', // 4MB máximo
        ]);

        // Guarda la imagen en storage/app/public/profile_pictures
        $path = $request->file('file')->store('profile_pictures', 'public');
        $url = url('storage/' . $path); // URL pública

        return response()->json(['url' => $url]);
    }
}
