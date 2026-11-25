<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Active = 'active';
    case Graduated = 'graduated';
    case Suspended = 'suspended';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'نشط',
            self::Graduated => 'متخرج',
            self::Suspended => 'موقوف',
            self::Withdrawn => 'منسحب',
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