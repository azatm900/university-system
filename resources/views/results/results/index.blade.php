@extends('layouts.app')
@section('title', 'النتائج')
@section('content')
<div class="card overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
        <a href="{{ route('results.results.create') }}" class="btn-primary">+ نتيجة جديدة</a>
    </div>
    <table class="w-full table-rh">
        <thead><tr><th>#</th><th>الطالب</th><th>المقرر</th><th>الفصل</th><th>المجموع</th><th>التقدير</th><th>الحالة</th><th>النشر</th><th>إجراءات</th></tr></thead>
        <tbody>
        @forelse (($results ?? \App\Models\Result::with(['registration.student.user','registration.course','registration.semester'])->latest()->paginate(20)) as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->registration?->student?->user?->name ?? '—' }}</td>
                <td>{{ $r->registration?->course?->name ?? '—' }}</td>
                <td>{{ $r->registration?->semester?->name ?? '—' }}</td>
                <td>{{ $r->total_score }}</td>
                <td>{{ $r->grade }}</td>
                <td>@include('partials.enum-label', ['value' => $r->status])</td>
                <td>
                    @if ($r->published_at)
                        <span class="text-green-700 text-xs font-semibold">منشور</span>
                    @else
                        <span class="text-gray-500 text-xs">غير منشور</span>
                    @endif
                </td>
                <td class="space-x-2 space-x-reverse">
                    <a href="{{ route('results.results.show', $r) }}" class="text-maroon-800 hover:underline text-sm">عرض</a>
                    <a href="{{ route('results.results.edit', $r) }}" class="text-maroon-800 hover:underline text-sm">تعديل</a>
                    @if (!$r->published_at && $r->status !== 'incomplete')
                        <form action="{{ route('results.results.publish', $r) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-green-700 hover:underline text-sm">نشر</button>
                        </form>
                    @endif
                    @if ($r->published_at)
                        <form action="{{ route('results.results.unpublish', $r) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-orange-700 hover:underline text-sm">إلغاء النشر</button>
                        </form>
                    @endif
                    <form action="{{ route('results.results.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline text-sm">حذف</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center text-gray-500 py-8">لا توجد نتائج</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ ($results ?? \App\Models\Result::paginate(20))->links() }}</div>
</div>
@endsection