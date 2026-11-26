<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index();
            $table->string('position');
            $table->string('hook', 160)->nullable();
            $table->string('city')->nullable();
            $table->string('division')->nullable();
            $table->text('summary')->nullable();
            $table->string('brochure_path')->nullable();
            $table->string('form_url', 500);
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_openings');
    }
};