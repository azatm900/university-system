<?php
use App\Models\Student;
use App\Models\Semester;
use App\Models\Registration;

echo "=== توزيع الطلاب (program_id, level) ===" . PHP_EOL;
foreach (Student::selectRaw('program_id, level, count(*) as c')->groupBy('program_id','level')->get() as $r) {
    echo "program={$r->program_id} level={$r->level} count={$r->c}" . PHP_EOL;
}

echo PHP_EOL . "=== الفصول المتاحة ===" . PHP_EOL;
foreach (Semester::orderByDesc('start_date')->limit(5)->get() as $s) {
    echo "{$s->id}: {$s->code} - {$s->name}" . PHP_EOL;
}

echo PHP_EOL . "=== registrations لكل فصل ===" . PHP_EOL;
foreach (Registration::selectRaw('semester_id, count(*) as c')->groupBy('semester_id')->get() as $r) {
    echo "semester={$r->semester_id} count={$r->c}" . PHP_EOL;
}