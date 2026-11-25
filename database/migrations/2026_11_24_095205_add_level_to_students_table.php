<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')
                ->default(1)
                ->after('program_id')
                ->comment('المستوى الدراسي (1-8)');

            $table->index(['program_id', 'level'], 'students_program_level_index');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_program_level_index');
            $table->dropColumn('level');
        });
    }
};