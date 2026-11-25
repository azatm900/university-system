@props(['title' => null, 'icon' => null, 'padding' => 'p-5', 'class' => ''])
<div {{ $attributes->merge(['class' => 'card ' . $class]) }}>
    @if($title)
        <div class="px-5 py-3 border-b border-default flex items-center gap-2">
            @if($icon)<span class="text-lg">{{ $icon }}</span>@endif
            <h3 class="text-sm font-bold" style="color: var(--text)">{{ $title }}</h3>
            @isset($actions)<div class="ms-auto">{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="px-5 py-3 border-t border-default">{{ $footer }}</div>
    @endisset
</div>