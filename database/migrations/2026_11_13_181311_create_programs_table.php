<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 200);
            $table->string('name_en', 200)->nullable();
            $table->enum('degree_level', ['diploma', 'bachelor', 'master', 'phd']);
            $table->unsignedTinyInteger('duration_years');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('degree_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
}; 