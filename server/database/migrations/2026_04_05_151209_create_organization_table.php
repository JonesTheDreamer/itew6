<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organization', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collegeId')->nullable();
            $table->string('organizationName', 150)->unique();
            $table->text('organizationDescription')->nullable();
            $table->date('dateCreated')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
            $table->foreign('collegeId')->references('id')->on('college')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization');
    }
};