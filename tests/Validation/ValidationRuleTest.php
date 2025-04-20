<?php

namespace Pickles\Tests\Validation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pickles\Validation\Exceptions\UnknownOperatorException;
use Pickles\Validation\Rule;
use Pickles\Validation\Rules\LessThan;
use Pickles\Validation\Rules\Number;
use Pickles\Validation\Rules\RequiredWhen;

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

    public static function getLessThanData()
    {
        return [
            [5, 5, false],
            [5, 6, false],
            [5, 3, true],
            [5, null, false],
            [5, "", false],
            [5, "test", false],
        ];
    }

    public static function getNumbers()
    {
        return [
            [0, true],
            [1, true],
            [1.5, true],
            [-1, true],
            [-1.5, true],
            ["0", true],
            ["1", true],
            ["1.5", true],
            ["-1", true],
            ["-1.5", true],
            ["test", false],
            ["1test", false],
            ["-5test", false],
            ["", false],
            [null, false],
        ];
    }

    public static function getRequiredWhenData()
    {
        return [
            ["other", "=", "value", ["other" => "value"], "test", false],
            ["other", "=", "value", ["other" => "value", "test" => 1], "test", true],
            ["other", "=", "value", ["other" => "not value"], "test", true],
            ["other", ">", 5, ["other" => 1], "test", true],
            ["other", ">", 5, ["other" => 6], "test", false],
            ["other", ">", 5, ["other" => 6, "test" => 1], "test", true],
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

    public function test_requiredWith()
    {
        $rule = Rule::requiredWith("other");
        $data = ["other" => "value", "test" => 1];
        $this->assertTrue($rule->validate("test", $data));

        $data = ["other" => "value"];
        $this->assertFalse($rule->validate("test", $data));
    }

    #[DataProvider("getLessThanData")]
    public function test_less_than($value, $check, $expected)
    {
        $rule = new LessThan($value);
        $data = ["test" => $check];
        $this->assertEquals($expected, $rule->validate("test", $data));
    }

    #[DataProvider("getNumbers")]
    public function test_number($n, $expected)
    {
        $rule = new Number();
        $data = ["test" => $n];
        $this->assertEquals($expected, $rule->validate("test", $data));
    }

    #[DataProvider("getRequiredWhenData")]
    public function test_required_when($other, $operator, $compareWith, $data, $field, $expected)
    {
        $rule = new RequiredWhen($other, $operator, $compareWith);
        $this->assertEquals($expected, $rule->validate($field, $data));
    }

    public function test_required_when_throws_parse_rule_exception_when_operator_is_invalid()
    {
        $rule = new RequiredWhen("other", "|||", "test");
        $data = ["other" => 5, "test" => 1];
        $this->expectException(UnknownOperatorException::class);
        $rule->validate("test", $data);
    }
}
