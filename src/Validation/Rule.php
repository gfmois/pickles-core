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
use ReflectionException;

/**
 * Represents a validation rule within the Pickles Framework.
 * This class is intended to define and handle specific validation logic.
 */
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

    /**
     * Loads the default validation rules.
     *
     * This method initializes the validation system by loading
     * a predefined set of default rules stored in the `$defaultRules` property.
     *
     * @return void
     */
    public static function loadDefaults(): void
    {
        self::load(self::$defaultRules);
    }

    /**
     * Loads an array of rule classes into the static rules property.
     *
     * @param array $rules An array of fully qualified class names representing the rules.
     *                      Each class name will be converted to snake_case and used as the key
     *                      in the static rules property, with the class name as the value.
     *
     * @return void
     */
    public static function load(array $rules): void
    {
        // TODO: Check if the classes are valid and implement the interface ValidationRule
        foreach ($rules as $class) {
            $className = array_slice(explode("\\", $class), -1)[0];
            $rule_name = snake_case($className);

            self::$rules[$rule_name] = $class;
        }
    }

    /**
     * Converts the class name of a given ValidationRule instance into a snake_case string.
     *
     * @param ValidationRule $rule The validation rule instance whose class name will be converted.
     * @return string The snake_case representation of the class name.
     * @throws ReflectionException If the class does not exist or cannot be reflected.
     */
    public static function nameOf(ValidationRule $rule): string
    {
        $class = new ReflectionClass($rule);
        return snake_case($class->getShortName());
    }

    /**
     * Creates and returns a new instance of the Email validation rule.
     *
     * @return Email An instance of the Email validation rule.
     */
    public static function email(): Email
    {
        return new Email();
    }

    /**
     * Creates and returns a new instance of the Required validation rule.
     *
     * @return Required An instance of the Required validation rule.
     */
    public static function required(): Required
    {
        return new Required();
    }

    /**
     * Creates a new instance of the RequiredWith validation rule.
     *
     * This rule ensures that the current field is required if the specified
     * related field is present and not empty.
     *
     * @param string $withField The name of the related field that determines
     *                          whether the current field is required.
     * @return RequiredWith An instance of the RequiredWith validation rule.
     */
    public static function requiredWith(string $withField): RequiredWith
    {
        return new RequiredWith($withField);
    }

    /**
     * Creates and returns a new instance of the Number validation rule.
     *
     * @return Number An instance of the Number validation rule.
     */
    public static function number(): Number
    {
        return new Number();
    }

    /**
     * Creates a new instance of the RequiredWhen validation rule.
     *
     * This rule specifies that a field is required only when another field
     * satisfies a given condition based on the provided operator and value.
     *
     * @param string $otherField The name of the other field to compare against.
     * @param string $operator The operator to use for comparison (e.g., '=', '!=', '>', '<').
     * @param string $compareWith The value to compare the other field with.
     *
     * @return RequiredWhen An instance of the RequiredWhen validation rule.
     */
    public static function requiredWhen(
        string $otherField,
        string $operator,
        string $compareWith
    ): RequiredWhen {
        return new RequiredWhen($otherField, $operator, $compareWith);
    }

    /**
     * Creates a new LessThan validation rule.
     *
     * This method generates a validation rule that checks if a given value
     * is less than the specified number.
     *
     * @param int|float $number The number to compare against.
     * @return LessThan An instance of the LessThan validation rule.
     */
    public static function lessThan(int|float $number): LessThan
    {
        return new LessThan($number);
    }

    /**
     * Parses a basic validation rule by its name and returns an instance of the rule.
     *
     * This method retrieves the rule class from the static `$rules` array using the provided
     * rule name, checks if the rule's constructor requires parameters, and throws an exception
     * if parameters are required but not provided. If no parameters are required, it creates
     * and returns a new instance of the rule.
     *
     * @param string $ruleName The name of the validation rule to parse.
     *
     * @throws RuleParseException If the rule requires parameters but none are provided.
     * @throws ReflectionException If the rule class does not exist or cannot be reflected.
     *
     * @return ValidationRule An instance of the validation rule.
     */
    public static function parseBasicRule(string $ruleName): ValidationRule
    {
        $class = new ReflectionClass(self::$rules[$ruleName]);
        $nConstructorParams = count($class->getConstructor()?->getParameters() ?? []);

        if ($nConstructorParams > 0) {
            throw new RuleParseException("Rule {$ruleName} requires parameters, but none were provided. ({$nConstructorParams} expected)");
        }

        return $class->newInstance();
    }

    /**
     * Parses a rule name and its raw parameters to create a ValidationRule instance.
     *
     * @param string $ruleName The name of the validation rule to parse.
     * @param string $rawParams A comma-separated string of parameters for the rule.
     *
     * @throws RuleParseException If the number of provided parameters does not match
     *                            the number of parameters required by the rule's constructor.
     *
     * @return ValidationRule An instance of the validation rule with the provided parameters.
     */
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

    /**
     * Parses a validation rule string and returns a ValidationRule object.
     *
     * @param string $validationStr The validation rule string to parse.
     *                              It should be in the format "ruleName:param1,param2,...".
     *
     * @return ValidationRule The parsed ValidationRule object.
     *
     * @throws RuleParseException If the provided validation string is empty.
     * @throws UnknownRuleException If the rule name in the validation string is not recognized.
     */
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
