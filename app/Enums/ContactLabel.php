<?php

declare(strict_types=1);

namespace App\Enums;

enum ContactLabel: string
{
    case Personal = 'personal';
    case Work = 'work';
    case Other = 'other';
}
