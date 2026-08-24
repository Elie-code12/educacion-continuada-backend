<?php

namespace App\Http\Controllers;

use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PopupController extends Controller
{
    public function index()
    {
        $popups = Popup::latest()->get();
        return response()->json($popups, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url',
        ]);

        $imagePath = $request->file('image')->store('popups', 'public');

        $popup = Popup::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'link' => $request->link,
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'popup creado',
            'popup' => $popup
        ], 201);
    }

    public function toggle(Popup $popup)
    {
        $popup->is_active = !$popup->is_active;
        $popup->save();

        return response()->json([
            'message' => 'estado actualizado',
            'is_active' => $popup->is_active
        ], 200);
    }

    public function destroy(Popup $popup)
    {
        if (Storage::disk('public')->exists($popup->image_path)) {
            Storage::disk('public')->delete($popup->image_path);
        }
        $popup->delete();

        return response()->json(['message' => 'popup eliminado'], 200);
    }
}
