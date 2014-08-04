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

if(!file_exists(MB_MODEL_DIR))
    die("Could not find models directory!");
else {
    /* TODO: Need a way to make sure these files which are being auto-loaded couldn't have been injected by an attacker */
    $mb_modelFiles = scandir(MB_MODEL_DIR);

    //Sanitize the array by removing any non PHP file indexes
    for($i=0; $i<=count($mb_modelFiles);$i++) {
        if(!strstr($mb_modelFiles[$i],"php"))
            unset($mb_modelFiles[$i]);
    }

    //Now load them in.
    foreach($mb_modelFiles as $mb_modelFilename) {
        $mb_modelFile = MB_MODEL_DIR . "/" . $mb_modelFilename;
        mb_debugMessage("Loading model: " . $mb_modelFile);
        require_once($mb_modelFile);
    }
}