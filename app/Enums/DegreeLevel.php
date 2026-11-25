<?php

namespace App\Enums;

enum DegreeLevel: string
{
    case Diploma = 'diploma';
    case Bachelor = 'bachelor';
    case Master = 'master';
    case Phd = 'phd';

    public function label(): string
    {
        return match ($this) {
            self::Diploma => 'دبلوم',
            self::Bachelor => 'بكالوريوس',
            self::Master => 'ماجستير',
            self::Phd => 'دكتوراه',
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