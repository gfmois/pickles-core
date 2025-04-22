<?php

use Pickles\Session\Session;

/**
 * Retrieve the current session instance.
 *
 * This function provides access to the session instance managed by the application.
 *
 * @return Session The current session instance.
 */
function session(): Session
{
    return app()->getSession();
}

/**
 * Retrieves the first error message for a specific field from the session.
 *
 * This function accesses the session to fetch error messages stored under
 * a predefined key. If there are multiple error messages for the given field,
 * only the first one is returned. If no errors exist for the field, `null` is returned.
 *
 * @param string $field The name of the field for which to retrieve the error message.
 * @return string|null The first error message for the specified field, or `null` if no errors exist.
 */
function error(string $field)
{
    $errors = session()->get(Constants::ERRORS_KEY, [])[$field] ?? [];
    $keys = array_keys($errors);
    if (count($keys) > 0) {
        return $errors[$keys[0]];
    }

    return null;
}

/**
 * Retrieve old input data for a given field from the session.
 *
 * This function fetches the value of a specific field from the old input data
 * stored in the session. If the field does not exist, it returns null.
 *
 * @param string $field The name of the field to retrieve old input data for.
 * @return mixed|null The value of the old input data for the specified field, or null if not found.
 */
function old(string $field)
{
    return session()->get(Constants::OLD_DATA_KEY, [])[$field] ?? null;
}
