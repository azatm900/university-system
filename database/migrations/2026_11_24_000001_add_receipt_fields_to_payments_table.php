<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'receipt_image')) {
                $table->string('receipt_image')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('payments', 'installment_no')) {
                $table->unsignedInteger('installment_no')->nullable()->after('receipt_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['receipt_image', 'installment_no']);
        });
    }
};