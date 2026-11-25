<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingsService
{
    public const CACHE_KEY = 'settings.map';

    /** Defaults used when a key is absent from the DB (or DB unreachable). */
    protected array $defaults = [
        'app.name'         => 'نظام جامعة العلوم الصحية',
        'app.name_en'      => 'University of Health Sciences',
        'app.logo'         => null,
        'app.address'      => '',
        'app.phone'        => '',
        'app.email'        => '',

        'file_number.student_format' => '{YEAR}{PROGRAM_CODE}{SEQ:4}',
        'file_number.invoice_format' => 'INV-{YEAR}{SEMESTER_CODE}-{SEQ:5}',
        'file_number.receipt_format' => 'RCP-{YEAR}-{SEQ:6}',

        'gpa.scale'          => 'both',
        'gpa.max'            => 4.0,
        'gpa.passing_score'  => 50,
        'gpa.passing_gpa'    => 1.0,

        'academic.current_year'     => null,
        'academic.current_semester' => null,

        'finance.currency'        => 'SDG',
        'finance.currency_symbol' => 'ج.س',
    ];

    /** Per-request cache. */
    protected ?array $loaded = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $map = $this->all();
        if (array_key_exists($key, $map)) return $map[$key];
        return $default ?? ($this->defaults[$key] ?? null);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all())
            || array_key_exists($key, $this->defaults);
    }

    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $attrs = ['value' => $this->serialize($value, $type)];
        if (Schema::hasColumn('settings', 'type'))  $attrs['type']  = $type;
        if (Schema::hasColumn('settings', 'group')) $attrs['group'] = $group;

        Setting::updateOrCreate(['key' => $key], $attrs);
        $this->flush();
    }

    public function forget(string $key): void
    {
        Setting::where('key', $key)->delete();
        $this->flush();
    }

    public function all(): array
    {
        if ($this->loaded !== null) return $this->loaded;

        $map = Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                if (! Schema::hasTable('settings')) return [];

                return Setting::query()->get()
                    ->mapWithKeys(fn (Setting $s) => [
                        $s->key => $this->deserialize($s->value, $s->getAttribute('type') ?? 'string'),
                    ])
                    ->all();
            } catch (Throwable) {
                return [];
            }
        });

        return $this->loaded = array_merge($this->defaults, $map);
    }

    public function group(string $group): array
    {
        try {
            if (! Schema::hasTable('settings') || ! Schema::hasColumn('settings', 'group')) return [];
            return Setting::where('group', $group)->get()
                ->mapWithKeys(fn (Setting $s) => [
                    $s->key => $this->deserialize($s->value, $s->getAttribute('type') ?? 'string'),
                ])
                ->all();
        } catch (Throwable) { return []; }
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->loaded = null;
    }

    protected function serialize(mixed $value, string $type): ?string
    {
        return match ($type) {
            'json', 'array'   => $value === null ? null : json_encode($value, JSON_UNESCAPED_UNICODE),
            'boolean', 'bool' => $value ? '1' : '0',
            default           => $value === null ? null : (string) $value,
        };
    }

    protected function deserialize(?string $value, string $type): mixed
    {
        if ($value === null) return null;
        return match ($type) {
            'integer', 'int'                => (int) $value,
            'float', 'double', 'decimal'    => (float) $value,
            'boolean', 'bool'               => (bool) (int) $value,
            'json', 'array'                 => json_decode($value, true),
            default                         => $value,
        };
    }
}