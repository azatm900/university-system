<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentFinancialExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function __construct(private Student $student) {}

    public function collection(): Collection
    {
        return $this->student->invoices()
            ->with('semester')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($i) {
                $status = $i->status instanceof \BackedEnum ? $i->status->value : (string) ($i->status ?? '-');
                $due = $i->due_date;
                $dueStr = $due ? (is_object($due) ? $due->format('Y-m-d') : substr((string) $due, 0, 10)) : '-';
                $totalCents = (int) round(((float) ($i->total ?? 0)) * 100);
                $paidCents  = (int) round(((float) ($i->paid_amount ?? 0)) * 100);
                return [
                    (string) ($i->invoice_number ?? '-'),
                    (string) ($i->description ?? '-'),
                    $i->semester?->code ?? '-',
                    (float) ($i->amount ?? 0),
                    (float) ($i->discount ?? 0),
                    (float) ($i->total ?? 0),
                    (float) ($i->paid_amount ?? 0),
                    ($totalCents - $paidCents) / 100,
                    $status,
                    $dueStr,
                ];
            });
    }

    public function headings(): array
    {
        return ['Invoice #', 'Description', 'Semester', 'Amount', 'Discount', 'Total', 'Paid', 'Balance', 'Status', 'Due Date'];
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