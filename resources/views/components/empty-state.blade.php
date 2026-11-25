@props(['icon' => '📄', 'title' => null, 'message' => null, 'description' => null, 'action' => null])
@php $message = $message ?? $description; @endphp
<div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="text-5xl mb-3 opacity-40">{{ $icon }}</div>
    @if($title)<p class="font-semibold mb-1">{{ $title }}</p>@endif
    <p class="text-sm text-muted">{{ $message ?? __('common.no_data') }}</p>
    @if($action)<div class="mt-4">{{ $action }}</div>@endif
</div>