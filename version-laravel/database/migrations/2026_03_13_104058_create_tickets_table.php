<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();            $table->foreignId('assigned_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('open');
            $table->string('priority', 50)->default('medium');
            $table->string('type', 50)->default('non_facturable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};