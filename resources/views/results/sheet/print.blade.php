@extends('layouts.app')

@section('title', __('results_ui.sheet_print_title'))

@section('content')
<style>
    .grade-sheet-page {
        direction: rtl;
        color: #1f2937;
    }

    .grade-sheet-paper {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, .06);
    }

    .grade-sheet-header {
        text-align: center;
        padding: 8px 0 20px;
        margin-bottom: 20px;
        border-bottom: 3px double #1e3a8a;
    }

    .grade-sheet-header h1 {
        margin: 0;
        color: #1e3a8a;
        font-size: 25px;
        font-weight: 800;
    }

    .grade-sheet-header h2 {
        margin: 8px 0 0;
        color: #374151;
        font-size: 19px;
        font-weight: 700;
    }

    .grade-sheet-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px 24px;
        margin-top: 16px;
        font-size: 13px;
    }

    .grade-sheet-meta-item {
        padding: 7px 12px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }

    .grade-sheet-meta-item strong {
        color: #111827;
        margin-inline-start: 4px;
    }

    .grade-sheet-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .grade-sheet-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 700px;
    }

    .grade-sheet-table th,
    .grade-sheet-table td {
        border: 1px solid #64748b;
        padding: 10px 8px;
        vertical-align: middle;
    }

    .grade-sheet-table thead th {
        background: #eff3f9;
        color: #1e293b;
        text-align: center;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }

    .grade-sheet-table tbody td {
        text-align: center;
        font-size: 13px;
    }

    .grade-sheet-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .grade-sheet-table .student-name-cell {
        text-align: right;
        min-width: 190px;
        font-weight: 700;
    }

    .student-number-label {
        display: block;
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
        font-weight: 500;
    }

    .grade-sheet-table .signature-cell {
        min-width: 90px;
        height: 65px;
    }

    .grade-sheet-table .score-cell {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .grade-sheet-table .final-score-cell {
        font-weight: 800;
        color: #1e3a8a;
    }

    .grade-sheet-empty {
        padding: 30px !important;
        color: #64748b;
        text-align: center !important;
    }

    .grade-sheet-signatures {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
        margin-top: 55px;
        padding-top: 10px;
    }

    .grade-sheet-signature {
        min-height: 75px;
        border-top: 1px solid #334155;
        padding-top: 12px;
        text-align: center;
        font-size: 13px;
        font-weight: 700;
    }

    .grade-sheet-footer {
        margin-top: 28px;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        color: #64748b;
        font-size: 11px;
    }

    @media (max-width: 640px) {
        .grade-sheet-paper {
            padding: 14px;
            border-radius: 10px;
        }

        .grade-sheet-header h1 {
            font-size: 20px;
        }

        .grade-sheet-header h2 {
            font-size: 16px;
        }

        .grade-sheet-signatures {
            grid-template-columns: 1fr;
            gap: 28px;
        }
    }

    @page {
        size: A4 landscape;
        margin: 12mm;
    }

    @media print {
        html, body {
            background: #fff !important;
            color: #000 !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print,
        header,
        aside,
        nav,
        footer {
            display: none !important;
        }

        main,
        .grade-sheet-page,
        .grade-sheet-paper {
            display: block !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .grade-sheet-header {
            margin-bottom: 12px;
            padding-bottom: 12px;
        }

        .grade-sheet-header h1 {
            font-size: 20px;
        }

        .grade-sheet-header h2 {
            font-size: 16px;
        }

        .grade-sheet-meta {
            gap: 6px 12px;
            margin-top: 10px;
            font-size: 11px;
        }

        .grade-sheet-meta-item {
            padding: 4px 8px;
            background: #fff !important;
        }

        .grade-sheet-table-wrap {
            overflow: visible !important;
        }

        .grade-sheet-table {
            min-width: 0 !important;
            width: 100% !important;
        }

        .grade-sheet-table th,
        .grade-sheet-table td {
            padding: 6px 5px;
            font-size: 10px !important;
            border-color: #333 !important;
        }

        .grade-sheet-table thead {
            display: table-header-group;
        }

        .grade-sheet-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .grade-sheet-table tbody tr:nth-child(even) {
            background: #fff !important;
        }

        .grade-sheet-signatures {
            margin-top: 35px;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .grade-sheet-signature {
            font-size: 11px;
        }

        .grade-sheet-footer {
            margin-top: 18px;
        }
    }
</style>

<div class="grade-sheet-page space-y-5">

    <div class="no-print flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a
            href="{{ route('results.sheet.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
        >
            <x-icon name="arrow-right" class="h-4 w-4" />
            {{ __('results_ui.sheet_new_filter') }}
        </a>

        <button
            type="button"
            onclick="window.print()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700"
        >
            <x-icon name="printer" class="h-4 w-4" />
            {{ __('results_ui.sheet_print') }}
        </button>
    </div>

    <div class="grade-sheet-paper">
        <div class="grade-sheet-header">
            <h1>{{ __('results_ui.sheet_university') }}</h1>
            <h2>{{ __('results_ui.sheet_print_title') }}</h2>

            <div class="grade-sheet-meta">
                <div class="grade-sheet-meta-item">
                    {{ __('results_ui.sheet_specialization') }}:
                    <strong>{{ $program->name }}</strong>
                </div>

                <div class="grade-sheet-meta-item">
                    {{ __('results_ui.sheet_level_label') }}:
                    <strong>{{ $level }}</strong>
                </div>

                <div class="grade-sheet-meta-item">
                    {{ __('results_ui.sheet_semester') }}:
                    <strong>{{ $semester->name }} ({{ $semester->code }})</strong>
                </div>

                @if($course)
                    <div class="grade-sheet-meta-item">
                        {{ __('results_ui.sheet_course') }}:
                        <strong>{{ $course->code }} — {{ $course->name }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <div class="grade-sheet-table-wrap">
            <table class="grade-sheet-table">
                <thead>
                    <tr>
                        <th style="width: 45px">{{ __('results_ui.sheet_serial') }}</th>
                        <th>{{ __('results_ui.sheet_student_name') }}</th>
                        <th style="width: 100px">{{ __('results_ui.sheet_signature') }}</th>
                        <th style="width: 125px">{{ __('results_ui.sheet_coursework') }}</th>
                        <th style="width: 125px">{{ __('results_ui.sheet_final_exam') }}</th>
                        <th style="width: 125px">{{ __('results_ui.sheet_final_score') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $i => $group)
                        @php
                            $st = $group['student'];
                        @endphp

                        @foreach($group['rows'] as $j => $reg)
                            @php
                                $r = $reg->result;
                                $coursework = $r
                                    ? ((float) ($r->coursework_score ?? 0) + (float) ($r->midterm_score ?? 0))
                                    : null;
                                $final = $r?->final_score;
                                $total = $r?->total_score;
                            @endphp

                            <tr>
                                @if($j === 0)
                                    <td rowspan="{{ $group['rows']->count() }}">
                                        {{ $i + 1 }}
                                    </td>

                                    <td rowspan="{{ $group['rows']->count() }}" class="student-name-cell">
                                        {{ $st->display_name }}
                                        <span class="student-number-label">
                                            {{ __('results_ui.student_number') }}: {{ $st->student_number }}
                                        </span>
                                    </td>

                                    <td rowspan="{{ $group['rows']->count() }}" class="signature-cell"></td>
                                @endif

                                <td class="score-cell">
                                    <span class="student-number-label">{{ $reg->course?->code }}</span>
                                    {{ $coursework !== null ? number_format($coursework, 2) : '—' }}
                                </td>

                                <td class="score-cell">
                                    {{ $final !== null ? number_format((float) $final, 2) : '—' }}
                                </td>

                                <td class="score-cell final-score-cell">
                                    {{ $total !== null ? number_format((float) $total, 2) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="grade-sheet-empty">
                                {{ __('results_ui.sheet_no_students') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grade-sheet-signatures">
            <div class="grade-sheet-signature">
                {{ __('results_ui.sheet_results_officer') }}
            </div>

            <div class="grade-sheet-signature">
                {{ __('results_ui.sheet_committee') }}
            </div>

            <div class="grade-sheet-signature">
                {{ __('results_ui.sheet_final_decision') }}
            </div>
        </div>

        <div class="grade-sheet-footer">
            {{ __('results_ui.sheet_university') }}
        </div>
    </div>
</div>
@endsection