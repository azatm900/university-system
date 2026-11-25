@props(['title', 'subtitle' => null, 'description' => null, 'actions' => null])
@php
    $subtitle = $subtitle ?? $description;
    $slotHtml = isset($slot) ? $slot->toHtml() : '';
    $hasSlot  = trim($slotHtml) !== '';
@endphp
<div class="flex items-start justify-between gap-3 mb-5 flex-wrap">
    <div class="min-w-0">
        <h1 class="text-xl md:text-2xl font-bold truncate" style="color: var(--text)">{{ $title }}</h1>
        @if($subtitle)<p class="text-sm text-muted mt-1">{{ $subtitle }}</p>@endif
    </div>
    @if($actions || $hasSlot)
        <div class="flex items-center gap-2 flex-wrap">
            @if($actions){{ $actions }}@endif
            @if($hasSlot){!! $slotHtml !!}@endif
        </div>
    @endif
</div>