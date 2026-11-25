@extends('layouts.app')

@section('title', 'كشف النتائج')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📊 كشف النتائج</h4>
        <a href="{{ route('results.results.index') }}" class="btn btn-outline-secondary btn-sm">
            ← عودة لإدارة النتائج
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('results.sheet.show') }}">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">التخصص (البرنامج) <span class="text-danger">*</span></label>
                        <select name="program_id" class="form-select" required>
                            <option value="">— اختر التخصص —</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->code }} — {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">المستوى <span class="text-danger">*</span></label>
                        <select name="level" class="form-select" required>
                            <option value="">—</option>
                            @foreach($levels as $l)
                                <option value="{{ $l }}" {{ request('level') == $l ? 'selected' : '' }}>
                                    المستوى {{ $l }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">الفصل الدراسي <span class="text-danger">*</span></label>
                        <select name="semester_id" class="form-select" required>
                            <option value="">— اختر الفصل —</option>
                            @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->code }} — {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">المادة (اختياري)</label>
                        <select name="course_id" class="form-select">
                            <option value="">— كل المواد —</option>
                            @if(request('program_id'))
                                @php
                                    $courses = \App\Models\Course::where('program_id', request('program_id'))
                                        ->when(request('level'), fn($q) => $q->where('level', request('level')))
                                        ->orderBy('code')->get(['id','code','name']);
                                @endphp
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->code }} — {{ $c->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            🔍 عرض الكشف
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</div>
@endsection