<?php
/**
 * PHP Model Buddy
 * MySQL PDO Connector
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

//Create a database connection
try {
    mb_debugMessage("Establishing database connection");
    $db = new PDO('mysql:host='.$DB_HOST.';dbname='.$DB_NAME.';charset=utf8', ''.$DB_USER.'', ''.$DB_PASS.'');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    die("Could not connect to the database.");
}

mb_debugMessage("Connected to " . $DB_HOST . " using " . MB_DB_ENGINE);
