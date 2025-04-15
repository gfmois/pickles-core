<?php

namespace Pickles\Tests\Validation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pickles\Validation\Rule;

class ValidationRuleTest extends TestCase
{
    public static function getEmails(): array
    {
        return [
            ["test@example.com", true],
            ["user.name+tag+sorting@example.com", true],
            ["user@subdomain.example.com", true],
            ["invalid-email", false],
            ["@missingusername.com", false],
            ["username@.com", false],
            ["username@domain..com", false],
            ["username@domain.c", true],
        ];
    }

    public static function getRequiredData(): array
    {
        return [
            ["", false],
            [null, false],
            [5, true],
            ["test", true],
            [" ", false],
            [[], true],
            [false, true],
        ];
    }

    #[DataProvider("getEmails")]
    public function test_email($email, $expected)
    {
        $data = ["email" => $email];
        $rule = Rule::email();

        $this->assertEquals($expected, $rule->validate("email", $data));
    }

    #[DataProvider("getRequiredData")]
    public function test_required($value, $expected)
    {
        $data = ["test" => $value];
        $rule = Rule::required();

        $this->assertEquals($expected, $rule->validate("test", $data));
    }
}
