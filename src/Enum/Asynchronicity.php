<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum Asynchronicity: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case ASY = 'ASY'; // Async
    case SYN = 'SYN'; // Sync
}
