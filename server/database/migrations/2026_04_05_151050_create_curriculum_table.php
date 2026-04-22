<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programId')->constrained('program')->cascadeOnDelete();
            $table->string('name', 150);
            $table->integer('effectiveYear');
            $table->boolean('isActive')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('curriculum'); }
};
