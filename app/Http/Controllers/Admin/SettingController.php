<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected SettingsService $settings,
        protected AuditService $audit,
    ) {}

    public function index(): View
    {
        $settings = $this->settings->all();

        $groups = [];
        foreach ($settings as $key => $value) {
            $group = explode('.', $key, 2)[0] ?: 'general';
            $groups[$group][$key] = $value;
        }
        ksort($groups);

        return view('admin.settings.index', compact('groups'));
    }

    public function edit(string $key): View
    {
        $value = $this->settings->get($key);
        $type  = 'string';
        return view('admin.settings.edit', compact('key', 'value', 'type'));
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        // السماح برفع ملف إذا كان المفتاح هو app.logo
        $rules = [
            'value' => $key === 'app.logo' ? ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'] : ['nullable'],
            'type'  => ['nullable', 'in:string,integer,float,boolean,json'],
            'group' => ['nullable', 'string', 'max:50'],
        ];

        $data = $request->validate($rules);
        $old = $this->settings->get($key);
        $newValue = $data['value'] ?? null;

        // معالجة رفع ملف الشعار
        if ($key === 'app.logo' && $request->hasFile('value')) {
            $file = $request->file('value');
            $path = $file->store('logos', 'public');
            $newValue = $path;
        }

        $this->settings->set(
            $key,
            $newValue,
            $data['type']  ?? 'string',
            $data['group'] ?? 'general',
        );

        $this->audit->log(
            action: 'setting.updated',
            oldValues: ['value' => $old],
            newValues: ['value' => $newValue],
            description: "تم تحديث الإعداد [{$key}]",
        );

        return redirect()->route('admin.settings.index')
            ->with('success', "تم تحديث الإعداد [{$key}] بنجاح.");
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'settings'   => ['required', 'array', 'min:1'],
            'settings.*' => ['nullable', 'string', 'max:5000'],
        ]);

        $old = [];
        $new = [];
        foreach ($data['settings'] as $key => $value) {
            $old[$key] = $this->settings->get($key);
            $this->settings->set($key, $value);
            $new[$key] = $value;
        }

        $this->audit->log(
            action: 'settings.bulk_updated',
            oldValues: $old,
            newValues: $new,
            description: count($data['settings']) . ' إعدادات تم تحديثها',
        );

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم تحديث الإعدادات بنجاح.');
    }

    public function forget(string $key): RedirectResponse
    {
        $old = $this->settings->get($key);
        $this->settings->forget($key);

        $this->audit->log(
            action: 'setting.forgotten',
            oldValues: ['value' => $old],
            newValues: [],
            description: "تم حذف الإعداد [{$key}]، سيتم استخدام الافتراضي.",
        );

        return redirect()->route('admin.settings.index')
            ->with('success', "تم حذف الإعداد [{$key}]، سيتم استخدام الافتراضي.");
    }
}