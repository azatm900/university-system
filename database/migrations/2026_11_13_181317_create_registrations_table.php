<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->restrictOnDelete();
            $table->foreignId('semester_id')
                  ->constrained('semesters')
                  ->restrictOnDelete();
            $table->date('registered_at');
            $table->enum('status', ['registered', 'dropped', 'completed'])
                  ->default('registered');
            $table->timestamps();

            // منع تكرار تسجيل نفس الطالب في نفس المقرر بنفس الفصل
            $table->unique(['student_id', 'course_id', 'semester_id'], 'registrations_unique_idx');

            $table->index('student_id');
            $table->index('course_id');
            $table->index('semester_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};