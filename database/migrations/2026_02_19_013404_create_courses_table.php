<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');


            $table->enum('category', [
                'DIPLOMADOS DE OPCION DE GRADO',
                'DIPLOMADOS DE PUBLICO GENERAL',
                'SEMINARIOS',
                'SEMINARIOS DE INGLES',
                'CURSOS'
            ]);

            $table->string('mode')->nullable();
            $table->integer('hours');
            $table->string('duration')->nullable();
            $table->text('justification');
            $table->text('general_objective')->nullable();
            $table->json('specific_objectives')->nullable();
            $table->json('topics')->nullable();
            $table->string('cost')->nullable();


            $table->string('image_url')->nullable();
            $table->string('pdf_document')->nullable();


            $table->json('schedules')->nullable();


            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
