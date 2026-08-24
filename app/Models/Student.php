<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;


    protected $fillable = [
        'full_name',
        'doc_type',
        'doc_number',
        'birth_place',
        'birth_date',
        'age',
        'expedition_place',
        'expedition_date',
        'gender',
        'blood_type',
        'city',
        'address',
        'personal_email',
        'institutional_email',
        'phone_number',
    ];


    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }


    public function approvedEnrollments()
    {
        return $this->hasMany(Enrollment::class)->where('status', 'Aprobado');
    }
}
