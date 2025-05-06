<?php

namespace Pickles\Tests\Validation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pickles\Validation\Exceptions\RuleParseException;
use Pickles\Validation\Exceptions\UnknownRuleException;
use Pickles\Validation\Rule;
use Pickles\Validation\Rules\Email;
use Pickles\Validation\Rules\LessThan;
use Pickles\Validation\Rules\Number;
use Pickles\Validation\Rules\Required;
use Pickles\Validation\Rules\RequiredWhen;
use Pickles\Validation\Rules\RequiredWith;

class RuleParseTest extends TestCase
{
    protected function setUp(): void
    {
        Rule::loadDefaults();
    }

    public function getBasicRules()
    {
        return [
            [Email::class, "email"],
            [Required::class, "required"],
            [Number::class, "number"],
        ];
    }

    /** @dataProvider getBasicRules */
    public function test_parse_basic_rules($class, $name)
    {
        $this->assertInstanceOf($class, Rule::from($name));
    }

    public function test_parsing_unknown_rules_throws_unkown_rule_exception()
    {
        $this->expectException(UnknownRuleException::class);
        Rule::from("unknown");
    }

    public static function getRulesWithParameters()
    {
        return [
            [new LessThan(5), "less_than:5"],
            [new RequiredWith("other"), "required_with:other"],
            [new RequiredWhen("other", "=", "test"), "required_when:other,=,test"],
        ];
    }

    /** @dataProvider getRulesWithParameters */
    public function test_parse_rules_with_parameters($expected, $rule)
    {
        $this->assertEquals($expected, Rule::from($rule));
    }

    public static function getRulesWithParametersWithError()
    {
        return [
            ["less_than"],
            ["less_than:"],
            ["required_with:"],
            ["required_when"],
            ["required_when:"],
            ["required_when:other"],
            ["required_when:other,"],
            ["required_when:other,="],
            ["required_when:other,=,"],
        ];
    }

    /** @dataProvider getRulesWithParametersWithError */
    public function test_parsing_rule_with_parameters_without_passing_correct_parameters_throws_rule_parse_exception($rule)
    {
        $this->expectException(RuleParseException::class);
        Rule::from($rule);
    }
}
