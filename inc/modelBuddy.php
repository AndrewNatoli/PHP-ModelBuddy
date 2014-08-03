<?php
/**
 * PHP Model Buddy
 * Include File
 * Loads the configuration and library files needed to use Model Buddy
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

//Load the database configuration
require_once("./config/modelBuddy_config.php");

//Load the Model Buddy Core. This will load any additional files needed to run.
require_once("./lib/modelBuddy/core.php");