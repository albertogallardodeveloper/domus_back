<?php

// app/Http/Controllers/Api/AuthController.php
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
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = UserApp::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas.'
            ], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6'
        ]);

        $cached = Cache::get('verify_' . $request->email);

        if ($cached && $cached == $request->code) {
            // Marca como verificado o continúa
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Código incorrecto'], 422);
    }

    /**
     * Verifica el código almacenado en la base de datos para el usuario.
     * Ruta: POST /api/verify-email
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'code'  => 'required|string|size:6',
        ]);

        $user = UserApp::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        // Comprueba que el código coincida con el stored en la BD
        if ($user->email_verification_code !== $request->code) {
            return response()->json(['message' => 'Código incorrecto.'], 422);
        }

        // Marca email como verificado y limpia el código
        $user->email_verified = true;
        $user->email_verification_code = null;
        $user->save();

        return response()->json(['message' => 'Correo verificado correctamente.'], 200);
    }

    /**
     * Reenvía un nuevo código de verificación al correo del usuario.
     * Ruta: POST /api/resend-verification
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);

        $user = UserApp::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        if ($user->email_verified) {
            return response()->json(['message' => 'Correo ya verificado.'], 409);
        }

        // Genera un nuevo código aleatorio de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->save();

        // Envía el correo con el nuevo código
        Mail::to($user->email)->send(new EmailVerificationMail($user, $code));

        return response()->json(['message' => 'Código reenviado.'], 200);
    }
}
