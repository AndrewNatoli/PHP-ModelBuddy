<?php
/**
 * PHP Model Buddy
 * The Core!
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

abstract class ModelBuddyModel {

    /**
     * @var $class
     * This is the name of the model we're using
     */
    private $class;

    /**
     * @var $tableStructure
     * The structure for this model's table
     */
    private $tableStructure;

    /**
     * __construct
     * Get our table structure and populate the model's data if necessary
     * @param mixed $wc Our WHERE clause for the record to fetch. Blank if creating a new record.
     */
    function __construct($wc="") {
        global $db;
        //Get our table name using the name of the class that was called
        $this->class = str_replace("Model","",get_class($this));

        //Get the table structure
        //TODO: Cache this for later
        try {
            $stmt = $db->prepare("DESCRIBE " . $this->class);
            $stmt->execute();
            $this->tableStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            die("Failed to load structure for " . $this->class . " table.");
        }
        echo "<pre>";
        print_r($this->tableStructure);
    }

}