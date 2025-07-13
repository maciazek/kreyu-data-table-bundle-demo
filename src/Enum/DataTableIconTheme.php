<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum DataTableIconTheme: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case BOOTSTRAP_ICONS_WEBFONT = 'BIW';
    case TABLER_ICONS_WEBFONT = 'TIW';

    public function getPath(): string
    {
        return match ($this) {
            self::BOOTSTRAP_ICONS_WEBFONT => '@KreyuDataTable/themes/bootstrap_icons_webfont.html.twig',
            self::TABLER_ICONS_WEBFONT => '@KreyuDataTable/themes/tabler_icons_webfont.html.twig',
        };
    }
}
