<?php

namespace App\Http\Controllers\Api;

use App\Models\UserApp;
use App\Models\Review;
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

    public function reviewsForProfessional(Request $request)
    {
        // ✅ Usamos el profesional autenticado
        $user = $request->user(); // auth:sanctum

        $reviews = Review::with(['user', 'service', 'booking'])
            ->whereHas('service', function ($q) use ($user) {
                $q->where('user_app_id', $user->id); // servicios de este profesional
            })
            ->latest()
            ->get();

        $average = round($reviews->avg('rating'), 1);

        return response()->json([
            'average_rating' => $average,
            'total_reviews' => $reviews->count(),
            'reviews' => $reviews
        ]);
    }

    public function reviewsByClient(Request $request)
    {
        $user = $request->user(); // cliente autenticado

        $reviews = Review::with(['service.professional'])
            ->where('user_app_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'reviews' => $reviews
        ]);
    }

    public function show($id)
    {
        return response()->json(['message' => 'Método show no implementado.'], 200);
    }
}
