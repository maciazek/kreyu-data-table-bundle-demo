<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum DataTableIconTheme: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case BIW = 'BIW'; // Bootstrap Icons (webfont)

    public function getPath(): string
    {
        return match ($this) {
            self::BIW => '@KreyuDataTable/themes/bootstrap_icons_webfont.html.twig',
        };
    }
}
