<?	//Purpose:		This will handle logout
	//Original Author:	Brett Jones
	//Creation Date:	9/14/2013
	//Modification Date:	11/18/2013
	//Modification Purpose:	Audit for Coding Convention exchange

	//Inclusions
	include "system.php";
	include 'session.php';

	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{	//Connection failed, notify and abort
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);

	if(@$_REQUEST['Session']!="")
	{	//use mysql_real_escape_string to protect against SQL inejction
		$Session = mysql_real_escape_string(@$_REQUEST['Session']);

		//Look for a username that matches the session ID
		$Name = mysql_result(mysql_query("SELECT Name FROM users WHERE Session='$Session'"), 0);

		//Update the user to be logged out
		$result=mysql_query("SELECT * FROM users WHERE Name='$Name'");

		//There should never be more than one match
		$count=mysql_num_rows($result);
		if($count==1) {
			mysql_query("UPDATE users SET Session = 'Loggedout' WHERE Name='$Name'");
		}
	}
	//redirect to login page
	header( 'Location: index.html' );
?>
