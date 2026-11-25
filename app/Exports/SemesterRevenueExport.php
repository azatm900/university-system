<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Semester;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SemesterRevenueExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function __construct(private Semester $semester) {}

    public function collection(): Collection
    {
        return Payment::whereHas('invoice', fn ($q) => $q->where('semester_id', $this->semester->id))
            ->with('invoice.student.user', 'receiver')
            ->orderBy('payment_date')
            ->get()
            ->map(function ($p) {
                $method = $p->method instanceof \BackedEnum ? $p->method->value : (string) ($p->method ?? '-');
                $pd = $p->payment_date;
                $pdStr = $pd ? (is_object($pd) ? $pd->format('Y-m-d') : substr((string) $pd, 0, 10)) : '-';
                return [
                    $pdStr,
                    $p->invoice?->invoice_number ?? '-',
                    $p->invoice?->student?->student_number ?? '-',
                    $p->invoice?->student?->user?->name ?? '-',
                    (float) ($p->amount ?? 0),
                    $method,
                    $p->reference_number ?? '-',
                    $p->receiver?->name ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return ['Payment Date', 'Invoice #', 'Student #', 'Student Name', 'Amount', 'Method', 'Reference', 'Received By'];
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