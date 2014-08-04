<?php

class StoreModel extends ModelBuddyModel {
    /**
     * $employees
     * Array containing our employee objects
     */
    public $employees = array();

    /**
     * extra_constructor()
     * Additional tasks to run once we've fetched the original model.
     * Here we're loading employee (Person) records into our Store.
     */
    function extra_constructor() {
        global $db;
        /*
         * Find employees associated with this store
         */
        try {
            $stmt = $db->prepare("SELECT person_id FROM employee WHERE store_id=?");
            $stmt->execute(array($this->store_id));
            /*
             * Found employees
             */
            if($stmt->rowCount() > 0) {
                $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($employees as $employee) {
                    $this->employees[] = new PersonModel($employee['person_id']);
                }
            }
        }
        catch(PDOException $e) {
            die("Failed to fetch employees for store. " . $e);
        }
    }
}