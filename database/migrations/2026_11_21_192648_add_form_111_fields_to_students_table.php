<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // 1. معلومات الطالب الأساسية
            $table->string('full_name')->nullable()->after('user_id'); // الاسم الرباعي
            $table->string('occupation')->nullable()->after('gender'); // المهنة
            $table->date('appointment_date')->nullable()->after('occupation'); // تاريخ التعيين

            // 2. العنوان التفصيلي
            $table->string('state')->nullable()->after('address'); // الولاية
            $table->string('district')->nullable()->after('state'); // الحي
            $table->string('block')->nullable()->after('district'); // المربع

            // 3. معلومات الثانوية / الشهادة
            $table->string('certificate_type')->nullable()->after('block'); // نوع الشهادة
            $table->date('certificate_date')->nullable()->after('certificate_type'); // تاريخ الشهادة
            $table->string('certificate_score')->nullable()->after('certificate_date'); // الدرجة / النسبة
            $table->text('academic_notes')->nullable()->after('certificate_score'); // ملاحظات الشؤون التعليمية

            // 4. الرسوم (بوساطة المسجل)
            $table->decimal('registration_fee', 10, 2)->nullable()->after('academic_notes'); // رسوم التسجيل رقماً
            $table->string('registration_fee_words')->nullable()->after('registration_fee'); // رسوم التسجيل كتابياً
            $table->decimal('tuition_fee', 10, 2)->nullable()->after('registration_fee_words'); // الرسوم الدراسية رقماً
            $table->string('tuition_fee_words')->nullable()->after('tuition_fee'); // الرسوم الدراسية كتابياً

            // 5. التحصيلات والاعتماد
            $table->string('registration_receipt_no')->nullable()->after('tuition_fee_words'); // إيصال التسجيل
            $table->string('tuition_receipt_no')->nullable()->after('registration_receipt_no'); // إيصال الرسوم
            $table->string('accountant_name')->nullable()->after('tuition_receipt_no'); // اسم المحاسب
            $table->string('registrar_name')->nullable()->after('accountant_name'); // اسم المسجل
            
            // 6. الصورة الشخصية
            $table->string('photo')->nullable()->after('registrar_name'); // مسار الصورة
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'full_name', 'occupation', 'appointment_date',
                'state', 'district', 'block',
                'certificate_type', 'certificate_date', 'certificate_score', 'academic_notes',
                'registration_fee', 'registration_fee_words', 'tuition_fee', 'tuition_fee_words',
                'registration_receipt_no', 'tuition_receipt_no', 'accountant_name', 'registrar_name',
                'photo'
            ]);
        });
    }
};