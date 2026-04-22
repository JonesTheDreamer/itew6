<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sectionId')->constrained('section')->cascadeOnDelete();
            $table->unsignedBigInteger('courseId')->nullable();
            $table->string('courseName', 150)->nullable();
            $table->time('timeStart')->nullable();
            $table->time('timeEnd')->nullable();
            $table->string('room', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('schedule'); }
};
