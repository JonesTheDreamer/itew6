<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('college', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('dean', 100)->nullable();
            $table->date('dateEstablished')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('college'); }
};
