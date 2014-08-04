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
                $newPerson = new PersonModel(array("person_id=1"));
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
        }
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
        echo "</ol>";
        break;
}