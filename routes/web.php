<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeContentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\PopupController;
use App\Http\Controllers\AdminDashboardController; // NUEVO

// --- RUTA PÚBLICA ---
Route::get('/', [CourseController::class, 'oferta'])->name('public.oferta');
Route::get('/oferta', [CourseController::class, 'index'])->name('public.oferta');
Route::get('/curso/{id}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/inscribirse/{id}', [EnrollmentController::class, 'create'])->name('enrollments.create');
Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');

// --- RUTAS AUTENTICACIÓN ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// --- RUTAS ADMINISTRATIVAS ---

// 1. DASHBOARD PRINCIPAL (Nueva ruta raíz del admin)
Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

// 2. CURSOS
Route::get('/admin/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/admin/courses/crear', [CourseController::class, 'create'])->name('courses.create');
Route::post('/admin/courses', [CourseController::class, 'store'])->name('courses.store');
Route::get('/admin/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
Route::put('/admin/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
Route::delete('/admin/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');

// 3. INSCRIPCIONES
Route::get('/admin/inscripciones', [App\Http\Controllers\AdminEnrollmentController::class, 'index'])->name('admin.enrollments.index');
Route::get('/admin/inscripciones/exportar', [App\Http\Controllers\AdminEnrollmentController::class, 'export'])->name('admin.enrollments.export');
Route::get('/admin/inscripciones/{id}', [App\Http\Controllers\AdminEnrollmentController::class, 'show'])->name('admin.enrollments.show');
Route::patch('/admin/inscripciones/{id}/estado/{status}', [App\Http\Controllers\AdminEnrollmentController::class, 'updateStatus'])->name('admin.enrollments.updateStatus');

// 4. PUBLICIDAD Y CONTENIDO DE INICIO (El "Hub" se elimina, ahora todo se controla desde el Dashboard)
Route::get('/admin/publicidad', [HomeContentController::class, 'index'])->name('admin.home_content.index');
Route::get('/admin/publicidad/textos', [HomeContentController::class, 'edit'])->name('home.content.edit');
Route::put('/admin/publicidad/textos', [HomeContentController::class, 'update'])->name('home.content.update');
Route::get('/admin/publicidad/ofertas', [PopupController::class, 'index'])->name('admin.popups.index');
Route::post('/admin/publicidad/ofertas', [PopupController::class, 'store'])->name('admin.popups.store');
Route::patch('/admin/publicidad/ofertas/{popup}/toggle', [PopupController::class, 'toggle'])->name('admin.popups.toggle');
Route::delete('/admin/publicidad/ofertas/{popup}', [PopupController::class, 'destroy'])->name('admin.popups.destroy');

// --- RUTAS PÚBLICAS DE CERTIFICADOS ---
Route::get('/certificados', [CertificateController::class, 'searchForm'])->name('certificates.search');
Route::post('/certificados/buscar', [CertificateController::class, 'find'])->name('certificates.find');
Route::get('/certificados/descargar/{verification_code}', [CertificateController::class, 'download'])->name('certificates.download');

// --- RUTAS ADMINISTRATIVAS DE CERTIFICADOS ---
Route::get('/admin/certificados', [CertificateController::class, 'index'])->name('admin.certificates.index');
Route::get('/admin/certificados/crear', [CertificateController::class, 'create'])->name('admin.certificates.create');
Route::post('/admin/certificados', [CertificateController::class, 'store'])->name('admin.certificates.store');
Route::delete('/admin/certificados/{id}', [CertificateController::class, 'destroy'])->name('admin.certificates.destroy');
Route::get('/admin/api/estudiante-aprobado/{doc_number}', [CertificateController::class, 'getStudentData']);
Route::get('/admin/certificados/ver/{id}', [CertificateController::class, 'showPdf'])->name('admin.certificates.showPdf');