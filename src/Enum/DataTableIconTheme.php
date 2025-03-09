<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum DataTableIconTheme: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case BIW = 'BIW'; // Bootstrap Icons (webfont)
    case TIW = 'TIW'; // Tabler Icons (webfont)

    public function getPath(): string
    {
        return match ($this) {
            self::BIW => '@KreyuDataTable/themes/bootstrap_icons_webfont.html.twig',
            self::TIW => '@KreyuDataTable/themes/tabler_icons_webfont.html.twig',
        };
    }
}
