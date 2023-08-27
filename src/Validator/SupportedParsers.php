<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SupportedParsers extends Constraint
{
    public string $message = 'There is not parsers for this url.';
}