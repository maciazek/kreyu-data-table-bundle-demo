<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatableEnumTrait
{
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans(self::class.'::'.$this->name, [], 'enums', $locale);
    }
}
