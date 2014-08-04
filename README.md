PHP-ModelBuddy
==============
*Database-driven applications should be easy to create. Let Model Buddy help you!*

### I had two problems when working on database-driven projects:

1. Writing SQL queries can be tedious and messy
2. Using a complete MVC framework is beyond overkill for some of my side projects

I wanted selecting a table row to be as simple as **$Andrew = new Person(1);** and updating that same item be as simple as **$Andrew->update();** without having to abide by someone else’s template engine and program design standards so I created Model Buddy.

Model Buddy reads the structures of your MySQL database tables to learn how it should handle data, can be easily customized to perform validation checks and best of all allows you and or your team to stick to your current in-house design trends.

###Need to add a client to the database? Take an object-oriented approach:
    $Client = new Client()
	$Client->name = "Fred";
	$Client->business = "Fred's Super Warehouse of Justice";
	$Client->update();

###Want to select a client from the database? Model Buddy is flexible:

####Select by primary key
Model Buddy will find your table’s primary key and can look up a record given only an index
    
    $client = new Client(42);
####Match an array of values
Give Model Buddy an array of values to match when searching for a record

	$client = new Client(array("client_id"=>42,"name"=>"Fred"));
####Write your own WC
Write your own where-clause for maximum flexibility! Don’t let operators stop you and certainly don’t use a different function. While you may have had to hand-write the where clause once, Model Buddy will handle it for you later when you need to insert, update or delete your record.

	$client = new Client("client_id >?",array(100));
Yes, Model Buddy uses **prepared statements** with [PDO MySQL](http://php.net/manual/en/ref.pdo-mysql.php) so you don’t have to worry about manually escaping your data.


###There are even more great reasons to love Model Buddy such as…
#####Validation
Override the validate() function to analyze modified data before it’s sent to the database. Something amiss? Tell the function to return false and then handle the discrepancy however you’d like.
#####Caching
Bombarding your database with redundant requests is never a good idea so when ModelBuddy looks up your table structure he only does it for the first instance of that model. Additional instances of the same model made during runtime will inherit the knowledge from memory.  
#####Performance
Model Buddy is designed so you don’t have to starve your server of its resources for a simple web application. Other frameworks come with fancy bells and whistles while Model Buddy is equipped with a lightweight tool belt simply for fetching and manipulating data.

Best of all, Model Buddy helps the performance of you and or your development team by handling database operations with three simple commands:

    new Model() //Create a new record or select an existing one
	update()	//Update or insert the record
	delete()	//Delete the record
#####Ease of Use & Customization
Model Buddy doesn’t just make it extremely simple to handle data manipulation; extend the abstract ModelBuddyModel and swap out individual parts as you need! Want to make sure you don’t overwrite someone’s recent changes? The **update()** function is written to be used as a template you can customize however you need. Each table gets its own class so you can even re-write functions on a per-table basis.
 
##Want to use ModelBuddy?
Edit config/modelBuddy_config.php, run demo.sql on your MySQL database and then check out the demo.php program for examples of ModelBuddy in action. 

To get started with Model Buddy in your own project...

####1. Include Model Buddy

	include("inc/modelBuddy.php");
####2. Create a "models" directory and a PHP file representing a table name.
####3. Open your [table].php file, edit and save the following example code:

	<?php
	class TableModel extends ModelBuddyModel {
	}
	
ModelBuddy will automatically load the new model file. 
####4. Start using Model Buddy!
	$object = new TableModel(); //No key specified. Table defaults loaded
	$object->field = "Hello World!"; //Set the value of a table row
	$object->update(); //Inserts the new record
	
	$object2 = new TableModel(42); //Select from Table where primary key = 42
	$object2->update(); //Update the record
	$object->delete(); //Delete this record
	
	
#Enjoy!
Like Model Buddy? Let me know you're using him! :D

AndrewNatoli [at] AndrewNatoli [dot] com