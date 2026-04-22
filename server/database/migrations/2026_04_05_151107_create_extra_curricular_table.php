<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_curricular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentId')->constrained('student')->cascadeOnDelete();
            $table->string('activity', 150)->nullable();
            $table->string('role', 100)->nullable();
            $table->string('organization', 150)->nullable();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('extra_curricular'); }
};
