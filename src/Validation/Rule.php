<?php

namespace Pickles\Validation;

use Pickles\Validation\Rules\Email;
use Pickles\Validation\Rules\LessThan;
use Pickles\Validation\Rules\Number;
use Pickles\Validation\Rules\Required;
use Pickles\Validation\Rules\RequiredWhen;
use Pickles\Validation\Rules\RequiredWith;

class Rule
{
    public static function email(): Email
    {
        return new Email();
    }

    public static function required(): Required
    {
        return new Required();
    }

    public static function requiredWith(string $withField): RequiredWith
    {
        return new RequiredWith($withField);
    }

    public static function number(): Number
    {
        return new Number();
    }

    public static function requiredWhen(
        string $otherField,
        string $operator,
        string $compareWith
    ): RequiredWhen {
        return new RequiredWhen($otherField, $operator, $compareWith);
    }

    public static function lessThan(int|float $number): LessThan
    {
        return new LessThan($number);
    }
}
