<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Card = 'card';
    case Check = 'check';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'نقدي',
            self::BankTransfer => 'حوالة بنكية',
            self::Card => 'بطاقة',
            self::Check => 'شيك',
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