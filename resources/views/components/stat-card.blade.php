@props(['label', 'value', 'iconName' => null, 'icon' => '📊', 'trend' => null, 'color' => 'brand', 'href' => null])
@php
    $colors = [
        'brand'   => 'var(--color-brand-600)',
        'success' => '#10b981',
        'warning' => '#f59e0b',
        'danger'  => '#ef4444',
        'info'    => '#0ea5e9',
    ];
    $bg = $colors[$color] ?? $colors['brand'];
    $tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if($href) href="{{ $href }}" @endif class="stat-card block" style="text-decoration:none">
    <div class="flex items-start justify-between gap-3 mb-3">
        <div class="stat-icon" style="background: {{ $bg }}18; color: {{ $bg }}">
            @if($iconName)
                <x-icon :name="$iconName" :size="24" :stroke-width="2" />
            @else
                <span class="text-2xl">{{ $icon }}</span>
            @endif
        </div>
        @if($trend)
            <span class="badge badge-{{ ($trend['positive'] ?? true) ? 'success' : 'danger' }}">{{ $trend['text'] }}</span>
        @endif
    </div>
    <p class="text-2xl font-bold" style="color: var(--text)">{{ $value }}</p>
    <p class="text-xs text-muted mt-1">{{ $label }}</p>
</{{ $tag }}>