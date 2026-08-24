<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEnrollmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;

    public function __construct($formData)
    {
        // Esto recibe los datos del formulario
        $this->formData = $formData;
    }

    public function build()
    {
        // Esto envía los datos a la vista del correo
        return $this->subject('Nueva Inscripción - Educación Continuada')
                    ->view('emails.enrollment_notification');
    }
}