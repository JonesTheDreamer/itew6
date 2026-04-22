<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('studentId', 20)->unique();
            $table->foreignId('programId')->constrained('program');
            $table->integer('yearLevel')->nullable();
            $table->integer('unitsTaken')->nullable();
            $table->integer('unitsLeft')->nullable();
            $table->date('dateEnrolled')->nullable();
            $table->date('dateGraduated')->nullable();
            $table->date('dateDropped')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->enum('status', ['Active', 'Graduated', 'Dropped', 'Suspended'])->default('Active');
            $table->timestamps();
            $table->index('studentId');
            $table->index('programId');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};