<?php

namespace App\Http\Controllers\Api;

use App\Models\UserApp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class AuthController extends Controller
{
    /**
     * Login y emisión de token Sanctum
     * Ruta: POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = UserApp::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas.'
            ], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 200);
    }

    /**
     * Genera y envía un código de verificación previo al registro.
     * Guarda el código en cache por 10 minutos.
     * Ruta: POST /api/send-verification-code
     */
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Si ya existe un usuario con ese email, devolvemos conflicto
        if (UserApp::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Ese correo ya está registrado.'
            ], 409);
        }

        // Genera un código de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lo guarda en cache por 10 minutos
        Cache::put('verify_' . $request->email, $code, now()->addMinutes(10));

        // Envía el correo con email y código
        Mail::to($request->email)
            ->send(new EmailVerificationMail($request->email, $code));

        return response()->json([
            'message' => 'Código de verificación enviado.'
        ], 200);
    }

    /**
     * Verifica el código contra lo guardado en cache.
     * Ruta: POST /api/verify-code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $cached = Cache::get('verify_' . $request->email);

        if ($cached && $cached === $request->code) {
            // Una vez verificado, eliminamos el código de cache
            Cache::forget('verify_' . $request->email);

            return response()->json([
                'message' => 'Email verificado correctamente.'
            ], 200);
        }

        return response()->json([
            'message' => 'Código incorrecto.'
        ], 422);
    }
}
