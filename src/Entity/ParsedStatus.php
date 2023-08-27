<?php

declare(strict_types=1);

namespace App\Entity;

enum ParsedStatus: int
{
    case PENDING = 0;

    case ERROR = 1;

    case PARSED = 2;
}