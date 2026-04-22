<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('violation_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('violationId')->constrained('violation')->cascadeOnDelete();
            $table->text('note');
            $table->foreignId('addedBy')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violation_notes');
    }
};
