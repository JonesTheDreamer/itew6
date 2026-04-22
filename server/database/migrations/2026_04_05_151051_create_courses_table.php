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
            $table->foreignId('curriculumId')->constrained('curriculum')->cascadeOnDelete();
            $table->string('courseCode', 20)->unique();
            $table->string('courseName', 150);
            $table->integer('units')->default(0);
            $table->integer('labUnits')->nullable();
            $table->integer('yearLevel');
            $table->integer('semester');
            $table->enum('courseType', ['lecture', 'lecture_lab'])->default('lecture');
            $table->boolean('isRequired')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('courses'); }
};
