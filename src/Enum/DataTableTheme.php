<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum DataTableTheme: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case BOOTSTRAP_5 = 'B5S';
    case BOOTSTRAP_5_CUSTOM = 'B5C';
    case TABLER = 'TAB';

    public function getPath(): string
    {
        return match ($this) {
            self::BOOTSTRAP_5 => '@KreyuDataTable/themes/bootstrap_5.html.twig',
            self::BOOTSTRAP_5_CUSTOM => 'themes/data_table_bootstrap_5.html.twig',
            self::TABLER => '@KreyuDataTable/themes/tabler.html.twig',
        };
    }
}
