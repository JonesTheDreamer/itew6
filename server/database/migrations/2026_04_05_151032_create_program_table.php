<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collegeId')->constrained('college')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('type', 50)->nullable();
            $table->date('dateEstablished')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('program'); }
};
