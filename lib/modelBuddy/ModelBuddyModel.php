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
    private $mb_class;

    /**
     * @var $primary_key;
     * Primary key for the table
     */
    private $mb_primary_key;

    /**
     * @var $tableStructure
     * The structure for this model's table
     */
    private $mb_tableStructure;

    /**
     * __construct
     * Get our table structure and populate the model's data if necessary
     * @param mixed $wc Our WHERE clause for the record to fetch. Blank if creating a new record.
     */
    function __construct($wc="") {
        global $db;
        //Get our table name using the name of the class that was called
        $this->mb_class = str_replace("Model","",get_class($this));

        //Get the table structure
        //TODO: Cache this for later
        try {
            $stmt = $db->prepare("DESCRIBE " . $this->mb_class);
            $stmt->execute();
            $this->mb_tableStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            die("Failed to load structure for " . $this->mb_class . " table.");
        }

        /*
         * Find our primary key
         */
        foreach($this->mb_tableStructure as $field) {
            if($field['Key'] == "PRI") {
                $this->mb_primary_key = $field['Field'];
                break;
            }
        }

        /*
         * Populate some data
         */
        if($wc == "") {
            //Blank model? Use defaults...
            foreach($this->mb_tableStructure as $field) {
                $this->$field['Field'] = $field['Default'];
            }

            echo "<pre>";
            print_r($this);
        }
        else {

        }
    }

}