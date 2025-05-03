<?php

namespace Pickles\Validation\Rules;

/**
 * Class Email
 *
 * This class implements the ValidationRule interface to provide
 * validation logic for email addresses.
 *
 * @package Validation\Rules
 */
class Email implements ValidationRule
{
    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return "The field must be a valid email address.";
    }


    /**
     * Validates an email address based on its structure.
     *
     * This method checks if the provided email address exists in the given data array
     * and validates its format by ensuring it has the correct parts (username, domain,
     * and top-level domain). The email is first trimmed and converted to lowercase
     * before validation.
     *
     * @param string $field The key in the data array that contains the email address.
     * @param array $data The array of data containing the email address to validate.
     *
     * @return bool Returns true if the email address is valid, otherwise false.
     */
    public function validate(string $field, array $data): bool
    {
        $email = $data[$field] ?? null;
        if ($email === null || $email === '') {
            return false;
        }

        $email = strtolower(trim($email));
        [$emailHasCorrectParts, $emailParts] = $this->hasCorrectParts('@', $email);
        if (!$emailHasCorrectParts) {
            return false;
        }

        [$username, $domain] = $emailParts;
        [$domainHasCorrectParts, $domainParts] = $this->hasCorrectParts('.', $domain);
        if (!$domainHasCorrectParts) {
            return false;
        }

        [$label, $topLevelDomain] = $domainParts;
        return strlen($username) >= 1 && strlen($label) >= 1 && strlen($topLevelDomain) >= 1;
    }


    /**
     * Splits a string by a specified separator and checks if the resulting parts meet the required count.
     *
     * @param string $separator The character used to split the string.
     * @param string $value The string to be split.
     * @param int $length The minimum number of parts required after splitting. Default is 2.
     *
     * @return array Returns an array where the first element is a boolean indicating
     *               whether the number of parts is greater than or equal to the required length,
     *               and the second element is the array of split parts.
     */
    private function hasCorrectParts(string $separator, string $value, int $length = 2): array
    {
        $split = explode($separator, $value);
        return [count($split) >= $length, $split];
    }
}
