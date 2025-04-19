<?php

/**
 * Converts a given string to snake_case.
 *
 * This function transforms a camelCase or PascalCase string into snake_case
 * by inserting underscores before uppercase letters and converting all characters to lowercase.
 *
 * @param string $str The input string to be converted.
 * @return string The converted string in snake_case format.
 *
 */
function snake_case(string $str): string
{
    $snake_cased = [];
    $skip = str_split(" -_/\\|.,;:!@#$%^&*(){}[]<>?~`");

    $i = 0;

    while ($i < strlen($str)) {
        $lastChar = count($snake_cased) > 0 ? $snake_cased[count($snake_cased) - 1] : null;
        $char = $str[$i++];
        if (ctype_upper($char)) {
            if ($lastChar != "_") {
                $snake_cased[] = "_";
            }
            $snake_cased[] = strtolower($char);
        } elseif (ctype_lower($char)) {
            $snake_cased[] = $char;
        } elseif (in_array($char, $skip)) {
            if ($lastChar != "_") {
                $snake_cased[] = "_";
            }
            while ($i < strlen($str) && in_array($str[$i], $skip)) {
                $i++;
            }
        }
    }

    if ($snake_cased[0] == "_") {
        array_shift($snake_cased);
    }
    if ($snake_cased[count($snake_cased) - 1] == "_") {
        array_pop($snake_cased);
    }

    return implode($snake_cased);
}
