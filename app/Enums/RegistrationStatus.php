<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Registered = 'registered';
    case Dropped = 'dropped';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'مسجل',
            self::Dropped => 'مسحوب',
            self::Completed => 'مكتمل',
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