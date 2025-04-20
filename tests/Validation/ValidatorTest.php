<?php

namespace Pickles\Tests\Validation;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use Pickles\Validation\Exceptions\ValidationException;
use Pickles\Validation\Rule;
use Pickles\Validation\Rules\Email;
use Pickles\Validation\Rules\LessThan;
use Pickles\Validation\Rules\Number;
use Pickles\Validation\Rules\Required;
use Pickles\Validation\Rules\RequiredWith;
use Pickles\Validation\Validator;

class ValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        Rule::loadDefaults();
    }

    public function test_basic_validation_passes()
    {
        $data = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
            "foo" => 5,
            "bar" => 4
        ];

        $rules = [
            "email" => new Email(),
            "other" => new Required(),
            "num" => new Number(),
        ];

        $expected = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
        ];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function test_throws_validation_exception_on_invalid_data()
    {
        $this->expectException(ValidationException::class);
        $v = new Validator(["test" => "test"]);
        $v->validate(["test" => new Number()]);
    }

    #[Depends("test_basic_validation_passes")]
    public function test_multiple_rules_validation()
    {
        $data = ["age" => 20, "num" => 3, "foo" => 5];

        $rules = [
            "age" => new LessThan(100),
            "num" => [new RequiredWith("age"), new Number()],
        ];

        $expected = ["age" => 20, "num" => 3];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function test_overrides_error_messages_correctly()
    {
        $data = [
            "email" => "test@",
            "num" => "asdf"
        ];

        $rules = [
            "email" => "email",
            "num" => "number",
            "num2" => ["required", "number"]
        ];

        $messages = [
            "email" => [
                "email" => "testing email message",
            ],
            "num" => [
                "number" => "testing number message",
            ],
            "num2" => [
                "required" => "testing required message",
                "number" => "testing number message on num2",
            ]
        ];

        $v = new Validator($data);

        try {
            $v->validate($rules, $messages);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $this->assertEquals($messages, $e->getErrors());
        }
    }

    public function test_basic_validation_passes_with_strings()
    {
        $data = [
            "email" => "example@example.com",
            "age" => "25",
            "name" => "John Doe"
        ];

        $rules = [
            "email" => "email",
            "age" => "number",
            "name" => "required"
        ];

        $expected = [
            "email" => "example@example.com",
            "age" => 25,
            "name" => "John Doe"
        ];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function test_returns_messages_for_each_rule_that_doesnt_pass()
    {
        $data = [
            "email" => "invalid-email",
            "age" => "not-a-number",
            "name" => ""
        ];

        $rules = [
            "email" => "email",
            "age" => "number",
            "name" => "required"
        ];

        $messages = [
            "email" => [
                "email" => "testing email message",
            ],
            "age" => [
                "number" => "testing number message",
            ],
            "name" => [
                "required" => "testing required message",
            ]
        ];

        $v = new Validator($data,);

        try {
            $v->validate($rules, $messages);
            $this->fail("Expected ValidationException not thrown");
        } catch (ValidationException $e) {
            $expectedErrors = [
                "email" => [
                    "email" => "testing email message"
                ],
                "age" => [
                    "number" => "testing number message"
                ],
                "name" => [
                    "required" => "testing required message"
                ]
            ];

            $this->assertEquals($expectedErrors, $e->getErrors());
        }
    }
}
