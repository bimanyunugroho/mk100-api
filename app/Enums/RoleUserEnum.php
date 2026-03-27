<?php

namespace App\Enums;

enum RoleUserEnum: string
{
    case EMPLOYER = 'employer';
    case FREELANCER = 'freelancer';


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
            self::EMPLOYER => 'Employer',
            self::FREELANCER => 'Freelancer',
        };
    }
}
