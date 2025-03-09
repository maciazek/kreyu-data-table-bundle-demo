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

    public function getIcon(DataTableIconTheme $dataTableIconTheme): string
    {
        switch ($dataTableIconTheme) {
            case DataTableIconTheme::BIW:
            default:
                return match ($this) {
                    self::ACT => 'check-circle-fill',
                    self::INA => 'x-square-fill',
                    self::LTL => 'exclamation-triangle-fill',
                };
                break;
            case DataTableIconTheme::TIW:
                return match ($this) {
                    self::ACT => 'circle-check-filled',
                    self::INA => 'square-x-filled',
                    self::LTL => 'alert-triangle-filled',
                };
                break;
        }

    }
}
