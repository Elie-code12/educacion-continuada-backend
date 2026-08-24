<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\HomeContent;

class CourseController extends Controller
{

    private function getCategoriasFijas()
    {
        return [
            'DIPLOMADOS DE OPCION DE GRADO',
            'DIPLOMADOS DE PUBLICO GENERAL',
            'SEMINARIOS',
            'DIPLOMADOS DE INGLES',
            'CURSOS'
        ];
    }


    public function categories()
    {
        return response()->json($this->getCategoriasFijas());
    }


    public function index(Request $request)
    {
        $courses = Course::latest()->get();
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'mode' => 'required|string',
            'hours' => 'required|integer',
            'duration' => 'nullable|string',
            'cost' => 'nullable|string',
            'justification' => 'required|string',
            'general_objective' => 'nullable|string',
            'specific_objectives' => 'nullable|string',
            'topics' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'pdf_document' => 'nullable|mimes:pdf|max:5120',
            'schedules' => 'nullable|array',
            'schedules.*' => 'nullable|string'
        ]);

        if ($request->has('specific_objectives')) {
            $validated['specific_objectives'] = array_filter(explode("\n", str_replace("\r", "", $request->specific_objectives)));
        }

        if ($request->has('topics')) {
            $validated['topics'] = array_filter(explode("\n", str_replace("\r", "", $request->topics)));
        }

        if ($request->has('schedules')) {
            $horariosLimpios = array_filter($request->schedules, function($value) {
                return !is_null($value) && trim($value) !== '';
            });
            $validated['schedules'] = !empty($horariosLimpios) ? array_values($horariosLimpios) : null;
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/images', 'public');
            $validated['image_url'] = '/storage/' . $imagePath;
        } else {
            $validated['image_url'] = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=800';
        }

        if ($request->hasFile('pdf_document')) {
            $pdfPath = $request->file('pdf_document')->store('courses/pdfs', 'public');
            $validated['pdf_document'] = '/storage/' . $pdfPath;
        }

        $course = Course::create($validated);


        return response()->json([
            'message' => 'Curso publicado exitosamente.',
            'data' => $course
        ], 201);
    }


    public function show($id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course);
    }


    public function oferta(Request $request)
    {
        $homeContent = HomeContent::firstOrCreate(['id' => 1]);
        $query = Course::query();

        if ($request->has('category') && $request->category != null) {
            $query->where('category', $request->category);
        }

        $courses = $query->get();

        return response()->json([
            'homeContent' => $homeContent,
            'courses' => $courses
        ]);
    }


    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'mode' => 'required|string',
            'hours' => 'required|integer',
            'duration' => 'nullable|string',
            'cost' => 'nullable|string',
            'justification' => 'required|string',
            'general_objective' => 'nullable|string',
            'specific_objectives' => 'nullable|string',
            'topics' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pdf_document' => 'nullable|mimes:pdf|max:5120',
            'schedules' => 'nullable|array',
            'schedules.*' => 'nullable|string'
        ]);

        if ($request->has('specific_objectives')) {
            $validated['specific_objectives'] = array_filter(explode("\n", str_replace("\r", "", $request->specific_objectives)));
        }

        if ($request->has('topics')) {
            $validated['topics'] = array_filter(explode("\n", str_replace("\r", "", $request->topics)));
        }

        if ($request->has('schedules')) {
            $horariosLimpios = array_filter($request->schedules, function($value) {
                return !is_null($value) && trim($value) !== '';
            });
            $validated['schedules'] = !empty($horariosLimpios) ? array_values($horariosLimpios) : null;
        } else {
            $validated['schedules'] = null;
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/images', 'public');
            $validated['image_url'] = '/storage/' . $imagePath;
        }

        if ($request->hasFile('pdf_document')) {
            $pdfPath = $request->file('pdf_document')->store('courses/pdfs', 'public');
            $validated['pdf_document'] = '/storage/' . $pdfPath;
        }

        $course->update($validated);

        return response()->json([
            'message' => 'Curso actualizado exitosamente.',
            'data' => $course
        ]);
    }


    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'message' => 'Curso eliminado correctamente.'
        ]);
    }
}
