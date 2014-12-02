<?	//Purpose:		This is user registration page
	//Original Author:	Brett Jones
	//Creation Date:	11/24/2013
	//Modification Purpose: Fixed failed validation to not register user
	//Modification Date:	11/27/2013
	//Modification Purpose: Added email notification
	//Modification Date:	12/1/2013

	//inclusions
	include 'myauth.php';
	include "system.php";
	include 'session.php';
	include 'displayfunctions.php';
	include 'mymail.php';

	//Generate the page, always show logout option, show PDF option
	BeginDoc('Poster Management System');
	PrintBreak();

	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{
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
		$Contact=mysql_real_escape_string($_REQUEST['Contact']);

		//see if there is a validation
		if(@$_REQUEST['op']=="sq")		//square
		{
			$para = pow(@$_REQUEST['para'], 2);
			if($para != @$_REQUEST['Solution']) {
				echo "Validation failed.";
				PrintBreak();
				echo "Try again or contact the system administrator.";
				PrintBreak();
				header("refresh: 5; register.php");
				exit;
			}
		} else if(@$_REQUEST['op']=="sqrt") {	//squareroot
			$para = sqrt(@$_REQUEST['para']);
			if($para != @$_REQUEST['Solution']) {
				echo "Validation failed.";
				PrintBreak();
				echo "Try again or contact the system administrator.";
				PrintBreak();
				header("refresh: 5; register.php");
				exit;
			}
		} else {				//shouldn't get to here
			echo "Validation failed.";
			PrintBreak();
			echo "Try again or contact the system administrator.";
			PrintBreak();
			header("refresh: 5; register.php");
			exit;
		}

		$result=mysql_query("SELECT * FROM users WHERE Name='$Name'");
		$count=mysql_num_rows($result);
		if($count>0)
		{	//found the user, so we can't register
			echo "Username already exists!  Redirecting in a few seconds.";
			PrintBreak();
			echo "Try another or contact the system administrator.";
			PrintBreak();
			header("refresh: 5; register.php");
		} else
		{	//if we DON'T find the user, we are ADDING a new user
			mysql_query("INSERT INTO users (Name,Password,Access,Contact) VALUES('$Name','$Password','User','$Contact');");
			echo "Registration was successful.  Redirecting in a few seconds.";
			//now send email notification
			SendEmail($Contact,$Name,'Registration successful',
				'If you did not register with the system, please reply to this email.');
			PrintBreak();
			header("refresh: 5; index.html");
		}
	} else {
		//Setup the form
		echo "Please enter your username, password, and contact email address below:";
		PrintFormHeader('register.php','post','');
		//assign initial values to textboxes
		PrintTextbox('Name','',50,'');
		PrintTextbox('Password','',50,'');
		PrintTextbox('Contact','',50,'');
		PrintBreak();
		echo "Additionally, please solve the following simple equation to proceed: ";
		PrintBreak();
		mt_srand((double)microtime() * 1000000);
		$Num = mt_rand(1,2);
		if($Num==1) {	// square
			$para = mt_rand(1,100);
			$op = "sq";
			echo "Find the square of " . $para;
			PrintBreak();
		} else {	//square root
			$para = mt_rand(1,100);
			$para = pow($para, 2);
			$op = "sqrt";
			echo "Find the square root of " . $para;
			PrintBreak();
		}
	
		echo "<input type=hidden name=op value=$op>";
		echo "<input type=hidden name=para value=$para>";
		PrintTextbox('Solution','',50,'');

		PrintButton('submit', 'Submit', true);
		PrintButton('reset', 'Clear', false);

		//End the form
		PrintFormFooter();
	}
	//End the document
	EndDoc();
?>
