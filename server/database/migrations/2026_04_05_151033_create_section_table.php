<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programId')->constrained('program')->cascadeOnDelete();
            $table->string('sectionName', 50);
            $table->string('academicYear', 20)->nullable();
            $table->integer('yearLevel')->nullable();
            $table->integer('semester')->nullable();
            $table->timestamps();
            $table->unique(['programId', 'sectionName', 'academicYear', 'semester']);
        });
    }

    public function down(): void { Schema::dropIfExists('section'); }
};
