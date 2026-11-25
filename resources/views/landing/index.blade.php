<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('landing.hero_title') }} — {{ __('landing.hero_branch') }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-slate-900 overflow-x-hidden">

{{-- ========== 1. NAVBAR ========== --}}
<nav id="landing-nav" class="landing-nav">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            <a href="{{ route('landing') }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 md:w-12 md:h-12 object-contain">
                <div class="hidden sm:block">
                    <p class="text-sm md:text-base font-bold text-slate-900 leading-tight">{{ __('landing.footer_university') }}</p>
                    <p class="text-xs text-slate-500 leading-tight">{{ __('landing.footer_branch') }}</p>
                </div>
            </a>
            <div class="hidden md:flex items-center gap-8">
                <a href="#home" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition">{{ __('landing.nav_home') }}</a>
                <a href="#features" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition">{{ __('landing.nav_features') }}</a>
                <a href="#services" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition">{{ __('landing.nav_programs') }}</a>
                <a href="#about" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition">{{ __('landing.nav_about') }}</a>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="hidden sm:inline-flex px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-teal-600 transition rounded-lg hover:bg-slate-100">
                    {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm md:btn">
                        <x-icon name="home" :size="16" /> {{ __('landing.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm md:btn">
                        <x-icon name="log-out" :size="16" /> {{ __('landing.login') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ========== 2. HERO ========== --}}
<section id="home" class="landing-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 lg:py-32">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="reveal">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-teal-100 text-teal-700 text-xs md:text-sm font-bold rounded-full mb-4">
                    <span class="w-2 h-2 bg-teal-500 rounded-full animate-pulse"></span>
                    {{ __('landing.hero_badge') }}
                </span>
                <p class="text-lg md:text-xl text-slate-600 mb-2 font-medium">{{ __('landing.hero_welcome') }}</p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-3 text-slate-900">
                    <span class="gradient-text">{{ __('landing.hero_title') }}</span>
                </h1>
                <p class="text-base md:text-lg text-teal-700 font-bold mb-6">{{ __('landing.hero_branch') }}</p>
                <p class="text-base md:text-lg text-slate-600 mb-8 leading-relaxed max-w-xl">
                    {{ __('landing.hero_subtitle') }}
                </p>
                <div class="flex flex-wrap gap-3 mb-8">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg magnetic-btn">
                        <x-icon name="log-out" :size="18" /> {{ __('landing.cta_login') }}
                    </a>
                    <a href="#features" class="btn btn-secondary btn-lg">
                        {{ __('landing.cta_learn') }}
                    </a>
                </div>
                <div class="flex flex-wrap gap-6 pt-6 border-t border-slate-200">
                    <div>
                        <p class="text-2xl md:text-3xl font-extrabold text-teal-600">12,000+</p>
                        <p class="text-xs text-slate-500">{{ __('landing.hero_stat_1') }}</p>
                    </div>
                    <div>
                        <p class="text-2xl md:text-3xl font-extrabold text-teal-600">500+</p>
                        <p class="text-xs text-slate-500">{{ __('landing.hero_stat_2') }}</p>
                    </div>
                    <div>
                        <p class="text-2xl md:text-3xl font-extrabold text-teal-600">4</p>
                        <p class="text-xs text-slate-500">{{ __('landing.hero_stat_3') }}</p>
                    </div>
                </div>
            </div>
            <div class="relative reveal">
                <div class="relative z-10 flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-56 h-56 md:w-80 md:h-80 lg:w-96 lg:h-96 object-contain float-animation drop-shadow-2xl">
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-64 h-64 md:w-96 md:h-96 bg-teal-300 rounded-full blur-3xl opacity-30 animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- ========== 3. FEATURES (COLLEGES) ========== --}}
<section id="features" class="py-16 md:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <h2 class="text-2xl md:text-4xl font-extrabold text-slate-900 mb-3">{{ __('landing.features_title') }}</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">{{ __('landing.features_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $colleges = [
                    ['icon' => 'heart-pulse', 'color' => 'rose', 'title' => __('landing.college_medicine_title'), 'desc' => __('landing.college_medicine_desc'), 'programs' => '8', 'students' => '4,500'],
                    ['icon' => 'pill', 'color' => 'amber', 'title' => __('landing.college_pharmacy_title'), 'desc' => __('landing.college_pharmacy_desc'), 'programs' => '4', 'students' => '2,000'],
                    ['icon' => 'heart', 'color' => 'pink', 'title' => __('landing.college_nursing_title'), 'desc' => __('landing.college_nursing_desc'), 'programs' => '3', 'students' => '3,200'],
                    ['icon' => 'smile', 'color' => 'blue', 'title' => __('landing.college_dentistry_title'), 'desc' => __('landing.college_dentistry_desc'), 'programs' => '3', 'students' => '2,300'],
                ];
                $colorMap = [
                    'rose' => 'bg-rose-100 text-rose-600',
                    'amber' => 'bg-amber-100 text-amber-600',
                    'pink' => 'bg-pink-100 text-pink-600',
                    'blue' => 'bg-blue-100 text-blue-600',
                ];
            @endphp
            @foreach($colleges as $c)
                <div class="feature-card reveal">
                    <div class="w-14 h-14 rounded-xl {{ $colorMap[$c['color']] }} flex items-center justify-center mb-4">
                        <x-icon :name="$c['icon']" :size="26" />
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">{{ $c['title'] }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-3">{{ $c['desc'] }}</p>
                    <div class="flex items-center gap-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                        <span><strong class="text-slate-700">{{ $c['programs'] }}</strong> {{ __('landing.college_programs_count') }}</span>
                        <span><strong class="text-slate-700">{{ $c['students'] }}</strong> {{ __('landing.college_students_count') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== 4. STATS ========== --}}
<section class="py-16 md:py-20 bg-gradient-to-br from-teal-600 to-teal-800 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-teal-300 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 reveal">
            <h2 class="text-2xl md:text-3xl font-extrabold mb-2">{{ __('landing.stats_title') }}</h2>
            <p class="text-teal-100">{{ __('landing.stats_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            @php
                $statItems = [
                    ['count' => 12000, 'suffix' => '+', 'label' => __('landing.stats_students')],
                    ['count' => 500, 'suffix' => '+', 'label' => __('landing.stats_staff')],
                    ['count' => 25, 'suffix' => '+', 'label' => __('landing.stats_programs')],
                    ['count' => 120, 'suffix' => '+', 'label' => __('landing.stats_courses')],
                ];
            @endphp
            @foreach($statItems as $s)
                <div class="text-center reveal">
                    <p class="stat-number text-3xl md:text-5xl font-extrabold mb-1" data-count="{{ $s['count'] }}" data-suffix="{{ $s['suffix'] }}">0</p>
                    <p class="text-xs md:text-sm text-teal-100">{{ $s['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== 5. WHY US ========== --}}
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <h2 class="text-2xl md:text-4xl font-extrabold text-slate-900 mb-3">{{ __('landing.why_title') }}</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">{{ __('landing.why_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $whys = [
                    ['icon' => 'check-circle', 'title' => __('landing.why_1'), 'desc' => __('landing.why_1_desc')],
                    ['icon' => 'users', 'title' => __('landing.why_2'), 'desc' => __('landing.why_2_desc')],
                    ['icon' => 'library', 'title' => __('landing.why_3'), 'desc' => __('landing.why_3_desc')],
                    ['icon' => 'trending-up', 'title' => __('landing.why_4'), 'desc' => __('landing.why_4_desc')],
                ];
            @endphp
            @foreach($whys as $w)
                <div class="flex items-start gap-4 p-5 rounded-xl bg-slate-50 border border-slate-100 hover:border-teal-300 hover:bg-teal-50/30 transition reveal">
                    <div class="w-12 h-12 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center shrink-0">
                        <x-icon :name="$w['icon']" :size="24" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">{{ $w['title'] }}</h3>
                        <p class="text-sm text-slate-600">{{ $w['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== 6. HOW IT WORKS (REGISTRATION) ========== --}}
<section class="py-16 md:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <h2 class="text-2xl md:text-4xl font-extrabold text-slate-900 mb-3">{{ __('landing.how_title') }}</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">{{ __('landing.how_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
            @php
                $steps = [
                    ['num' => '1', 'title' => __('landing.how_step1_title'), 'desc' => __('landing.how_step1_desc')],
                    ['num' => '2', 'title' => __('landing.how_step2_title'), 'desc' => __('landing.how_step2_desc')],
                    ['num' => '3', 'title' => __('landing.how_step3_title'), 'desc' => __('landing.how_step3_desc')],
                    ['num' => '4', 'title' => __('landing.how_step4_title'), 'desc' => __('landing.how_step4_desc')],
                ];
            @endphp
            @foreach($steps as $step)
                <div class="text-center reveal relative">
                    <div class="step-circle mx-auto mb-4">{{ $step['num'] }}</div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-600 max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== 7. E-SERVICES ========== --}}
<section id="services" class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <h2 class="text-2xl md:text-4xl font-extrabold text-slate-900 mb-3">{{ __('landing.services_title') }}</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">{{ __('landing.services_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $services = [
                    ['icon' => 'award', 'color' => 'teal', 'title' => __('landing.service_certificate_title'), 'desc' => __('landing.service_certificate_desc')],
                    ['icon' => 'clipboard', 'color' => 'blue', 'title' => __('landing.service_register_title'), 'desc' => __('landing.service_register_desc')],
                    ['icon' => 'book', 'color' => 'amber', 'title' => __('landing.service_lms_title'), 'desc' => __('landing.service_lms_desc')],
                    ['icon' => 'wallet', 'color' => 'emerald', 'title' => __('landing.service_payment_title'), 'desc' => __('landing.service_payment_desc')],
                    ['icon' => 'file-text', 'color' => 'purple', 'title' => __('landing.service_results_title'), 'desc' => __('landing.service_results_desc')],
                    ['icon' => 'alert-circle', 'color' => 'rose', 'title' => __('landing.service_support_title'), 'desc' => __('landing.service_support_desc')],
                ];
                $serviceColors = [
                    'teal' => 'from-teal-500 to-teal-600',
                    'blue' => 'from-blue-blue-500 to-blue-600',
                    'amber' => 'from-amber-500 to-amber-600',
                    'emerald' => 'from-emerald-500 to-emerald-600',
                    'purple' => 'from-purple-500 to-purple-600',
                    'rose' => 'from-rose-500 to-rose-600',
                ];
            @endphp
            @foreach($services as $s)
                <div class="group relative rounded-2xl overflow-hidden bg-white border border-slate-200 hover:border-transparent hover:shadow-2xl transition-all duration-300 reveal">
                    <div class="absolute inset-0 bg-gradient-to-br {{ $serviceColors[$s['color']] }} opacity-0 group-hover:opacity-5 transition-opacity"></div>
                    <div class="p-6 relative">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $serviceColors[$s['color']] }} text-white flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                            <x-icon :name="$s['icon']" :size="26" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $s['title'] }}</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">{{ $s['desc'] }}</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-sm font-bold text-teal-600 hover:gap-2 transition-all">
                            {{ __('landing.service_go') }}
                            <span class="rtl:rotate-180">→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== 8. CTA ========== --}}
<section id="about" class="py-16 md:py-24 bg-gradient-to-br from-teal-600 via-teal-700 to-teal-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-teal-300 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-4">{{ __('landing.cta_title') }}</h2>
        <p class="text-teal-100 text-base md:text-lg mb-8 max-w-2xl mx-auto">{{ __('landing.cta_subtitle') }}</p>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-teal-700 font-bold rounded-xl hover:bg-teal-50 transition shadow-xl hover:scale-105 transform magnetic-btn">
            <x-icon name="log-out" :size="20" /> {{ __('landing.cta_button') }}
        </a>
    </div>
</section>

{{-- ========== 9. FOOTER ========== --}}
<footer class="bg-slate-900 text-slate-300 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain bg-white rounded-lg p-1">
                    <div>
                        <p class="text-white font-bold">{{ __('landing.footer_university') }}</p>
                        <p class="text-xs text-slate-400">{{ __('landing.footer_branch') }}</p>
                    </div>
                </div>
                <p class="text-sm leading-relaxed">{{ __('landing.footer_about') }}</p>
            </div>
            <div>
                <p class="text-white font-bold mb-3">{{ __('landing.footer_links') }}</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="#home" class="hover:text-teal-400 transition">{{ __('landing.nav_home') }}</a></li>
                    <li><a href="#features" class="hover:text-teal-400 transition">{{ __('landing.nav_features') }}</a></li>
                    <li><a href="#services" class="hover:text-teal-400 transition">{{ __('landing.nav_programs') }}</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-teal-400 transition">{{ __('landing.login') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-bold mb-3">{{ __('landing.footer_contact') }}</p>
                <p class="text-sm flex items-center gap-2 mb-1">
                    <x-icon name="home" :size="14" /> {{ __('landing.footer_address') }}
                </p>
                <p class="text-sm flex items-center gap-2 mb-1">
                    <x-icon name="file-text" :size="14" /> {{ __('landing.footer_email') }}
                </p>
                <p class="text-sm flex items-center gap-2">
                    <x-icon name="clock" :size="14" /> {{ __('landing.footer_phone') }}
                </p>
            </div>
        </div>
        <div class="border-t border-slate-700 pt-6 text-center text-xs text-slate-500">
            © {{ date('Y') }} {{ __('landing.footer_university') }} — {{ __('landing.footer_branch') }} — {{ __('landing.footer_rights') }}
        </div>
    </div>
</footer>

{{-- ========== SCRIPTS ========== --}}
<script>
    // Navbar scroll
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('landing-nav');
        if (nav) nav.classList.toggle('scrolled', window.scrollY > 20);
    });

    // Reveal on scroll with stagger
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((e, i) => {
            if (e.isIntersecting) {
                setTimeout(() => e.target.classList.add('revealed'), i * 80);
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Count-up with suffix
    const counters = document.querySelectorAll('[data-count]');
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const el = e.target;
                const target = +el.dataset.count;
                const suffix = el.dataset.suffix || '';
                if (!target) { el.textContent = '0' + suffix; counterObserver.unobserve(el); return; }
                const duration = 2000;
                const start = performance.now();
                const animate = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(eased * target).toLocaleString('{{ app()->getLocale() === 'ar' ? 'ar-EG' : 'en-US' }}') + suffix;
                    if (progress < 1) requestAnimationFrame(animate);
                };
                requestAnimationFrame(animate);
                counterObserver.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(el => counterObserver.observe(el));

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', (e) => {
            const href = a.getAttribute('href');
            if (href.length > 1) {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Magnetic buttons
    document.querySelectorAll('.magnetic-btn').forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0, 0)';
        });
    });
</script>
</body>
</html>
