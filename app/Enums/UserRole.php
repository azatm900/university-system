<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Registrar = 'registrar';
    case Finance = 'finance';
    case Results = 'results';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'مدير النظام',
            self::Registrar => 'مسجل',
            self::Finance => 'مالية',
            self::Results => 'نتائج',
            self::Student => 'طالب',
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