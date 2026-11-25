<?php

namespace App\Enums;

enum CourseType: string
{
    case Compulsory = 'compulsory';
    case Elective = 'elective';

    public function label(): string
    {
        return match ($this) {
            self::Compulsory => 'إجباري',
            self::Elective => 'اختياري',
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