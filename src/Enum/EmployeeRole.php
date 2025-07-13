<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum EmployeeRole: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case MANAGER = 'MNG';
    case MENTOR = 'MEN';
    case AUDITOR = 'AUD';
}
