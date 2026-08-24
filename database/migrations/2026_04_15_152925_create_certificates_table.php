<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();


            $table->foreignId('course_id')->constrained()->onDelete('cascade');


            $table->string('verification_code', 5)->unique();


            $table->string('doc_type');
            $table->string('doc_number');
            $table->string('student_name');
            $table->string('file_path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }

};
