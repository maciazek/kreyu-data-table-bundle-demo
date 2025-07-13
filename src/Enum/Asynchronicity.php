<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum Asynchronicity: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case ASYNC = 'ASY';
    case SYNC = 'SYN';
}
