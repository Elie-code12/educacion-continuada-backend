<?php

namespace App\Exports;

use App\Models\Enrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EnrollmentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $category;
    protected $status;

    public function __construct($category, $status = 'Todos')
    {
        $this->category = $category;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Enrollment::with(['course', 'student']);

        if ($this->category !== 'Todas') {
            $query->whereHas('course', function($query) {
                $query->where('category', $this->category);
            });
        }

        if ($this->status !== 'Todos') {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Fecha de Inscripción',
            'Programa Matriculado',
            'Estado',
            'Nombre Completo',
            'Tipo de Documento',
            'Número de Documento',
            'Lugar de Expedición',
            'Fecha de Expedición',
            'Lugar de Nacimiento',
            'Fecha de Nacimiento',
            'Edad',
            'Género',
            'Tipo de Sangre',
            'Correo Personal',
            'Correo Institucional (UT)',
            'Teléfono / Celular',
            'Ciudad de Residencia',
            'Dirección',
            'Horario Seleccionado',
            '¿Es Estudiante UT?',
            'Código Estudiantil',
            'Programa Académico',
            'Semestre',
            '¿Es Egresado(a) UT?',
            '¿Tiene Título Superior?',
            'Pregrado',
            'Especialización',
            'Maestría',
            'Doctorado'
        ];
    }

    public function map($enrollment): array
    {
        $extra = $enrollment->extra_details ?? [];

        $esEstudiante = isset($extra['is_ut_student']) ? (strtolower($extra['is_ut_student']) === 'yes' ? 'Sí' : 'No') : '';
        $esEgresado = isset($extra['is_ut_graduate']) ? (strtolower($extra['is_ut_graduate']) === 'yes' ? 'Sí' : 'No') : '';
        $tieneTitulo = isset($extra['has_degree']) ? (strtolower($extra['has_degree']) === 'yes' ? 'Sí' : 'No') : '';

        $pregrado = '';
        $especializacion = '';
        $maestria = '';
        $doctorado = '';


        if (isset($extra['degrees_info'])) {
            $titulosArray = explode(' | ', $extra['degrees_info']);

            foreach ($titulosArray as $tituloInfo) {
                $tituloInfo = trim($tituloInfo);

                if (Str::startsWith($tituloInfo, 'Pregrado:')) {
                    $pregrado = trim(str_replace('Pregrado:', '', $tituloInfo));
                } elseif (Str::startsWith($tituloInfo, 'Especialización:')) {
                    $especializacion = trim(str_replace('Especialización:', '', $tituloInfo));
                } elseif (Str::startsWith($tituloInfo, 'Maestría:')) {
                    $maestria = trim(str_replace('Maestría:', '', $tituloInfo));
                } elseif (Str::startsWith($tituloInfo, 'Doctorado:')) {
                    $doctorado = trim(str_replace('Doctorado:', '', $tituloInfo));
                }
            }
        } elseif ($tieneTitulo === 'No') {
            $pregrado = 'Ninguno';
            $especializacion = 'Ninguno';
            $maestria = 'Ninguno';
            $doctorado = 'Ninguno';
        }

        return [
            Carbon::parse($enrollment->created_at)->format('d/m/Y H:i'),
            $enrollment->course->title ?? 'Programa Eliminado',
            $enrollment->status,

            $enrollment->student->full_name ?? '',
            $enrollment->student->doc_type ?? '',
            $enrollment->student->doc_number ?? '',
            $enrollment->student->expedition_place ?? '',
            $enrollment->student->expedition_date ?? '',
            $enrollment->student->birth_place ?? '',
            $enrollment->student->birth_date ?? '',
            $enrollment->student->age ?? '',
            $enrollment->student->gender ?? '',
            $enrollment->student->blood_type ?? '',
            $enrollment->student->personal_email ?? '',
            $enrollment->student->institutional_email ?? '',
            $enrollment->student->phone_number ?? '',
            $enrollment->student->city ?? '',
            $enrollment->student->address ?? '',

            $enrollment->schedule ?? '',

            $esEstudiante,
            $extra['student_code'] ?? '',
            $extra['academic_program'] ?? '',
            $extra['semester'] ?? '',
            $esEgresado,
            $tieneTitulo,
            $pregrado,
            $especializacion,
            $maestria,
            $doctorado
        ];
    }
}
