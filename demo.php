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

echo "<br/><Br/><b>Select by array</b><br/>";
$array = array("person_id"=>1,"firstname"=>"Andrew");
$guy = new PersonModel($array);

echo "<br/><br/><b>Select by primary key</b><br/>";
$guy = new PersonModel(1);

/*
echo "<br/><br/><b>Select using a custom wc</b><br/>";
$array = array("person_id"=>1,"firstname"=>"Andrew");
$guy = new PersonModel($array);
*/