<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faculty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('position', 100)->nullable();
            $table->date('employmentDate')->nullable();
            $table->string('employmentType', 50)->nullable();
            $table->decimal('monthlyIncome', 10, 2)->nullable();
            $table->string('department', 100)->nullable();
            $table->timestamps();
            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculty');
    }
};