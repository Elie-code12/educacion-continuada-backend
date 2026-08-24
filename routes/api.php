<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminEnrollmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeContentController;
use App\Http\Controllers\PopupController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CertificateController;


Route::get('/popups', [PopupController::class, 'index']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/oferta', [CourseController::class, 'oferta']);
Route::get('/courses/categories', [CourseController::class, 'categories']);
Route::get('/courses/{course}', [CourseController::class, 'show']);
Route::post('/enrollments', [EnrollmentController::class, 'store']);
Route::post('/certificates/search', [CertificateController::class, 'searchPublic']);


Route::get('/admin/home-content', [HomeContentController::class, 'index']);
Route::get('/admin/popups', [PopupController::class, 'index']);



Route::middleware('auth:sanctum')->group(function () {


    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);


    Route::get('/admin/enrollments', [AdminEnrollmentController::class, 'index']);
    # export debe ir antes de {id} para que laravel no se confunda
    Route::get('/admin/enrollments/export', [AdminEnrollmentController::class, 'export']);
    Route::get('/admin/enrollments/{id}', [AdminEnrollmentController::class, 'show']);
    Route::put('/admin/enrollments/{id}/status', [AdminEnrollmentController::class, 'updateStatus']);


    Route::get('/admin/certificates', [CertificateController::class, 'index']);
    Route::post('/admin/certificates', [CertificateController::class, 'store']);

    Route::get('/admin/certificates/student/{doc_number}', [CertificateController::class, 'getStudentData']);
    Route::get('/admin/certificates/courses/{category_id}', [CertificateController::class, 'getCourses']);
    Route::get('/admin/certificates/{id}/pdf', [CertificateController::class, 'showPdf']);
    Route::delete('/admin/certificates/{id}', [CertificateController::class, 'destroy']);



    Route::put('/admin/home-content', [HomeContentController::class, 'update']);
    Route::post('/admin/popups', [PopupController::class, 'store']);
    Route::patch('/admin/popups/{popup}/toggle', [PopupController::class, 'toggle']);
    Route::delete('/admin/popups/{popup}', [PopupController::class, 'destroy']);

});
