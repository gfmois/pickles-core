<?php

/**
 * Class Constants
 *
 * This class defines a set of constant keys used throughout the framework
 * for managing session data, flash messages, error handling, and request data.
 */
class Constants
{
    /**
     * Key used to store flash messages in the session.
     * @var string
     */
    public const FLASH_KEY = "__flash__";

    /**
     * Key used to store old flash messages in the session.
     * @var string
     */
    public const FLASH_OLD_KEY = "__flash_old__";

    /**
     * Key used to store new flash messages in the session.
     * @var string
     */
    public const FLASH_NEW_KEY = "__flash_new__";

    /**
     * Key used to store validation or other errors in the session.
     * @var string
     */
    public const ERRORS_KEY = "__errors";

    /**
     * Key used to store data from the previous request.
     * @var string
     */
    public const PREVIOUS_REQUEST_KEY = "__previous_request";

    /**
     * Key used to store old input data from the previous request.
     * @var string
     */
    public const OLD_DATA_KEY = "__old_data";

    /**
     * Key used to store the framework's session ID.
     * @var string
     */
    public const FRAMEWORK_SESSION_ID_KEY = "__pickles_sid";

    public const MIGRATIONS_TABLE_NAME = "migrations";
}
