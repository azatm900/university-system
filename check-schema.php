<?php
$tables = ['attendance','attendances','batches','batch','specializations','specialization','semesters','levels','level','results','subjects','courses','enrollments','registrations','programs'];
foreach ($tables as $t) {
    echo str_pad($t, 20) . ': ' . (\Illuminate\Support\Facades\Schema::hasTable($t) ? 'YES' : 'NO') . PHP_EOL;
}
echo "---- programs columns ----" . PHP_EOL;
echo implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('programs')) . PHP_EOL;
echo "---- semesters columns ----" . PHP_EOL;
echo implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('semesters')) . PHP_EOL;
echo "---- courses columns ----" . PHP_EOL;
echo implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('courses')) . PHP_EOL;
echo "---- registrations columns ----" . PHP_EOL;
echo implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('registrations')) . PHP_EOL;
echo "---- results columns ----" . PHP_EOL;
echo implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('results')) . PHP_EOL;
