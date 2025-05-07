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

    /**
     * The name of the database table used to store migration information.
     *
     * This constant defines the table name where migration records are stored.
     * It is used by the framework to track executed migrations.
     */
    public const MIGRATIONS_TABLE_NAME = "migrations";

    /** Config Constants */
    // VIEW KEYS
    public const VIEW_ENGINE = "view.engine";
    public const VIEW_PATH = "view.path";
    public const DEFAULT_VIEW_ENGINE = "pickles";

    // SESSION STORAGE KEYS
    public const SESSION_STORAGE = "session.storage";
    public const DEFAULT_SESSION_STORAGE = "native";

    // DATABASE KEYS
    public const DATABASE_PROTOCOL = "database.connection";
    public const DATABASE_HOST = "database.host";
    public const DATABASE_PORT = "database.port";
    public const DATABASE_USERNAME = "database.username";
    public const DATABASE_PASSWORD = "database.password";
    public const DATABASE_DATABASE = "database.database";
    public const DEFAULT_DATABASE_PROTOCOL = "mysql";
    public const POSTGRES_DATABASE_PROTOCOL = "pgsql";
    public const MYSQL_DATABASE_PROTOCOL = "mysql";

    // PROVIDERS KEYS
    public const BOOT_PROVIDERS = "boot";
    public const RUNTIME_PROVIDERS = "runtime";
}
