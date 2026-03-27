<?php

namespace App\Enums;

enum TypeJobEnum: string
{
    case FREELANCER = 'freelancer';
    case PARTTIME = 'parttime';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::FREELANCER => 'Freelancer',
            self::PARTTIME => 'Parttime',
        };
    }
}
