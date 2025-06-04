<?php

namespace App\Http\Controllers\Api;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index()
    {
        return Review::with(['service', 'user'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'user_app_id' => 'required|exists:users_app,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create($validated);

        return response()->json($review->load('user'), 201);
    }

    public function byService($id)
    {
        return Review::where('service_id', $id)->with('user')->latest()->get();
    }
}