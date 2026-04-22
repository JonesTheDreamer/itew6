<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facultyId')->constrained('faculty')->cascadeOnDelete();
            $table->string('position', 100)->nullable();
            $table->date('employmentDate')->nullable();
            $table->date('employmentEndDate')->nullable();
            $table->string('employmentType', 50)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('workLocation', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('job_history'); }
};
