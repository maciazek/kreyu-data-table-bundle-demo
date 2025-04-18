<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum DataTableTheme: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case B5S = 'B5S'; // Bootstrap 5
    case B5C = 'B5C'; // Bootstrap 5 (custom)
    case TAB = 'TAB'; // Tabler

    public function getPath(): string
    {
        return match ($this) {
            self::B5S => '@KreyuDataTable/themes/bootstrap_5.html.twig',
            self::B5C => 'themes/data_table_bootstrap_5.html.twig',
            self::TAB => '@KreyuDataTable/themes/tabler.html.twig',
        };
    }
}
