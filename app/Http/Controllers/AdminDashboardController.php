<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class AdminDashboardController extends Controller
{
    public function index()
    {
        
        $totalCourses = Course::count();
        
        
        return view('admin.dashboard', compact('totalCourses'));
    }
}