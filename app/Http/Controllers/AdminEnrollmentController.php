<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EnrollmentsExport;
use Illuminate\Support\Str;

class AdminEnrollmentController extends Controller
{
    public function index(Request $request)
    {

        Enrollment::where('status', 'Rechazado')
            ->where('updated_at', '<=', now()->subDays(15))
            ->delete();

        $categories = Course::whereHas('enrollments')->distinct()->pluck('category');
        $selectedCategory = $request->query('category');

        $query = Enrollment::with(['course', 'student'])->latest();

        if ($selectedCategory && $selectedCategory !== 'Todas') {
            $query->whereHas('course', function($q) use ($selectedCategory) {
                $q->where('category', $selectedCategory);
            });
        }

        $enrollments = $query->get();

        return response()->json([
            'categories' => $categories,
            'enrollments' => $enrollments
        ], 200);
    }

    public function show($id)
    {
        $enrollment = Enrollment::with(['course', 'student'])->findOrFail($id);
        return response()->json($enrollment, 200);
    }

    public function export(Request $request)
    {
        $category = $request->query('category', 'Todas');
        $status = $request->query('status', 'Todos');

        $statusSlug = Str::slug($status);
        $categorySlug = Str::slug($category);
        $fileName = "inscripciones-{$statusSlug}-{$categorySlug}.xlsx";

        return Excel::download(new EnrollmentsExport($category, $status), $fileName);
    }

    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status');

        if (!in_array($status, ['Aprobado', 'Rechazado', 'Pendiente'])) {
            return response()->json(['error' => 'estado no valido'], 400);
        }

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = $status;
        $enrollment->save();


        if ($status === 'Aprobado') {
            Enrollment::where('student_id', $enrollment->student_id)
                ->where('course_id', $enrollment->course_id)
                ->where('id', '!=', $enrollment->id)
                ->where('status', 'Pendiente')
                ->update(['status' => 'Rechazado']);
        }

        return response()->json([
            'message' => 'estado actualizado',
            'status' => $enrollment->status
        ], 200);
    }
}
