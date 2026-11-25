<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeneralStatisticsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function __construct(private array $stats) {}

    public function collection(): Collection
    {
        $labels = [
            'students_total'      => 'Total Students',
            'students_active'     => 'Active Students',
            'programs_total'      => 'Total Programs',
            'programs_active'     => 'Active Programs',
            'courses_total'       => 'Total Courses',
            'courses_active'      => 'Active Courses',
            'semesters_total'     => 'Total Semesters',
            'semesters_active'    => 'Active Semesters',
            'registrations_total' => 'Total Registrations',
            'results_total'       => 'Total Results',
            'results_published'   => 'Published Results',
            'invoices_total'      => 'Total Invoices',
            'invoices_unpaid'     => 'Unpaid Invoices',
            'payments_total'      => 'Total Payments',
            'payments_sum'        => 'Total Payments Amount (cents)',
            'invoices_billed_sum' => 'Total Invoices Billed (cents)',
            'invoices_paid_sum'   => 'Total Invoices Paid (cents)',
        ];
        return collect($this->stats)->map(fn ($v, $k) => [$labels[$k] ?? $k, is_numeric($v) ? (float) $v : (string) $v]);
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}