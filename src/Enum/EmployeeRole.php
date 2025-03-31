<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum EmployeeRole: string implements TranslatableInterface
{
    use TranslatableEnumTrait;

    case MNG = 'MNG'; // Manager
    case MEN = 'MEN'; // Mentor
    case AUD = 'AUD'; // Auditor
}
