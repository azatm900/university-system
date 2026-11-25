<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // جهات الاتصال
            $table->string('next_of_kin_name')->nullable()->after('phone');
            $table->string('next_of_kin_phone', 20)->nullable()->after('next_of_kin_name');
            $table->string('mother_name')->nullable()->after('next_of_kin_phone');
            $table->string('mother_phone', 20)->nullable()->after('mother_name');

            // الحالة المالية
            $table->enum('finance_status', ['pending', 'paid', 'partial'])
                  ->default('pending')
                  ->after('status');

            $table->index('finance_status');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['finance_status']);
            $table->dropColumn([
                'next_of_kin_name',
                'next_of_kin_phone',
                'mother_name',
                'mother_phone',
                'finance_status',
            ]);
        });
    }
};