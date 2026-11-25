<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('name', 200);
            $table->foreignId('program_id')
                  ->constrained('programs')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('credit_hours');
            $table->unsignedTinyInteger('level');            // السنة الدراسية 1..6
            $table->unsignedTinyInteger('semester_number');  // 1 أو 2
            $table->enum('course_type', ['compulsory', 'elective'])->default('compulsory');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['program_id', 'code']);
            $table->index('is_active');
            $table->index(['program_id', 'level', 'semester_number'], 'courses_program_level_sem_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};