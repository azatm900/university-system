@extends('layouts.app')
@section('title', 'البرامج الأكاديمية')
@section('content')
<div class="card overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
        <a href="{{ route('registrar.programs.create') }}" class="btn-primary">+ برنامج جديد</a>
    </div>
    <table class="w-full table-rh">
        <thead><tr><th>#</th><th>الرمز</th><th>الاسم</th><th>المستوى</th><th>المدة</th><th>الحالة</th><th>إجراءات</th></tr></thead>
        <tbody>
        @forelse (($programs ?? \App\Models\Program::latest()->paginate(20)) as $prog)
            <tr>
                <td>{{ $prog->id }}</td>
                <td>{{ $prog->code }}</td>
                <td>{{ $prog->name }}</td>
                <td>@include('partials.enum-label', ['value' => $prog->degree_level])</td>
                <td>{{ $prog->duration_years }} سنة</td>
                <td>@if($prog->is_active)<span class="text-green-700 text-xs font-semibold">نشط</span>@else<span class="text-red-700 text-xs font-semibold">معطل</span>@endif</td>
                <td class="space-x-2 space-x-reverse">
                    <a href="{{ route('registrar.programs.show', $prog) }}" class="text-maroon-800 hover:underline text-sm">عرض</a>
                    <a href="{{ route('registrar.programs.edit', $prog) }}" class="text-maroon-800 hover:underline text-sm">تعديل</a>
                    <form action="{{ route('registrar.programs.destroy', $prog) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline text-sm">حذف</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-gray-500 py-8">لا توجد برامج</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ ($programs ?? \App\Models\Program::paginate(20))->links() }}</div>
</div>
@endsection