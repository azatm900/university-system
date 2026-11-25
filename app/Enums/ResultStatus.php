<?php

namespace App\Enums;

enum ResultStatus: string
{
    case Passed = 'passed';
    case Failed = 'failed';
    case Incomplete = 'incomplete';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'ناجح',
            self::Failed => 'راسب',
            self::Incomplete => 'غير مكتمل',
        };
    }

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(
            static fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}