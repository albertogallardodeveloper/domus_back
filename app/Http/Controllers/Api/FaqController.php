<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return Faq::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:Uploaded,To be uploaded',
        ]);

        $faq = Faq::create($data);
        return response()->json($faq, 201);
    }

    public function show(Faq $faq)
    {
        return $faq;
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'category' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:Uploaded,To be uploaded',
        ]);

        $faq->update($data);
        return response()->json($faq);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return response()->json(null, 204);
    }
}
