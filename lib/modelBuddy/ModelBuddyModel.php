<?php
/**
 * PHP Model Buddy
 * Base model
 * Contains all the main logic for fetching generic models, manipulating and updating them.
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

abstract class ModelBuddyModel {

    /**
     * @var $mb_class
     * This is the name of the model we're using
     */
    protected $mb_class;

    /**
     * @var $mb_primary_key;
     * Primary key for the table
     */
    protected  $mb_primary_key;

    /**
     * @var $mb_tableStructure
     * The structure for this model's table
     */
    protected  $mb_tableStructure;

    /**
     * @var $mb_wc
     * The value the user submited for the where-clause
     */
    private $mb_wc;

    /**
     * @var $mb_custom_wc_values
     * Array of values if we're using a custom WC
     */
    private $mb_custom_wc_values;

    /**
     * @var $wc_type
     * The type of WHERE clause we were given for our model.
     * Should be set with one of the wc_use[key|custom|array] constants
     */
    private $wc_type;

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
     * @param mixed $custom_wc_values Array of values if we're using a custom WC.
     */
    function __construct($wc="",$custom_wc_values="") {
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
        elseif(strstr($wc," ") || strstr($wc,"="))
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
         * Use either the defaults if no mb_wc was specified OR fetch the model if there is a mb_wc
         */
        if($wc == "") {
            //Blank model? Use defaults...
            $this->mb_setDefaults();
        }
        else {
            $this->mb_fetchModel($wc,$custom_wc_values);
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
     * @param mixed $custom_wc_values Array of values to be thrown into the mb_wc
     */
    private function mb_fetchModel($wc,$custom_wc_values) {
        global $db;
        switch($this->wc_type) {
            /*
             * Search by the primary key
             */
            case ModelBuddyModel::wc_use_key:       //Search by key
                mb_debugMessage("Searching for " . $this->mb_class . " record by primary key");
                $query = "SELECT * FROM {$this->mb_class} WHERE {$this->mb_primary_key}=?";
                try {
                    mb_debugMessage($query);
                    $stmt = $db->prepare($query);
                    $stmt->execute(array("{$wc}"));
                }
                catch(PDOException $e){
                    echo "Failed to fetch " . $this->mb_class . " model.<br/><br/>" . $e;
                }
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
             * Search using a custom mb_wc
             */
            case ModelBuddyModel::wc_use_custom:
                mb_debugMessage("Searching for " . $this->mb_class . " record using a custom WC");
                $query = "SELECT * FROM {$this->mb_class} WHERE ";

                //Make sure they didn't enter a WHERE in the beginning...
                if(substr($wc,0,6) == "WHERE ")
                    $wc = substr($wc,6);

                //Make sure we have the mb_wc values set
                if(!is_array($custom_wc_values))
                    die("You must supply an array of values for the where clause");
                $query .= $wc;
                try {
                    mb_debugMessage($query);
                    $stmt = $db->prepare($query);
                    $stmt->execute($custom_wc_values);
                }
                catch(PDOException $e) {
                    echo "Failed to fetch " . $this->mb_class . " model.<br/><br/>" . $e;
                }
                break;

        }

        /*
         * Did we find anything?
         * If we did, populate the object with the values and save the mb_wc to use later.
         * If we didn't find anything, make a default object and trash the mb_wc.
         */
        try {
            if($stmt->rowCount() == 0) {
                mb_debugMessage("No records found. Using defaults.");
                $this->mb_setDefaults();
                $this->mb_wc = "";
            }
            else {
                mb_debugMessage("Record found.");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                foreach($row as $key=>$value) {
                    $this->$key = $value;
                }
                $this->mb_wc = strstr($query," WHERE ", false); //Keep the query instead of the original mb_wc so we don't have to do the conversion again later
                if($custom_wc_values != "")
                    $this->mb_custom_wc_values = $custom_wc_values;
                else
                    $this->mb_custom_wc_values = $wc;
            }
        }
        catch(PDOException $e) {
            die("Could not fetch result for " . $this->mb_class . " model.");
        }
    }

    /**
     * update()
     * Save the model's information to the database.
     * This calls additional functions so it's easier for developers to customize the system to their needs.
     * An example modification would be determining if a record has been altered by someone else before you to prevent overwriting changes.
     * We keep things simple here though ;)
     */
    function update() {
        //Perform validation first
        if($this->validate()) {
            /*
             * Insert a new record
             */
            if($this->mb_wc == "") {
                mb_debugMessage("Adding new record");
                $this->insertRecord();
            }
            /*
             * Update the existing record
             */
            else {
                mb_debugMessage("Updating existing record");
                $this->updateRecord();
            }
        }
    }

    /**
     * insertRecord
     * Inserts the new record into the database.
     */
    private function insertRecord() {
        global $db;
        mb_debugMessage("Called insertRecord");
        $field_string = "";
        $value_string = "";
        foreach($this->mb_tableStructure as $field) {
            if(!strstr($field['Extra'],"auto_increment")) { //Let auto increment fields handle themselves
                //INSERT INTO table (...
                $field_string   .= $field['Field'] . ",";
                //VALUES(...
                $value_string   .= ":" . $field['Field'] . ",";
                //The array of values to use in our prepared statement
                $values[":".$field['Field']] = $this->$field['Field'];
            }
        }
        //Trim off the ending comma
        $field_string = substr($field_string,0,-1);
        $value_string = substr($value_string,0,-1);

        //Now piece the query together!
        $query = "INSERT INTO " . $this->mb_class . " (" . $field_string . ") VALUES (" . $value_string . ")";
        mb_debugMessage($query);
        mb_debugMessage($values);

        //Let's insert it
        try {
            $stmt = $db->prepare($query);
            $stmt->execute($values);
        }
        catch(PDOException $e) {
            die("Error inserting " . $this->mb_class . " record.<br/><br/>" . $e);
        }
        mb_debugMessage("Record inserted.");
    }

    /**
     * updateRecord
     * Updates the existing record in the database based on how it was selected ($this->wc)
     */
    private function updateRecord() {
        global $db;

        mb_debugMessage("Called updateRecord");
        $field_string = "";
        foreach($this->mb_tableStructure as $field) {
            if(!strstr($field['Extra'],"auto_increment")) { //Let auto increment fields handle themselves
                //UPDATE table SET...
                $field_string   .= $field['Field'] . "=?,";
                //The array of values to use in our prepared statement
                $values[] = $this->$field['Field'];
            }
        }
        //Trim off the ending comma
        $field_string = substr($field_string,0,-1);

        /*
         * Now we need to add the original where-clause values into the $values array
         */
        //Array of values?
        if($this->wc_type == ModelBuddyModel::wc_use_array) {
            foreach($this->mb_custom_wc_values as $wc_value) {
                $values[] = $wc_value; //How convenient we built this array for the constructor's select statement ;)
            }
        }
        //Custom values?
        elseif($this->wc_type == ModelBuddyModel::wc_use_custom) {
            $values[] = $this->mb_custom_wc_values;
        }
        else {
            $values[] = $this->{$this->mb_primary_key};
        }

        //Now piece the query together!
        $query = "UPDATE " . $this->mb_class . " SET " . $field_string . " " . $this->mb_wc;
        mb_debugMessage($query);
        mb_debugMessage($values);

        //Let's update it
        try {
            $stmt = $db->prepare($query);
            $stmt->execute($values);
        }
        catch(PDOException $e) {
            die("Error updating " . $this->mb_class . " record.<br/><br/>" . $e);
        }
        mb_debugMessage("Record updated.");
    }

    protected function validate() {
        //TODO: Validation code here
        return true;
    }

}