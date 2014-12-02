<?	//Purpose:		This will handle all login functions
	//Original Author:	Brett Jones
	//Creation Date:	9/14/2013
	//Modification Date:	11/18/2013
	//Modification Purpose:	Audit for Coding Convention exchange

	//Inclusions
	include "system.php";
	include 'session.php';
	include 'displayfunctions.php';

	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{	//Connection failed, notify and abort
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);

	//Only accept non-empty username
	if(@$_REQUEST['Name']!="")
	{	//use mysql_real_escape_string to protect against SQL inejction
		$Name=mysql_real_escape_string($_REQUEST['Name']);
		$Password=mysql_real_escape_string($_REQUEST['Password']);

		//Look for a username and password match
		$result=mysql_query("SELECT * FROM users WHERE Name='$Name' and Password='$Password'");
		$count=mysql_num_rows($result);

		//There should never be more than one match
		if($count==1) {
			//Assign session ID
			mysql_query("UPDATE users SET Session = 'NewSession' WHERE Name='$Name' and Password='$Password'");
			//Redirect user to Welcome
			header( 'Location: welcome.php?Session=NewSession' );
		}
	//Otherwise, reset the form
	} else {
		$Name = "";
		$Password = "";
	}
	
	//Assume login failed, begin creating a replica of the index page.
	BeginDoc('Poster Management System');
?>
&nbsp;<p>
<h1>Login</h1>

<h2><?='Invalid login information'?></h2>
<?	//Setup the login form, looping back to this page
	//Form needs a textbox for Name and another for Password
	//Form needs a submit (Login) and reset (Clear) button
	PrintFormHeader('login.php','post','');
	PrintTextbox('Name','',50,"$Name");
	PrintPasswordbox('Password','',50,"$Password");
	PrintButton('submit', 'Login', true);
	PrintButton('reset', 'Clear', false);
	//End the form
	PrintFormFooter();

	//Optionally, register as new user, sending to register.php
	//Needs only a label and submit (Register) button
	PrintFormHeader('register.php','post','');
	echo "New User?";
	PrintButton('submit', 'Register', false);
	//End the form
	PrintFormFooter();

	//Finish the replica of the index page
	EndDoc();
?>
