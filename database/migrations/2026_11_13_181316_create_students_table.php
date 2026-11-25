<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('program_id')
                  ->constrained('programs')
                  ->restrictOnDelete();
            $table->string('student_number', 30)->unique();   // رقم القيد
            $table->string('national_id', 30)->unique()->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'graduated', 'suspended', 'withdrawn'])
                  ->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('program_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};