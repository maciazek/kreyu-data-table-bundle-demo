<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum EmployeeStatus: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case ACTIVE = 'ACT';
    case INACTIVE = 'INA';
    case LONG_TERM_LEAVE = 'LTL';

    public function getContext(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::LONG_TERM_LEAVE => 'warning',
        };
    }

    public function getIcon(DataTableIconTheme $dataTableIconTheme): string
    {
        switch ($dataTableIconTheme) {
            case DataTableIconTheme::BOOTSTRAP_ICONS_WEBFONT:
            default:
                return match ($this) {
                    self::ACTIVE => 'check-circle-fill',
                    self::INACTIVE => 'x-square-fill',
                    self::LONG_TERM_LEAVE => 'exclamation-triangle-fill',
                };
                break;
            case DataTableIconTheme::TABLER_ICONS_WEBFONT:
                return match ($this) {
                    self::ACTIVE => 'circle-check-filled',
                    self::INACTIVE => 'square-x-filled',
                    self::LONG_TERM_LEAVE => 'alert-triangle-filled',
                };
                break;
        }
    }
}
