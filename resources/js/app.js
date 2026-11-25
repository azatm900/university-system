import '@fontsource/cairo/400.css';
import '@fontsource/cairo/600.css';
import '@fontsource/cairo/700.css';
import '@fontsource/cairo/800.css';

// Chart.js (يُحمَّل عبر Vite فقط)
import {
    Chart,
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement,
    PointElement,
    Tooltip,
    Legend,
    Filler,
    BarController,
    LineController,
} from 'chart.js';

Chart.register(
    CategoryScale, LinearScale, BarElement, LineElement, PointElement,
    Tooltip, Legend, Filler, BarController, LineController
);

// تعريف Chart عالمياً لاستخدامه في أي Blade
window.Chart = Chart;

// ============================================================
// DASHBOARD CHARTS (admin only — data injected via data-* attrs)
// ============================================================
(function () {
    document.addEventListener('DOMContentLoaded', () => {
        const regEl = document.getElementById('chartRegistrations');
        const payEl = document.getElementById('chartPayments');

        if (regEl) {
            const months = JSON.parse(regEl.dataset.months || '[]');
            const values = JSON.parse(regEl.dataset.values || '[]');
            const label = regEl.dataset.label || '';
            new Chart(regEl, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: label,
                        data: values,
                        backgroundColor: 'rgba(20, 184, 166, 0.6)',
                        borderColor: 'rgba(20, 184, 166, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        }

        if (payEl) {
            const months = JSON.parse(payEl.dataset.months || '[]');
            const values = JSON.parse(payEl.dataset.values || '[]');
            const label = payEl.dataset.label || '';
            new Chart(payEl, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: label,
                        data: values,
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    });
})();

// ============================================================
// THEME TOGGLE (dark mode)
// ============================================================
(function () {
    const root = document.documentElement;

    function apply(theme) {
        if (theme === 'dark') root.classList.add('dark');
        else root.classList.remove('dark');
        try { localStorage.setItem('theme', theme); } catch (e) {}

        document.querySelectorAll('[data-icon="sun"]').forEach(el => {
            el.classList.toggle('hidden', theme === 'dark');
            el.classList.toggle('flex', theme !== 'dark');
        });
        document.querySelectorAll('[data-icon="moon"]').forEach(el => {
            el.classList.toggle('hidden', theme !== 'dark');
            el.classList.toggle('flex', theme === 'dark');
        });
    }

    // Apply saved theme immediately
    apply(localStorage.getItem('theme') || 'light');

    // Bind all theme toggle buttons
    document.querySelectorAll('#theme-toggle, [data-theme-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            apply(root.classList.contains('dark') ? 'light' : 'dark');
        });
    });
})();

// ============================================================
// SIDEBAR (collapse desktop + drawer mobile)
// ============================================================
(function () {
    const sidebar = document.querySelector('.sidebar') || document.getElementById('sidebar');
    const toggleBtns = document.querySelectorAll('#sidebar-toggle, [data-sidebar-toggle]');
    const backdrop = document.getElementById('backdrop');
    if (!sidebar || toggleBtns.length === 0) return;

    function isMobile() { return window.matchMedia('(max-width: 1023px)').matches; }

    if (!isMobile() && localStorage.getItem('sidebar-collapsed') === '1') {
        sidebar.classList.add('collapsed');
    }

    function openDrawer() {
        sidebar.classList.add('mobile-open');
        if (backdrop) {
            backdrop.classList.remove('hidden');
            setTimeout(() => backdrop.classList.add('show'), 10);
        }
        document.body.classList.add('sidebar-open');
    }
    function closeDrawer() {
        sidebar.classList.remove('mobile-open');
        if (backdrop) {
            backdrop.classList.remove('show');
            setTimeout(() => backdrop.classList.add('hidden'), 300);
        }
        document.body.classList.remove('sidebar-open');
    }

    toggleBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (isMobile()) {
                if (sidebar.classList.contains('mobile-open')) closeDrawer();
                else openDrawer();
            } else {
                sidebar.classList.toggle('collapsed');
                try {
                    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
                } catch (e) {}
            }
        });
    });

    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    sidebar.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', () => {
            if (isMobile()) closeDrawer();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('mobile-open')) closeDrawer();
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            if (backdrop) {
                backdrop.classList.remove('show');
                backdrop.classList.add('hidden');
            }
            document.body.classList.remove('sidebar-open');
        }
    });
})();

// ============================================================
// DROPDOWNS (user menu + notifications)
// — uses .open (matches app.css) NOT .hidden
// ============================================================
(function () {
    const userBtn = document.querySelector('#user-menu-btn, [data-user-menu-toggle]');
    const userMenu = document.getElementById('user-menu');
    const userArrow = document.getElementById('user-menu-arrow');

    const notifBtn = document.querySelector('#notif-btn, [data-notif-toggle]');
    const notifMenu = document.getElementById('notif-menu');

    function closeAll() {
        if (userMenu) { userMenu.classList.remove('open'); userMenu.classList.add('hidden'); }
        if (userArrow) userArrow.style.transform = 'rotate(0deg)';
        if (notifMenu) { notifMenu.classList.remove('open'); notifMenu.classList.add('hidden'); }
    }

    function open(el) {
        if (!el) return;
        el.classList.remove('hidden');
        // force reflow so opacity transition plays
        void el.offsetWidth;
        el.classList.add('open');
    }

    function toggle(el) {
        if (!el) return;
        const willOpen = !el.classList.contains('open');
        closeAll();
        if (willOpen) open(el);
    }

    if (userBtn) {
        userBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggle(userMenu);
            if (userMenu && userMenu.classList.contains('open') && userArrow) {
                userArrow.style.transform = 'rotate(180deg)';
            }
        });
    }

    if (notifBtn) {
        notifBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggle(notifMenu);
            if (notifMenu && notifMenu.classList.contains('open')) fetchNotifs();
        });
    }

    document.addEventListener('click', (e) => {
        if (userMenu && userBtn && !userMenu.contains(e.target) && !userBtn.contains(e.target)) {
            userMenu.classList.remove('open');
            userMenu.classList.add('hidden');
            if (userArrow) userArrow.style.transform = 'rotate(0deg)';
        }
        if (notifMenu && notifBtn && !notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
            notifMenu.classList.remove('open');
            notifMenu.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
    });
})();

// ============================================================
// NOTIFICATIONS FETCH
// ============================================================
function fetchNotifs() {
    const badge = document.getElementById('notif-badge');
    const list = document.getElementById('notif-list');
    const empty = document.getElementById('notif-empty');
    if (!badge && !list) return;

    // Get URL from meta tag or default
    const url = document.querySelector('meta[name="notifications-url"]')?.content || '/api/notifications';

    fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    })
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
            if (badge && data.total > 0) {
                badge.textContent = data.total > 99 ? '99+' : data.total;
                badge.classList.remove('hidden');
            } else if (badge) {
                badge.classList.add('hidden');
            }
            if (list && data.items && data.items.length > 0) {
                if (empty) empty.classList.add('hidden');
                // أيقونات SVG مدمجة (بدل الإيموجي)
                // خريطة تحويل emoji → اسم أيقونة SVG
const emojiToIcon = {
    '📅': 'clipboard', '🗓️': 'clipboard', '📋': 'clipboard',
    '💰': 'receipt', '💵': 'receipt', '🧾': 'receipt', '💳': 'receipt',
    '🎓': 'award', '🏆': 'award', '🏅': 'award',
    '✅': 'check-circle', '✔️': 'check-circle',
    '⚠️': 'alert-circle', '❗': 'alert-circle',
    '🔔': 'bell', '📢': 'bell',
};
const iconSvg = {
                    bell: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
                    'check-circle': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
                    'alert-circle': '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>',
                    receipt: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1z"></path></svg>',
                    clipboard: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>',
                    award: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>',
                };

                list.innerHTML = data.items.map(i => {
                    // إذا كان i.icon اسم أيقونة معروف → استخدم SVG
                    // إذا كان emoji → استخدم span
                    let iconHtml = '';
                    // تحويل emoji → اسم أيقونة إن أمكن
                    let iconName = i.icon;
                    if (i.icon && emojiToIcon[i.icon]) {
                        iconName = emojiToIcon[i.icon];
                    }
                    if (iconName && iconSvg[iconName]) {
                        iconHtml = '<span class="shrink-0" style="color: var(--color-brand-600, #0d9488)">' + iconSvg[iconName] + '</span>';
                    } else if (i.icon && !/[a-zA-Z]/.test(i.icon)) {
                        iconHtml = '<span class="text-lg shrink-0">' + i.icon + '</span>';
                    }
                    return '<a href="' + (i.url || '#') + '" class="dropdown-item flex items-start gap-2 px-3 py-2.5 text-sm">'
                        + iconHtml + '<span class="flex-1">' + i.text + '</span></a>';
                }).join('');
            } else if (list) {
                if (empty) empty.classList.remove('hidden');
                list.innerHTML = '';
            }
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('notif-btn')) {
        fetchNotifs();
        setInterval(fetchNotifs, 60000);
    }
});

// ============================================================
// TOASTS
// ============================================================
document.querySelectorAll('.toast[data-auto-dismiss]').forEach(t => {
    const ms = parseInt(t.dataset.autoDismiss, 10) || 5000;
    setTimeout(() => {
        t.style.transition = 'all 0.3s ease';
        t.style.opacity = '0';
        t.style.transform = 'translateX(-20px)';
        setTimeout(() => t.remove(), 300);
    }, ms);
});
document.querySelectorAll('.toast-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const toast = btn.closest('.toast');
        if (!toast) return;
        toast.style.transition = 'all 0.3s ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    });
});
/* ============================================
   STUDENT REGISTRATION WIZARD
   ============================================ */
document.addEventListener('DOMContentLoaded', () => {
    const wizard = document.getElementById('registration-wizard');
    if (!wizard) return;

    const steps    = wizard.querySelectorAll('.wizard-step');
    const panels   = wizard.querySelectorAll('.wizard-panel');
    const prevBtn  = document.getElementById('wizard-prev');
    const nextBtn  = document.getElementById('wizard-next');
    const submitBtn= document.getElementById('wizard-submit');
    const form     = document.getElementById('registration-form');
    const total    = panels.length;
    let current    = 1;

    function showStep(step) {
        current = Math.min(Math.max(step, 1), total);

        // تحديث اللوحات
        panels.forEach(p => {
            p.classList.toggle('active', Number(p.dataset.panel) === current);
        });

        // تحديث شريط المراحل
        steps.forEach(s => {
            const n = Number(s.dataset.step);
            s.classList.toggle('active', n === current);
            s.classList.toggle('completed', n < current);
        });

        // الأزرار
        prevBtn.style.display   = current === 1 ? 'none' : 'inline-flex';
        nextBtn.style.display   = current === total ? 'none' : 'inline-flex';
        submitBtn.style.display = current === total ? 'inline-flex' : 'none';

        // تمرير لأعلى
        wizard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // التحقق من الحقول المطلوبة في المرحلة الحالية
    function validateStep(step) {
        const panel = wizard.querySelector(`.wizard-panel[data-panel="${step}"]`);
        const required = panel.querySelectorAll('[required]');
        let ok = true;
        required.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                field.addEventListener('input', () => field.classList.remove('border-red-500'), { once: true });
                ok = false;
            }
        });
        if (!ok) {
            alert('يرجى إكمال الحقول المطلوبة قبل المتابعة.');
        }
        return ok;
    }

    nextBtn.addEventListener('click', () => {
        if (validateStep(current)) showStep(current + 1);
    });
    prevBtn.addEventListener('click', () => showStep(current - 1));

    // النقر على المراحل
    steps.forEach(s => {
        s.addEventListener('click', () => {
            const target = Number(s.dataset.step);
            if (target < current || validateStep(current)) showStep(target);
        });
    });

    // تحقق نهائي قبل الإرسال
    form.addEventListener('submit', (e) => {
        for (let i = 1; i <= total; i++) {
            if (!validateStep(i)) {
                e.preventDefault();
                showStep(i);
                return;
            }
        }
    });

    showStep(1);
});
/* ============================================
   STUDENT REGISTRATION WIZARD
   ============================================ */
document.addEventListener('DOMContentLoaded', () => {
    const wizard = document.getElementById('registration-wizard');
    if (!wizard) return;

    const steps     = wizard.querySelectorAll('.wizard-step');
    const panels    = wizard.querySelectorAll('.wizard-panel');
    const prevBtn   = document.getElementById('wizard-prev');
    const nextBtn   = document.getElementById('wizard-next');
    const submitBtn = document.getElementById('wizard-submit');
    const form      = document.getElementById('registration-form');
    const total     = panels.length;
    let current     = 1;

    function showStep(step) {
        current = Math.min(Math.max(step, 1), total);
        panels.forEach(p => p.classList.toggle('active', Number(p.dataset.panel) === current));
        steps.forEach(s => {
            const n = Number(s.dataset.step);
            s.classList.toggle('active', n === current);
            s.classList.toggle('completed', n < current);
        });
        prevBtn.style.display   = current === 1 ? 'none' : 'inline-flex';
        nextBtn.style.display   = current === total ? 'none' : 'inline-flex';
        submitBtn.style.display = current === total ? 'inline-flex' : 'none';
        wizard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function validateStep(step) {
        const panel = wizard.querySelector('.wizard-panel[data-panel="' + step + '"]');
        const required = panel.querySelectorAll('[required]');
        let ok = true;
        required.forEach(field => {
            if (!field.value || !field.value.trim()) {
                field.classList.add('border-red-500');
                field.addEventListener('input', () => field.classList.remove('border-red-500'), { once: true });
                ok = false;
            }
        });
        if (!ok) alert('يرجى إكمال الحقول المطلوبة قبل المتابعة.');
        return ok;
    }

    nextBtn.addEventListener('click', () => { if (validateStep(current)) showStep(current + 1); });
    prevBtn.addEventListener('click', () => showStep(current - 1));

    steps.forEach(s => s.addEventListener('click', () => {
        const target = Number(s.dataset.step);
        if (target < current || validateStep(current)) showStep(target);
    }));

    form.addEventListener('submit', (e) => {
        for (let i = 1; i <= total; i++) {
            if (!validateStep(i)) { e.preventDefault(); showStep(i); return; }
        }
    });

    showStep(1);

    /* معاينة الصورة */
    const photoInput = document.getElementById('photo-input');
    const photoPreview = document.getElementById('photo-preview');
    const photoPlaceholder = document.getElementById('photo-placeholder');
    if (photoInput) {
        photoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    photoPreview.src = ev.target.result;
                    photoPreview.classList.remove('hidden');
                    if (photoPlaceholder) photoPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});