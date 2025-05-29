<?php

namespace App\Http\Controllers\Api;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    public function index()
    {
        return Language::all();
    }

    public function store(Request $request)
    {
        return Language::create($request->all());
    }

    public function show(Language $language)
    {
        return $language->load('users');
    }

    public function update(Request $request, Language $language)
    {
        $language->update($request->all());
        return $language;
    }

    public function destroy(Language $language)
    {
        $language->delete();
        return response()->noContent();
    }
}
