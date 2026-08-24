<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{
    //
    protected $fillable = [
        'about_us', 
        'formation_types', 
        'discounts'
    ];
}