<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeContent;

class HomeContentController extends Controller
{
    public function index()
    {
        $content = HomeContent::firstOrCreate(['id' => 1]);
        return response()->json($content, 200);
    }

    public function update(Request $request)
    {
        $content = HomeContent::first();

        $validated = $request->validate([
            'about_us' => 'nullable|string',
            'formation_types' => 'nullable|string',
            'discounts' => 'nullable|string',
        ]);

        $content->update($validated);

        return response()->json([
            'message' => 'textos actualizados',
            'content' => $content
        ], 200);
    }
}
