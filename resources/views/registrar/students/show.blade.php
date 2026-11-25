@extends('layouts.app')
@section('title', $student->user?->name ?? __('students.title'))

@section('content')
<div class="space-y-6">
    <x-page-header :title="$student->user?->name ?? 'â€”'"
                   :subtitle="__('students.fields.student_number') . ': ' . $student->student_number">
        <x-button variant="primary" :href="route('registrar.students.edit', $student)">
            <x-icon name="pencil" :size="16" />
            <span class="ms-1">{{ __('students.actions.edit') }}</span>
        </x-button>
        <x-button variant="secondary" :href="route('registrar.students.index')">
            {{ __('students.actions.back') }}
        </x-button>
        @if (method_exists($student, 'trashed') && $student->trashed())
            <form method="POST" action="{{ route('registrar.students.restore', $student->id) }}" class="inline"
                  onsubmit="return confirm({{ json_encode(__('students.actions.confirm_restore')) }});">
                @csrf
                <x-button variant="secondary" type="submit">
                    <x-icon name="trending-up" :size="16" />
                    <span class="ms-1">{{ __('students.actions.restore') }}</span>
                </x-button>
            </form>
        @else
            <form method="POST" action="{{ route('registrar.students.destroy', $student) }}" class="inline"
                  onsubmit="return confirm({{ json_encode(__('students.actions.confirm_delete')) }});">
                @csrf @method('DELETE')
                <x-button variant="secondary" type="submit" class="text-red-600">
                    <x-icon name="trash" :size="16" />
                    <span class="ms-1">{{ __('students.actions.delete') }}</span>
                </x-button>
            </form>
        @endif
    </x-page-header>

    @if (method_exists($student, 'trashed') && $student->trashed())
        <div class="p-3 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200 text-sm">
            {{ __('students.show.deleted_badge') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat-card icon-name="file-text" :label="__('students.stats.registrations')" :value="$student->registrations->count()" />
        <x-stat-card icon-name="receipt"   :label="__('students.stats.invoices')"      :value="$student->invoices->count()" />
        <x-stat-card icon-name="award"     :label="__('students.stats.results')"        :value="$student->results->count()" />
    </div>

    <div id="student-tabs">
        <div class="flex gap-1 border-b mb-4" role="tablist">
            <button type="button" data-tab="details"       class="tab-btn is-active px-4 py-2 text-sm font-semibold border-b-2 border-teal-500">{{ __('students.show.details') }}</button>
            <button type="button" data-tab="registrations" class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent">{{ __('students.show.registrations') }} ({{ $student->registrations->count() }})</button>
            <button type="button" data-tab="invoices"      class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent">{{ __('students.show.invoices') }} ({{ $student->invoices->count() }})</button>
            <button type="button" data-tab="results"       class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent">{{ __('students.show.results') }} ({{ $student->results->count() }})</button>
        </div>

        <div data-panel="details">
            <x-card>
                <h3 class="font-semibold text-lg mb-4 border-b pb-2">{{ __('students.show.personal') }}</h3>
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-y-3 text-sm">
                    <dt class="text-gray-500">{{ __('students.fields.name') }}</dt>
                    <dd class="md:col-span-2">{{ $student->user?->name ?? 'â€”' }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.email') }}</dt>
                    <dd class="md:col-span-2">{{ $student->user?->email ?? 'â€”' }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.student_number') }}</dt>
                    <dd class="md:col-span-2 font-mono">{{ $student->student_number }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.national_id') }}</dt>
                    <dd class="md:col-span-2">{{ $student->national_id ?? 'â€”' }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.date_of_birth') }}</dt>
                    <dd class="md:col-span-2">{{ $student->date_of_birth ?? 'â€”' }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.gender') }}</dt>
                    <dd class="md:col-span-2">
                        @php $gv = $student->gender instanceof \BackedEnum ? $student->gender->value : $student->gender; @endphp
                        {{ $gv ? __('students.genders.' . $gv) : 'â€”' }}
                    </dd>
                </dl>

                <h3 class="font-semibold text-lg mt-6 mb-4 border-b pb-2">{{ __('students.show.academic') }}</h3>
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-y-3 text-sm">
                    <dt class="text-gray-500">{{ __('students.fields.program') }}</dt>
                    <dd class="md:col-span-2">{{ $student->program?->name ?? 'â€”' }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.enrollment_date') }}</dt>
                    <dd class="md:col-span-2">{{ $student->enrollment_date ?? 'â€”' }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.status') }}</dt>
                    <dd class="md:col-span-2">
                        @php $sv = $student->status instanceof \BackedEnum ? $student->status->value : $student->status; @endphp
                        <x-badge :color="match($sv) {
                            'active'    => 'success',
                            'suspended' => 'warning',
                            'graduated' => 'info',
                            'withdrawn' => 'danger',
                            default     => 'primary',
                        }">{{ __('students.statuses.' . $sv) }}</x-badge>
                    </dd>
                </dl>

                <h3 class="font-semibold text-lg mt-6 mb-4 border-b pb-2">{{ __('students.show.contact') }}</h3>
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-y-3 text-sm">
                    <dt class="text-gray-500">{{ __('students.fields.phone') }}</dt>
                    <dd class="md:col-span-2">{{ $student->phone ?? 'â€”' }}</dd>
                    <dt class="text-gray-500">{{ __('students.fields.address') }}</dt>
                    <dd class="md:col-span-2">{{ $student->address ?? 'â€”' }}</dd>
                </dl>
            </x-card>
        </div>

        <div data-panel="registrations" class="hidden">
            <x-card>
                @if ($student->registrations->isEmpty())
                    <x-empty-state icon="ðŸ“‹" :title="__('students.show.registrations')" :description="'â€”'" />
                @else
                    <table class="table w-full text-sm">
                        <thead><tr>
                            <th>#</th>
                            <th>{{ __('students.fields.program') }}</th>
                            <th>{{ __('students.fields.status') }}</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($student->registrations as $r)
                                <tr>
                                    <td>{{ $r->id }}</td>
                                    <td>{{ $r->course?->name ?? 'â€”' }}</td>
                                    <td>{{ $r->status ?? 'â€”' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </x-card>
        </div>

        <div data-panel="invoices" class="hidden">
            <x-card>
                @if ($student->invoices->isEmpty())
                    <x-empty-state icon="ðŸ’³" :title="__('students.show.invoices')" :description="'â€”'" />
                @else
                    <table class="table w-full text-sm">
                        <thead><tr>
                            <th>#</th>
                            <th>{{ __('students.fields.status') }}</th>
                            <th>Total</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($student->invoices as $inv)
                                <tr>
                                    <td class="font-mono">{{ $inv->invoice_number ?? $inv->id }}</td>
                                    <td>{{ $inv->status ?? 'â€”' }}</td>
                                    <td>{{ $inv->total_amount ?? 'â€”' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </x-card>
        </div>

        <div data-panel="results" class="hidden">
            <x-card>
                @if ($student->results->isEmpty())
                    <x-empty-state icon="ðŸ†" :title="__('students.show.results')" :description="'â€”'" />
                @else
                    <table class="table w-full text-sm">
                        <thead><tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Mark</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($student->results as $r)
                                <tr>
                                    <td>{{ $r->id }}</td>
                                    <td>{{ $r->course?->name ?? 'â€”' }}</td>
                                    <td>{{ $r->mark ?? $r->score ?? 'â€”' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </x-card>
        </div>
    </div>
</div>

<script>
(function () {
    var root = document.getElementById('student-tabs');
    if (!root) return;
    var btns   = root.querySelectorAll('[data-tab]');
    var panels = root.querySelectorAll('[data-panel]');
    btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var name = btn.dataset.tab;
            btns.forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
                b.classList.toggle('border-teal-500', b === btn);
                b.classList.toggle('border-transparent', b !== btn);
            });
            panels.forEach(function (p) {
                p.classList.toggle('hidden', p.dataset.panel !== name);
            });
        });
    });
})();
</script>
@endsection