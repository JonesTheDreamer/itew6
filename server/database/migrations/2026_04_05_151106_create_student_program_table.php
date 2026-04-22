<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentId')->constrained('student')->cascadeOnDelete();
            $table->foreignId('programId')->constrained('program');
            $table->date('dateEnrolled')->nullable();
            $table->date('dateLeft')->nullable();
            $table->timestamps();
            $table->unique(['studentId', 'programId']);
        });
    }

    public function down(): void { Schema::dropIfExists('student_program'); }
};
