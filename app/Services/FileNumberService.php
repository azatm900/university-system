<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FileNumberService
{
    public function __construct(protected SettingsService $settings) {}

    public function nextStudentNumber(Program $program, ?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        return $this->generate(
            type:   'student',
            tokens: ['YEAR' => $year, 'PROGRAM_CODE' => $this->codeFor($program->code ?? $program->name)],
            model:  Student::class,
            column: 'student_number',
        );
    }

    public function nextInvoiceNumber(Semester $semester, ?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        return $this->generate(
            type:   'invoice',
            tokens: ['YEAR' => $year, 'SEMESTER_CODE' => $this->codeFor($semester->code ?? $semester->name)],
            model:  Invoice::class,
            column: 'invoice_number',
        );
    }

    public function nextReceiptNumber(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        return $this->generate(
            type:   'receipt',
            tokens: ['YEAR' => $year],
            model:  Payment::class,
            column: 'receipt_number',
        );
    }

    public function generate(string $type, array $tokens, string $model, string $column): string
    {
        $format = $this->settings->get("file_number.{$type}_format");
        if (! $format) {
            throw new RuntimeException("No format configured for [file_number.{$type}_format].");
        }

        $template = $format;
        foreach ($tokens as $name => $value) {
            $template = str_replace('{' . $name . '}', (string) $value, $template);
        }

        if (! preg_match('/\{SEQ(?::(\d+))?\}/', $template, $m)) {
            return $template;
        }

        $pad      = (int) ($m[1] ?? 4);
        $seqToken = $m[0];
        $seqPos   = strpos($template, $seqToken);
        if ($seqPos === false) return $template;

        $prefix = substr($template, 0, $seqPos);
        $suffix = substr($template, $seqPos + strlen($seqToken));

        return DB::transaction(function () use ($model, $column, $prefix, $suffix, $pad) {
            $last = $model::query()
                ->where($column, 'like', $prefix . '%' . $suffix)
                ->orderByDesc($column)
                ->lockForUpdate()
                ->value($column);

            $next = 1;
            if ($last) {
                $middle = substr($last, strlen($prefix));
                if ($suffix !== '') $middle = substr($middle, 0, -strlen($suffix));
                $next = ((int) $middle) + 1;
            }

            return $prefix . str_pad((string) $next, $pad, '0', STR_PAD_LEFT) . $suffix;
        });
    }

    protected function codeFor(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') return 'GEN';
        $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $value), 0, 4));
        return $code !== '' ? $code : 'GEN';
    }
}