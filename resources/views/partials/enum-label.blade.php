@php
$labels = [
    'diploma' => 'دبلوم', 'bachelor' => 'بكالوريوس', 'master' => 'ماجستير', 'phd' => 'دكتوراه',
    'compulsory' => 'إجباري', 'elective' => 'اختياري',
    'male' => 'ذكر', 'female' => 'أنثى',
    'active' => 'نشط', 'graduated' => 'متخرج', 'suspended' => 'موقوف', 'withdrawn' => 'منسحب',
    'registered' => 'مسجل', 'dropped' => 'منسحب', 'completed' => 'مكتمل',
    'unpaid' => 'غير مدفوع', 'partial' => 'جزئي', 'paid' => 'مدفوع', 'cancelled' => 'ملغى',
    'cash' => 'نقدي', 'bank_transfer' => 'تحويل', 'card' => 'بطاقة', 'check' => 'شيك',
    'admin' => 'مدير', 'registrar' => 'مسجل', 'finance' => 'مالية', 'results' => 'نتائج', 'student' => 'طالب',
];
$val = $value instanceof \BackedEnum ? $value->value : $value;
@endphp
{{ $labels[$val] ?? $val }}