<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('date');

            $table->enum('status', ['present', 'absent', 'late', 'excused'])
                ->default('present');

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'date'], 'attendance_unique_per_day');
            $table->index(['course_id', 'semester_id', 'date'], 'attendance_course_semester_date_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};