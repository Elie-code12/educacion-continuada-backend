<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

  
    protected $fillable = [
        'student_id',
        'course_id',
        'schedule',
        'id_document_path',
        'approval_document_path',
        'extra_details',
        'status',
    ];

   
    protected $casts = [
        'extra_details' => 'array',
    ];

    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}