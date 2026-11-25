@extends('layouts.app')

@section('title', 'تسجيلات فصل')

@section('content')
<div class="container mx-auto p-6" dir="rtl">
    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="text-maroon-700 hover:underline">← التقارير</a>
    </div>
    <h1 class="text-2xl font-bold mb-4 text-maroon-700">تسجيلات فصل</h1>
    <p class="text-gray-600 mb-4">اختر فصلاً لعرض تسجيلاته.</p>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكود</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">البداية</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">النهاية</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">نشط</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($semesters as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-mono">{{ $s->code }}</td>
                        <td class="px-4 py-3 text-sm">{{ $s->name }}</td>
                        <td class="px-4 py-3 text-sm">{{ $s->start_date }}</td>
                        <td class="px-4 py-3 text-sm">{{ $s->end_date }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($s->is_active)<span class="text-green-700">نشط</span>@else<span class="text-gray-500">—</span>@endif
                        </td>
                        <td class="px-4 py-3 text-left">
                            <a href="{{ route('reports.semester-registrations.show', $s) }}"
                               class="bg-maroon-700 text-white px-3 py-1 rounded text-xs hover:bg-maroon-800">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">لا توجد فصول.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection