<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();


            $table->string('full_name');
            $table->string('doc_type');
            $table->string('doc_number')->unique();
            $table->string('birth_place');
            $table->date('birth_date');
            $table->integer('age');
            $table->string('expedition_place');
            $table->date('expedition_date');
            $table->string('gender');
            $table->string('blood_type');


            $table->string('city');
            $table->string('address');
            $table->string('personal_email');
            $table->string('institutional_email')->nullable();
            $table->string('phone_number');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
