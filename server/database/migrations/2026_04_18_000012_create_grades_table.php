<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentId')->constrained('student')->cascadeOnDelete();
            $table->foreignId('sectionId')->constrained('section')->cascadeOnDelete();
            $table->foreignId('courseId')->constrained('courses')->cascadeOnDelete();
            $table->string('academicYear', 20);
            $table->unsignedTinyInteger('semester');
            $table->enum('term', ['preliminary', 'midterm', 'finals']);
            $table->decimal('grade', 3, 2);
            $table->enum('remarks', ['passed', 'failed', 'dropped', 'incomplete'])->nullable();
            $table->timestamps();
            $table->unique(['studentId', 'courseId', 'academicYear', 'semester', 'term'], 'grades_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('grades'); }
};
