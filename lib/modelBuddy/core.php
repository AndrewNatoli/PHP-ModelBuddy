<?php
/**
 * PHP Model Buddy
 * The Core!
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

//Load our extra functions
require_once("functions.php");

//Include the database driver and establish a connection
switch(MB_DB_ENGINE) {
    case "mysql_pdo":
        //Make sure the file exists
        if(file_exists(__DIR__."/db/".MB_DB_ENGINE.".php"))
            require_once(__DIR__."/db/".MB_DB_ENGINE.".php");
        else
            die("Missing database driver file. (".MB_DB_ENGINE.".php)"); //Not found? Error.
        break;
    default:
        die("Invalid database engine specified.");
        break;
}

/**
 * $mb_table_cache array
 * Stores table structures for our models
 */
$mb_table_cache = array();

//Load our base model
require_once("ModelBuddyModel.php");

/*
 * Should we auto load the models?
 */
if(MB_AUTOLOAD_MODELS) {
    mb_autoload_models();
}