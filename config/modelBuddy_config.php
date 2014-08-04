<?php
/**
 * PHP Model Buddy
 * Configuration file
 * Database configuration is first... then some MB-specific settings are after.
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

/*
 * MySQL Database Configuration
 */

/**
 * Database host
 * Default: localhost
 */
$DB_HOST            = "localhost";

/**
 * Database port
 * Usually 3306 in standard environments
 */
$DB_PORT            = 8889;

/**
 * MySQL username
 */
$DB_USER            = "root";

/**
 * MySQL user's password
 */
$DB_PASS            = "root";

/**
 * Which database to use
 */
$DB_NAME            = "modelBuddy";

/*
 * Model Buddy configuration values
 */

/**
 * Location of the models directory
 * This must be relative to the /lib/modelBuddy/ directory.
 * @default: ././models
 */
define("MB_MODEL_DIR","././models");

/**
 * MB_DEBUG
 * Print debug messages during execution.
 * Set this to "false" in production environments!
 */
define("MB_DEBUG",true);

/**
 * MB_DB_ENGINE
 * Which database driver ModelBuddy should use.
 * Only mysql_pdo is supported at the time. Additional support will require re-working the base ModelBuddyModel
 */
define("MB_DB_ENGINE","mysql_pdo");