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

class StudentTranscriptExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function __construct(private Student $student) {}

    public function collection(): Collection
    {
        return $this->student->results()
            ->with('registration.course', 'registration.semester')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                $status = $r->status instanceof \BackedEnum ? $r->status->value : (string) ($r->status ?? '-');
                $pub = $r->published_at;
                $pubStr = $pub ? (is_object($pub) ? $pub->format('Y-m-d') : substr((string) $pub, 0, 10)) : 'Draft';
                return [
                    $r->registration?->semester?->code ?? '-',
                    $r->registration?->course?->code ?? '-',
                    $r->registration?->course?->name ?? '-',
                    (int) ($r->registration?->course?->credit_hours ?? 0),
                    (float) ($r->coursework_score ?? 0),
                    (float) ($r->midterm_score ?? 0),
                    (float) ($r->final_score ?? 0),
                    (float) ($r->total_score ?? 0),
                    (string) ($r->grade ?? '-'),
                    (float) ($r->grade_points ?? 0),
                    $status,
                    $pubStr,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Semester', 'Course Code', 'Course Name', 'Credits',
            'Coursework', 'Midterm', 'Final', 'Total',
            'Grade', 'Grade Points', 'Status', 'Published',
        ];
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