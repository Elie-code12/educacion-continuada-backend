<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function searchForm()
    {
        return view('public.certificates.search');
    }

    public function find(Request $request)
    {
        $request->validate([
            'doc_type' => 'required',
            'doc_number' => 'required',
            'expedition_date' => 'required|date'
        ]);

        $certificates = Certificate::where('doc_type', $request->doc_type)
                                   ->where('doc_number', $request->doc_number)
                                   ->where('expedition_date', $request->expedition_date)
                                   ->with('course')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return view('public.certificates.search', compact('certificates'));
    }

    public function download($verification_code)
    {
        $certificate = Certificate::where('verification_code', $verification_code)->firstOrFail();

        if (!Storage::disk('local')->exists($certificate->file_path)) {
            abort(404, 'El archivo del certificado no se encuentra disponible.');
        }

        return Storage::disk('local')->download($certificate->file_path, 'Certificado_UT_' . $certificate->verification_code . '.pdf');
    }

    public function index()
    {
        $certificates = Certificate::with('course')->latest()->get();
        return response()->json($certificates, 200);
    }

    public function getStudentData($doc_number)
    {
        $enrollments = \App\Models\Enrollment::whereHas('student', function ($query) use ($doc_number) {
            $query->where('doc_number', $doc_number);
        })
        ->where('status', 'Aprobado')
        ->with(['course', 'student'])
        ->get();

        if ($enrollments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No se encontraron inscripciones aprobadas para este documento.'], 404);
        }

        $firstEnrollment = $enrollments->first();
        $student = $firstEnrollment->student;

        $courses = $enrollments->map(function($enrollment) use ($doc_number) {

            $hasCertificate = \App\Models\Certificate::where('doc_number', $doc_number)
                                                     ->where('course_id', $enrollment->course->id)
                                                     ->exists();
            return [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title ?? 'Curso eliminado',
                'has_certificate' => $hasCertificate
            ];
        });

        return response()->json([
            'success' => true,
            'doc_type' => $student->doc_type,
            'student_name' => $student->full_name,
            'expedition_date' => $student->expedition_date,
            'courses' => $courses
        ], 200);
    }

    public function getCourses($category_id)
    {
        $courses = Course::where('category_id', $category_id)->get();
        return response()->json($courses, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'doc_type' => 'required|string',
            'doc_number' => 'required|string',
            'student_name' => 'required|string',
            'expedition_date' => 'required|date',
            'pdf_file' => 'required|mimes:pdf|max:5120',
        ]);

        $path = $request->file('pdf_file')->store('certificates', 'local');

        $code = strtoupper(Str::random(5));

        while (Certificate::where('verification_code', $code)->exists()) {
            $code = strtoupper(Str::random(5));
        }

        $certificate = Certificate::create([
            'verification_code' => $code,
            'course_id' => $request->course_id,
            'doc_type' => $request->doc_type,
            'doc_number' => $request->doc_number,
            'student_name' => strtoupper($request->student_name),
            'expedition_date' => $request->expedition_date,
            'file_path' => $path,
        ]);

        return response()->json([
            'message' => 'Certificado subido y vinculado correctamente.',
            'certificate' => $certificate
        ], 201);
    }

    public function showPdf($id)
    {
        $certificate = Certificate::findOrFail($id);

        if (!Storage::disk('local')->exists($certificate->file_path)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }

        return Storage::disk('local')->response($certificate->file_path);
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        if (Storage::disk('local')->exists($certificate->file_path)) {
            Storage::disk('local')->delete($certificate->file_path);
        }

        $certificate->delete();

        return response()->json(['message' => 'Certificado y archivo eliminados correctamente.'], 200);
    }

    public function searchPublic(Request $request)
    {
        $request->validate([
            'doc_type' => 'required|string',
            'doc_number' => 'required|string',
            'expedition_date' => 'required|date'
        ]);

        $certificates = Certificate::where('doc_type', $request->doc_type)
                                   ->where('doc_number', $request->doc_number)
                                   ->where('expedition_date', $request->expedition_date)
                                   ->with('course')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return response()->json($certificates, 200);
    }
}
