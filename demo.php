<?php
/**
 * PHP Model Buddy
 * Demonstration Program
 * @author Andrew Natoli
 * @date 2014 August
 * @since 1
 */

error_reporting(255);
include("inc/modelBuddy.php");

if(!empty($_GET['mode']))
    $mode = $_GET['mode'];
else
    $mode = "default";

if(!empty($_GET['step']))
    $step = $_GET['step'];
else
    $step = "1";

switch($mode) {
    /*
     * Selection demo
     */
    case "select":
        echo "<br/><Br/><b>Select by array</b><br/>";
        $array = array("person_id"=>1,"firstname"=>"Andrew");
        $guy = new PersonModel($array);

        echo "<br/><br/><b>Select by primary key</b><br/>";
        $guy = new PersonModel(1);


        echo "<br/><br/><b>Select using a custom wc</b><br/>";
        $guy = new PersonModel("WHERE person_id=?",array(1)); //You shouldn't use a leading WHERE but the program WILL correct this :)

        echo "<hr><pre>";
        print_r($guy);
        echo "<br/><a href='demo.php'>Back to menu</a>";
        break;

    case "insert":
        switch($step) {
            case 1:
                echo "<h2>Model Creation Demo</h2>Fill out the form to create a record. Follow the proper format for the birth date as this is a rushed form and doesn't do validation.";
                ?>
                <form action="demo.php?mode=insert&step=2" method="POST">
                    <input name="firstname" type="text" size="30" maxlength="20" placeholder="First name" required><br/>
                    <input name="lastname" type="text" size="30" maxlength="20" placeholder="Last name" required><br/>
                    <input name="dob" type="text" size="30" maxlength="10" placeholder="Birthday (yyyy-mm-dd)" required><br/>
                    <input type="submit" value="Create">
                </form>
                <?php
                break;
            case 2:
                $newPerson = new PersonModel();
                $newPerson->firstname   = $_POST['firstname'];
                $newPerson->lastname    = $_POST['lastname'];
                $newPerson->dob         = $_POST['dob'];
                echo "<h2>Here's what your new model looks like...</h2>";
                echo "<pre>";
                print_r($newPerson);
                echo "</pre>";
                echo "<h3>Now we'll save it to the database by calling PersonModel's update function</h3>";
                $newPerson->update();
                break;
        }
        echo "<br/><a href='demo.php'>Back to menu</a>";
        break;

    case "update":
        switch($step) {
            case 1:
                $newPerson = new PersonModel(1);
                echo "<h2>Model Update Demo</h2>Modify the form to update the record. Follow the proper format for the birth date as this is a rushed form and doesn't do validation.";
                ?>
                <form action="demo.php?mode=update&step=2" method="POST">
                    <strong>person_id: <?php echo $newPerson->person_id; ?></strong><br/>
                    <input name="firstname" type="text" size="30" maxlength="20" value="<?php echo $newPerson->firstname; ?>" placeholder="First name" required><br/>
                    <input name="lastname" type="text" size="30" maxlength="20" value="<?php echo $newPerson->lastname; ?>" placeholder="Last name" required><br/>
                    <input name="dob" type="text" size="30" maxlength="10" value="<?php echo substr($newPerson->dob,0,10); ?>" placeholder="Birthday (yyyy-mm-dd)" required><br/>
                    <input type="submit" value="Create">
                </form>
                <?php
                break;
            case 2:
                $newPerson = new PersonModel(1);
                $newPerson->firstname   = $_POST['firstname'];
                $newPerson->lastname    = $_POST['lastname'];
                $newPerson->dob         = $_POST['dob'];
                echo "<h2>Here's what your revised model looks like...</h2>";
                echo "<pre>";
                print_r($newPerson);
                echo "</pre>";
                echo "<h3>Now we'll update it in the database by calling PersonModel's update function</h3>";
                $newPerson->update();
                break;
                echo "<br/><a href='demo.php'>Back to menu</a>";
        }
        break;

    case "delete":
        $newPerson = new PersonModel("WHERE person_id>=?",array(2));
        echo "<h2>Deletion Demo</h2>We're going to select a record, delete it from the database but then re-insert it as a new record.<br/><Br/>";

        $newPerson->delete();
        echo "<h3>The record still exists in PHP so we can modify it and re-insert it as a new record!</h3>";
        $newPerson->update();
        echo "<h4>Done.</h4>";
        echo "<a href='demo.php'>Back to menu</a>";
        break;

    case "cache":
        echo "<h2>Cache Demo</h2>Instead of hitting the database server every time we need the structure of a model, Model Buddy does it the first
                time for each model type and stores the structure in a PHP array. If MB_DEBUG is set to true in modelBuddy_config.php,
                notice the difference in debug messages when we select our second person.<br/><br/>";
        $firstPerson = new PersonModel(1);
        $secondPerson= new PersonModel("person_id>?",array(1));

        echo "<br/><a href='demo.php'>Back to menu</a>";
        break;

    case "tree":
        echo '<h2>Data Tree</h2>
            Model Buddy can execute an optional function at the end of its constructor designed
            to extend the model with additional models.<br/><br/>

            For example: if you have a store and want to load in all of the employees, you can load the employee
            model into the store model but alter them separately. Here\'s an example of what the resulting object looks like:
            <br/><br/>
        ';
        $myStore = new StoreModel(1);
        echo '<pre>';
        print_r($myStore);
        echo '</pre>';

        echo 'You can now manipulate that employee with it visualized as part of the store. ($myStore->employees[0]->update())<br/><br/>';
        echo $myStore->employees[0]->firstname . " " . $myStore->employees[0]->lastname . '<br/><br/>';
        $myStore->employees[0]->lastname = "Crunch";
        $myStore->employees[0]->update();

        echo '<br/><br/> Another use could be listing all the products in a store or all of the doctors that work at a practice.';
        echo "<br/><a href='demo.php'>Back to menu</a>";
        break;

    /*
     * Main menu
     */
    default:
        echo "<h2>Welcome to the PHP Model Buddy Demo!</h2>";
        echo "<ol>";
        echo "<li><a href='?mode=select'>Select Demo</a></li>";
        echo "<li><a href='?mode=insert'>Insert Demo</a></li>";
        echo "<li><a href='?mode=update'>Update Demo</a></li>";
        echo "<li><a href='?mode=delete'>Delete Demo</a></li>";
        echo "<li><a href='?mode=cache'>Cache Demo</a></li>";
        echo "<li><a href='?mode=tree'>Data Tree Demo</a></li>";
        echo "</ol>";
        break;
}