<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentId')->constrained('student')->cascadeOnDelete();
            $table->foreignId('sectionId')->constrained('section')->cascadeOnDelete();
            $table->string('academicYear', 20);
            $table->tinyInteger('semester');
            $table->timestamps();
            $table->unique(['studentId', 'sectionId', 'academicYear', 'semester']);
        });
    }

    public function down(): void { Schema::dropIfExists('student_section'); }
};
