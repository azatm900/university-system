<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')
                  ->unique()
                  ->constrained('registrations')
                  ->cascadeOnDelete();
            $table->decimal('coursework_score', 5, 2)->nullable();
            $table->decimal('midterm_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2);
            $table->string('grade', 3);              // A, B+, C ...
            $table->decimal('grade_points', 3, 2);   // 0.00 – 4.00
            $table->enum('status', ['passed', 'failed', 'incomplete']);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};