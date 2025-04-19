<?php

namespace Pickles\Validation;

use Pickles\Validation\Exceptions\RuleParseException;
use Pickles\Validation\Exceptions\UnknownRuleException;
use Pickles\Validation\Rules\Email;
use Pickles\Validation\Rules\LessThan;
use Pickles\Validation\Rules\Number;
use Pickles\Validation\Rules\Required;
use Pickles\Validation\Rules\RequiredWhen;
use Pickles\Validation\Rules\RequiredWith;
use Pickles\Validation\Rules\ValidationRule;
use ReflectionClass;

class Rule
{
    private static array $rules = [];
    private static array $defaultRules = [
        Email::class,
        LessThan::class,
        RequiredWhen::class,
        RequiredWith::class,
        Required::class,
        Number::class
    ];

    public static function loadDefaults(): void
    {
        self::load(self::$defaultRules);
    }
    public static function load(array $rules): void
    {
        foreach ($rules as $class) {
            $className = array_slice(explode("\\", $class), -1)[0];
            $rule_name = snake_case($className);

            self::$rules[$rule_name] = $class;
        }
    }

    public static function nameOf(ValidationRule $rule): string
    {
        $class = new ReflectionClass($rule);
        return snake_case($class->getShortName());
    }

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

    public static function parseBasicRule(string $ruleName): ValidationRule
    {
        $class = new ReflectionClass(self::$rules[$ruleName]);
        $nConstructorParams = count($class->getConstructor()?->getParameters() ?? []);

        if ($nConstructorParams > 0) {
            throw new RuleParseException("Rule {$ruleName} requires parameters, but none were provided. ({$nConstructorParams} expected)");
        }

        return $class->newInstance();
    }

    public static function parseRuleWithParams(string $ruleName, string $rawParams): ValidationRule
    {
        $class = new ReflectionClass(self::$rules[$ruleName]);
        $constructorParams = $class->getConstructor()?->getParameters() ?? [];
        $params = array_filter(explode(",", $rawParams), fn ($p) => !empty($p));

        if (count($params) != count($constructorParams)) {
            throw new RuleParseException(sprintf(
                "Rule %s requires %d parameters, but %d were provided. (Received: %s)",
                $ruleName,
                count($constructorParams),
                count($params),
                $rawParams
            ));
        }

        return $class->newInstance(...$params);
    }

    public static function from(string $validationStr): ValidationRule
    {
        if (empty($validationStr)) {
            throw new RuleParseException("Cannot parse empty rule.");
        }

        $ruleParts = explode(":", $validationStr);
        if (!array_key_exists($ruleParts[0], self::$rules)) {
            throw new UnknownRuleException("Rule {$ruleParts[0]} not found.");
        }

        if (count($ruleParts) == 1) {
            return self::parseBasicRule($ruleParts[0]);
        }

        [$ruleName, $params] = $ruleParts;
        return self::parseRuleWithParams($ruleName, $params);
    }
}
