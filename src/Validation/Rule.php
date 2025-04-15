<?php

namespace Pickles\Validation;

use Pickles\Validation\Rules\Email;
use Pickles\Validation\Rules\Required;
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
}
