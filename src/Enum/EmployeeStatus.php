<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum EmployeeStatus: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case ACT = 'ACT'; // Active
    case INA = 'INA'; // Inactive
    case LTL = 'LTL'; // Long term leave

    public function getContext(): string
    {
        return match ($this) {
            self::ACT => 'success',
            self::INA => 'danger',
            self::LTL => 'warning',
        };
    }
}
