<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('violation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('studentId')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->date('violationDate');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violation');
    }
};