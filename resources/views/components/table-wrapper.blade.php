@props(['title' => null, 'actions' => null])
<div class="card overflow-hidden">
    @if($title || $actions)
        <div class="px-5 py-3 border-b border-default flex items-center justify-between gap-2">
            @if($title)<h3 class="text-sm font-bold">{{ $title }}</h3>@endif
            @if($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endif
        </div>
    @endif
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="px-5 py-3 border-t border-default">{{ $footer }}</div>
    @endisset
</div>