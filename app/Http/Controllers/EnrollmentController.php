<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\NewEnrollmentMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    public function create($id)
    {
        $course = Course::findOrFail($id);
        return view('public.enroll', compact('course'));
    }

    public function store(Request $request)
    {
        $course = Course::findOrFail($request->course_id);

        $rules = [
            'course_id' => 'required|exists:courses,id',
            'full_name' => 'required|string|max:255',
            'doc_type' => 'required|string',
            'doc_number' => 'required|string',
            //'birth_place' => 'required|string',
            'birth_date' => 'required|date',
            'age' => 'required|integer',
            'expedition_place' => 'required|string',
            'expedition_date' => 'required|date',
            'gender' => 'required|string',
            'blood_type' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'personal_email' => 'required|email',
            'phone_number' => 'required|string',
            'extra_details' => 'nullable|array',
        ];

        if ($course->category === 'DIPLOMADOS DE OPCION DE GRADO') {
            $rules['approval_document'] = 'required|mimes:pdf|max:5120';
            $rules['id_document'] = 'required|mimes:pdf|max:5120';
        } elseif ($course->category === 'SEMINARIOS DE INGLES') {

        } else {
            $rules['id_document'] = 'required|mimes:pdf|max:5120';

            $rules['schedule'] = 'nullable|string';
        }

        if (isset($request->extra_details['is_ut_student']) && $request->extra_details['is_ut_student'] === 'yes') {
            $rules['institutional_email'] = ['required', 'email', 'regex:/^[a-zA-Z0-9._%+\-]+@ut\.edu\.co$/'];
        } else {
            $rules['institutional_email'] = 'nullable|email';
        }

        $validated = $request->validate($rules);

        try {

            $idPath = 'N/A';
            if ($request->hasFile('id_document')) {
                $idPath = '/storage/' . $request->file('id_document')->store('enrollments/identities', 'public');
            }

            $approvalPath = null;
            if ($request->hasFile('approval_document')) {
                $approvalPath = '/storage/' . $request->file('approval_document')->store('enrollments/approvals', 'public');
            }

            $student = Student::updateOrCreate(
                ['doc_number' => $validated['doc_number']],
                [
                    'doc_type' => $validated['doc_type'],
                    'full_name' => strtoupper($validated['full_name']),
                   // 'birth_place' => $validated['birth_place'],
                    'birth_date' => $validated['birth_date'],
                    'age' => $validated['age'],
                    'expedition_place' => $validated['expedition_place'],
                    'expedition_date' => $validated['expedition_date'],
                    'gender' => $validated['gender'],
                    'blood_type' => $validated['blood_type'],
                    'city' => $validated['city'],
                    'address' => $validated['address'],
                    'personal_email' => strtolower($validated['personal_email']),
                    'institutional_email' => strtolower($validated['institutional_email'] ?? ''),
                    'phone_number' => $validated['phone_number'],
                ]
            );

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $validated['course_id'],
                'schedule' => $validated['schedule'] ?? null,
                'id_document_path' => $idPath,
                'approval_document_path' => $approvalPath,
                'extra_details' => $validated['extra_details'] ?? null,
                'status' => 'Pendiente'
            ]);

            $mailData = array_merge($student->toArray(), $enrollment->toArray());
            Mail::to('allealt@ut.edu.co')->send(new NewEnrollmentMail($mailData));

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => '¡El formulario se ha enviado correctamente!'
                ], 201);
            }

            return redirect()->route('public.oferta')->with('success', '¡El formulario se ha enviado correctamente!');

        } catch (\Exception $e) {

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hubo un problema procesando la inscripción: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Hubo un problema: ' . $e->getMessage())->withInput();
        }
    }
}
