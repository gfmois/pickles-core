<?php

namespace Pickles\Validation\Rules;

class Email implements ValidationRule
{
    public function message(): string
    {
        return "The field must be a valid email address.";
    }

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

    private function hasCorrectParts(string $separator, string $value, int $length = 2): array
    {
        $split = explode($separator, $value);
        return [count($split) >= $length, $split];
    }
}
