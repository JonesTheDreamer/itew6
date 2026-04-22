<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organizationId')->constrained('organization')->cascadeOnDelete();
            $table->string('role', 100)->nullable();
            $table->date('dateJoined')->nullable();
            $table->date('dateLeft')->nullable();
            $table->timestamps();
            $table->unique(['userId', 'organizationId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_organization');
    }
};