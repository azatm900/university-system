@php
    $locale = session('locale', 'ar');
    $role = auth()->user()?->role?->value;
    $canSeeNotifications = in_array($role, ['admin', 'registrar', 'finance'], true);
@endphp
<header class="navbar">
    <div class="flex items-center gap-2 md:gap-3 min-w-0 flex-1">
        <button id="sidebar-toggle" class="icon-btn shrink-0 md:hidden" aria-label="{{ __('navigation.toggle_sidebar') }}" data-sidebar-toggle>
            <x-icon name="menu" :size="22" />
        </button>
        <div class="min-w-0">
            <h2 class="text-sm md:text-lg font-bold truncate" style="color: var(--text, inherit)">@yield('title', __('dashboard.title'))</h2>
        </div>
    </div>

    <div class="flex items-center gap-1 md:gap-2 shrink-0">

        {{-- 🔔 Notifications Bell --}}
        @if($canSeeNotifications)
        <div class="relative shrink-0">
            <button type="button" id="notif-btn" class="icon-btn relative"
                    data-notif-toggle
                    title="{{ __('navigation.notifications') }}"
                    aria-label="{{ __('navigation.notifications') }}">
                <x-icon name="bell" :size="19" />
                <span id="notif-badge" class="absolute top-1 end-1 min-w-[16px] h-4 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center hidden">0</span>
            </button>
            <div id="notif-menu" class="dropdown-menu absolute top-full mt-2 z-50 hidden min-w-[280px] {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }}">
                <div class="px-3 py-2 border-b border-default flex items-center justify-between">
                    <span class="text-sm font-semibold">{{ __('navigation.notifications') }}</span>
                </div>
                <div id="notif-empty" class="p-4 text-center text-sm text-muted">
                    {{ __('navigation.no_notifications') }}
                </div>
                <div id="notif-list" data-notif-list class="max-h-80 overflow-y-auto"></div>
            </div>
        </div>
        @endif

        <a href="{{ route('locale.switch', $locale === 'ar' ? 'en' : 'ar') }}" class="icon-btn lang-btn" title="{{ __('navigation.language') }}">
            <span class="text-[13px] font-bold tracking-tight">{{ $locale === 'ar' ? 'EN' : 'ع' }}</span>
        </a>

        <button id="theme-toggle" class="icon-btn" title="{{ __('navigation.toggle_theme') }}">
            <span data-icon="sun" class="flex items-center justify-center dark:hidden">
                <x-icon name="sun" :size="19" />
            </span>
            <span data-icon="moon" class="hidden items-center justify-center dark:flex">
                <x-icon name="moon" :size="19" />
            </span>
        </button>

        <div class="relative shrink-0">
            <button type="button" id="user-menu-btn" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-surface-2 transition focus:outline-none" data-user-menu-toggle>
                <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem; flex-shrink: 0; border-radius: 9999px; background-color: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: bold;">{{ mb_strtoupper(mb_substr(auth()->user()?->name ?? 'U', 0, 1)) }}</div>
                <div class="hidden md:block text-start min-w-0">
                    <p class="text-sm font-semibold leading-tight truncate" style="color: var(--text, inherit)">{{ auth()->user()?->name }}</p>
                </div>
                <span class="hidden md:inline-flex text-muted transition-transform duration-200" id="user-menu-arrow"><x-icon name="chevron-down" :size="14" /></span>
            </button>

            <div id="user-menu" class="dropdown-menu absolute top-full mt-2 z-50 hidden min-w-[180px] {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }}">
                <div class="p-1 bg-surface border border-default rounded-lg shadow-lg" style="background-color: var(--surface, white); border-color: var(--border, #e5e7eb);">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item flex items-center gap-2 px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition" style="color: var(--text, inherit);">
                        <x-icon name="user" :size="16" /> {{ __('profile.title') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item flex items-center gap-2 px-3 py-2 text-sm rounded-md w-full text-start transition text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <x-icon name="log-out" :size="16" /> {{ __('auth.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</header>