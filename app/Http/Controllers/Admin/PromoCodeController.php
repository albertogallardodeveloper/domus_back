<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promos = PromoCode::orderByDesc('created_at')->paginate(20);
        return view('admin.promo_codes.index', compact('promos'));
    }

    public function create()
    {
        return view('admin.promo_codes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:promo_codes,code',
            'discount_percent' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
            'max_redemptions' => 'nullable|integer|min:1',
        ]);
        $data['code'] = strtoupper($data['code']);
        $data['active'] = $request->has('active') ? $data['active'] : false;

        PromoCode::create($data);

        return redirect()->route('admin.promo-codes.index')->with('success', 'Código creado correctamente.');
    }

    public function edit(PromoCode $promo_code)
    {
        return view('admin.promo_codes.edit', ['promo' => $promo_code]);
    }

    public function update(Request $request, PromoCode $promo_code)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:promo_codes,code,' . $promo_code->id,
            'discount_percent' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
            'max_redemptions' => 'nullable|integer|min:1',
        ]);
        $data['code'] = strtoupper($data['code']);
        $data['active'] = $request->has('active') ? $data['active'] : false;

        $promo_code->update($data);

        return redirect()->route('admin.promo-codes.index')->with('success', 'Código actualizado.');
    }

    public function destroy(PromoCode $promo_code)
    {
        $promo_code->delete();
        return redirect()->route('admin.promo-codes.index')->with('success', 'Código eliminado.');
    }
}
