<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'mode',
        'hours',
        'duration',
        'justification',
        'general_objective',
        'specific_objectives',
        'topics',
        'cost',
        'image_url',
        'pdf_document',
        'schedules',
    ];


    protected $casts = [
        'specific_objectives' => 'array',
        'topics' => 'array',
        'schedules' => 'array',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
