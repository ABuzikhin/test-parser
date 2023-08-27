<?php

declare(strict_types=1);

namespace App\Validator;

use App\Service\ParserProvider;
use App\Service\Parsers\SiteParserInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SupportedParsersValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ParserProvider $provider,
    )
    { }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SupportedParsers) {
            throw new UnexpectedTypeException($constraint, SupportedParsers::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $parser = $this->provider->getParserByUrl($value);

        if ($parser instanceof SiteParserInterface) {
            return;
        }

        $this->context->buildViolation($constraint->message)
                      ->addViolation();
    }
}