@extends('layouts.app')
@section('title', __('students.actions.edit'))

@section('content')
<div class="space-y-6">
    <x-page-header :title="__('students.actions.edit')" :description="$student->user?->name ?? ''">
        <x-button variant="secondary" :href="route('registrar.students.show', $student)">
            {{ __('students.actions.back') }}
        </x-button>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('registrar.students.update', $student) }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.user') }} *</label>
                    <select name="user_id" required class="input w-full">
                        @foreach (($users ?? \App\Models\User::where('role','student')->get()) as $u)
                            <option value="{{ $u->id }}" @selected((int) old('user_id', $student->user_id) === $u->id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.program') }} *</label>
                    <select name="program_id" required class="input w-full">
                        @foreach (($programs ?? \App\Models\Program::all()) as $pr)
                            <option value="{{ $pr->id }}" @selected((int) old('program_id', $student->program_id) === $pr->id)>
                                {{ $pr->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.student_number') }} *</label>
                    <input name="student_number" value="{{ old('student_number', $student->student_number) }}" required class="input w-full">
                    @error('student_number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.national_id') }}</label>
                    <input name="national_id" value="{{ old('national_id', $student->national_id) }}" class="input w-full">
                    @error('national_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.date_of_birth') }} *</label>
                    <input name="date_of_birth" type="date" value="{{ old('date_of_birth', $student->date_of_birth) }}" required class="input w-full">
                    @error('date_of_birth')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                @php $cg = $student->gender instanceof \BackedEnum ? $student->gender->value : $student->gender; @endphp
                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.gender') }} *</label>
                    <select name="gender" required class="input w-full">
                        @foreach (\App\Enums\StudentGender::cases() as $g)
                            <option value="{{ $g->value }}" @selected(old('gender', $cg) === $g->value)>
                                {{ __('students.genders.' . $g->value) }}
                            </option>
                        @endforeach
                    </select>
                    @error('gender')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.phone') }}</label>
                    <input name="phone" value="{{ old('phone', $student->phone) }}" class="input w-full">
                    @error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.enrollment_date') }} *</label>
                    <input name="enrollment_date" type="date" value="{{ old('enrollment_date', $student->enrollment_date) }}" required class="input w-full">
                    @error('enrollment_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.address') }}</label>
                    <textarea name="address" rows="2" class="input w-full">{{ old('address', $student->address) }}</textarea>
                    @error('address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                @php $cs = $student->status instanceof \BackedEnum ? $student->status->value : $student->status; @endphp
                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('students.fields.status') }} *</label>
                    <select name="status" required class="input w-full">
                        @foreach (\App\Enums\StudentStatus::cases() as $s)
                            <option value="{{ $s->value }}" @selected(old('status', $cs) === $s->value)>
                                {{ __('students.statuses.' . $s->value) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-2 pt-2 border-t">
                <x-button variant="primary" type="submit">
                    <x-icon name="check-circle" :size="16" />
                    <span class="ms-1">{{ __('students.actions.save') }}</span>
                </x-button>
                <x-button variant="secondary" :href="route('registrar.students.index')">
                    {{ __('students.actions.cancel') }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection