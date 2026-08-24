<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'verification_code',
        'doc_type',
        'doc_number',
        'student_name',
        'expedition_date',
        'file_path',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {

            do {
                $code = strtoupper(Str::random(5));
            } while (self::where('verification_code', $code)->exists());

            $certificate->verification_code = $code;
        });
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
