<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class CleanRejectedEnrollments extends Command
{
    // El nombre que usaremos para llamar a este comando
    protected $signature = 'enrollments:clean-rejected';
    
    protected $description = 'Elimina inscripciones rechazadas de hace más de 30 días junto con sus documentos PDF adjuntos.';

    public function handle()
    {
        // Buscamos inscripciones rechazadas que lleven 30 días o más en ese estado
        $fechaLimite = Carbon::now()->subDays(30);
        
        $inscripcionesRechazadas = Enrollment::where('status', 'Rechazado')
                                             ->where('updated_at', '<=', $fechaLimite)
                                             ->get();

        $contador = 0;

        foreach ($inscripcionesRechazadas as $inscripcion) {
            // 1. Eliminar PDF de Cédula si existe
            if ($inscripcion->id_document_path && $inscripcion->id_document_path !== 'N/A') {
                $rutaCedula = public_path($inscripcion->id_document_path);
                if (File::exists($rutaCedula)) {
                    File::delete($rutaCedula);
                }
            }

            // 2. Eliminar PDF de Aval si existe
            if ($inscripcion->approval_document_path) {
                $rutaAval = public_path($inscripcion->approval_document_path);
                if (File::exists($rutaAval)) {
                    File::delete($rutaAval);
                }
            }

            // 3. Eliminar el registro definitivo de la base de datos
            $inscripcion->delete();
            $contador++;
        }

        $this->info("Limpieza completada: {$contador} inscripciones eliminadas del sistema.");
    }
}