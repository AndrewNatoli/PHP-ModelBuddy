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
    protected $mb_class;

    /**
     * @var $primary_key;
     * Primary key for the table
     */
    protected  $mb_primary_key;

    /**
     * @var $tableStructure
     * The structure for this model's table
     */
    protected  $mb_tableStructure;

    /**
     * @var $wc
     * The value the user submited for the where-clause
     */
    private $wc;

    /**
     * @var $wc_type
     * The type of WHERE clause we were given for our model.
     * Should be set with one of the wc_use[key|custom|array] constants
     */

    /*
     *  Constants for determining how our where-clauses should work
     */
    const   wc_use_key   =   0;
    const   wc_use_custom=   1;
    const   wc_use_array =   2;

    /**
     * __construct
     * Get our table structure and populate the model's data if necessary
     * @param mixed $wc Our WHERE clause for the record to fetch. Blank if creating a new record.
     */
    function __construct($wc="") {
        global $db;
        //Get our table name using the name of the class that was called
        $this->mb_class = str_replace("Model","",get_class($this));

        /*
         * Load the table structure
         */
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
         * Determine how we are going to search for our data
         */
        if(is_array($wc))
            $this->wc_type = ModelBuddyModel::wc_use_array;     //Use an array for the where-clause. Match keys to values
        elseif(strstr($wc," "))
            $this->wc_type = ModelBuddyModel::wc_use_custom;    //Use a custom, hand-crafted where clause
        else
            $this->wc_type = ModelBuddyModel::wc_use_key;       //Use the table's primary key with a single value

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
         * Use either the defaults if no wc was specified OR fetch the model if there is a wc
         */
        if($wc == "") {
            //Blank model? Use defaults...
            $this->mb_setDefaults();
        }
        else {
            $this->mb_fetchModel($wc);
        }
    }

    /**
     * mb_setDefaults()
     * Populate this instance with default values from the table
     */
    private function mb_setDefaults() {
        global $db;
        foreach($this->mb_tableStructure as $field) {
            $this->$field['Field'] = $field['Default'];
        }
    }

    /**
     * mb_fetchModel
     * Populate the object with the database row
     * @param mixed $wc Where-clause. Can be a single word, manually written WC or array of keys to match
     */
    private function mb_fetchModel($wc) {
        global $db;
        switch($this->wc_type) {
            /*
             * Search by the primary key
             */
            case ModelBuddyModel::wc_use_key:       //Search by key
                mb_debugMessage("Searching for " . $this->mb_class . " record by primary key");

                break;

            /*
             * Search by using an array
             */
            case ModelBuddyModel::wc_use_array:
                mb_debugMessage("Searching for " . $this->mb_class . " record by array");
                $query = "SELECT * FROM {$this->mb_class} WHERE ";
                foreach($wc as $key=>$value){
                    $query .= "{$key}=? AND ";
                    $values[] = $value;
                }
                $query = substr($query, 0, -4); //Knock out the trailing "AND"
                mb_debugMessage("Query: " . $query);
                mb_debugMessage($values);
                try {
                    $stmt = $db->prepare($query);
                    $stmt->execute($values);
                }
                catch(PDOException $e) {
                    echo "Failed to fetch " . $this->mb_class . " model.<br/><br/>" . $e;
                }
                break;

            /*
             * Search using a custom wc
             */
            case ModelBuddyModel::wc_use_custom:
                mb_debugMessage("Searching for " . $this->mb_class . " record using a custom WC");
                break;

        }

        /*
         * Did we find anything?
         * If we did, populate the object with the values and save the wc to use later.
         * If we didn't find anything, make a default object and trash the wc.
         */
        try {
            if($stmt->rowCount() == 0) {
                mb_debugMessage("No records found. Using defaults.");
                $this->mb_setDefaults();
                $this->wc = "";
            }
            else {
                mb_debugMessage("Record found.");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                foreach($row as $key=>$value) {
                    $this->$key = $value;
                }
                $this->wc = $wc;
            }
        }
        catch(PDOException $e) {
            echo "no";
        }

    }

}