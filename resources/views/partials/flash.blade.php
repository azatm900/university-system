<div class="toast-container">
    @if (session('success'))
        <div class="toast toast-success" data-auto-dismiss="5000">
            <span style="color:#10b981; font-size:1.1rem">✓</span>
            <span style="flex:1" class="text-sm">{{ session('success') }}</span>
            <button class="toast-close" type="button" aria-label="{{ __('common.close') }}">×</button>
        </div>
    @endif
    @if (session('error'))
        <div class="toast toast-error" data-auto-dismiss="6000">
            <span style="color:#ef4444; font-size:1.1rem">⚠</span>
            <span style="flex:1" class="text-sm">{{ session('error') }}</span>
            <button class="toast-close" type="button" aria-label="{{ __('common.close') }}">×</button>
        </div>
    @endif
    @if (session('warning'))
        <div class="toast toast-warning" data-auto-dismiss="5000">
            <span style="color:#f59e0b; font-size:1.1rem">!</span>
            <span style="flex:1" class="text-sm">{{ session('warning') }}</span>
            <button class="toast-close" type="button" aria-label="{{ __('common.close') }}">×</button>
        </div>
    @endif
    @if (isset($errors) && $errors->any())
        <div class="toast toast-error" data-auto-dismiss="8000">
            <span style="color:#ef4444; font-size:1.1rem">⚠</span>
            <div style="flex:1" class="text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
            <button class="toast-close" type="button" aria-label="{{ __('common.close') }}">×</button>
        </div>
    @endif
</div>