<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromoCode;

class PromoCodeController extends Controller
{
    // Listar todos los códigos
    public function index()
    {
        return response()->json(PromoCode::orderBy('created_at', 'desc')->get());
    }

    // Crear un nuevo código
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:promo_codes,code',
            'discount_percent' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
        ]);

        $promo = PromoCode::create([
            'code' => strtoupper($request->code),
            'discount_percent' => $request->discount_percent,
            'expires_at' => $request->expires_at,
            'active' => $request->active ?? true,
        ]);

        return response()->json($promo, 201);
    }

    // Obtener un código concreto
    public function show($id)
    {
        $promo = PromoCode::findOrFail($id);
        return response()->json($promo);
    }

    // Actualizar un código
    public function update(Request $request, $id)
    {
        $promo = PromoCode::findOrFail($id);

        $request->validate([
            'code' => 'string|unique:promo_codes,code,' . $promo->id,
            'discount_percent' => 'integer|min:1|max:100',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
        ]);

        $promo->update($request->only('code', 'discount_percent', 'expires_at', 'active'));

        return response()->json($promo);
    }

    // Eliminar un código
    public function destroy($id)
    {
        $promo = PromoCode::findOrFail($id);
        $promo->delete();

        return response()->json(['message' => 'Promo code deleted successfully.']);
    }

    public function validateCode($code)
    {
        $promo = \App\Models\PromoCode::where('code', strtoupper($code))
            ->where('active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$promo) {
            return response()->json(['error' => 'Invalid or expired promo code.'], 404);
        }

        if (!is_null($promo->max_redemptions) && $promo->redemptions >= $promo->max_redemptions) {
            return response()->json(['error' => 'This promo code has reached its redemption limit.'], 403);
        }

        return response()->json($promo);
    }

}
