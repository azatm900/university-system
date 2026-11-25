<?php

namespace App\Exports;

use App\Models\Semester;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SemesterRegistrationsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function __construct(private Semester $semester) {}

    public function collection(): Collection
    {
        return $this->semester->registrations()
            ->with('student.user', 'student.program', 'course')
            ->orderBy('registered_at')
            ->get()
            ->map(function ($r) {
                $status = $r->status instanceof \BackedEnum ? $r->status->value : (string) ($r->status ?? '-');
                $reg = $r->registered_at;
                $regStr = $reg ? (is_object($reg) ? $reg->format('Y-m-d') : substr((string) $reg, 0, 10)) : '-';
                return [
                    $r->student?->student_number ?? '-',
                    $r->student?->user?->name ?? '-',
                    $r->student?->program?->name ?? '-',
                    $r->course?->code ?? '-',
                    $r->course?->name ?? '-',
                    (int) ($r->course?->credit_hours ?? 0),
                    $regStr,
                    $status,
                ];
            });
    }

    public function headings(): array
    {
        return ['Student #', 'Student Name', 'Program', 'Course Code', 'Course Name', 'Credits', 'Registered', 'Status'];
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