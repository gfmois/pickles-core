<?php

namespace Pickles\Tests\Validation;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use Pickles\Validation\Exceptions\ValidationException;
use Pickles\Validation\Rules\Email;
use Pickles\Validation\Rules\LessThan;
use Pickles\Validation\Rules\Number;
use Pickles\Validation\Rules\Required;
use Pickles\Validation\Rules\RequiredWith;
use Pickles\Validation\Validator;

class ValidatorTest extends TestCase
{
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
}
