<?php
	//Purpose:		This is the individual poster modification page
	//Original Author:	Brett Jones
	//Creation Date:	11/24/2013
	//Modification Date:	12/1/2013
	//Modification Purpose: Added email notification
	//Modification Author:	Paul Wiechmann
	//Modification Date:	12/4/2013
	//Modification Purpose: Added android compatibility
	//Modification Date:	12/4/2013
	//Modification Authors:	Brett Jones
	//Modification Purpose: Fixed a few issues with android compatibility

	//inclusions
	include 'myauth.php';
	include "system.php";
	include 'session.php';
	include 'displayfunctions.php';
	include 'mymail.php';
	
	//Flag for mobile connection
	$mobile = false;
	if(@$_REQUEST['Mobile']!="") $mobile = true;

	//verify that the system is expecting the user here
	if (!CheckAccess(@$_REQUEST['Session']))
	{	//Access failed, notify and abort
		if($mobile) {
		        $result_data = array(
				'Next' => "login",
				'Session' => "error:session",
				'Data' => array()
			);

		        //Output the JSON data
		        echo json_encode($result_data); 
			//if we're on mobile, exit now
			exit;
		}
		//show the access denied message and exit script
		echo 'Invalid session.  Please login again.';
		exit;
	} 

	//generate a new session key
	$Session = NewSessionKey(@$_REQUEST['Session']);
?>

<?php
	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{	//Connection failed, notify and abort
		if($mobile) {
		        $result_data = array(
				'Next' => "login",
				'Session' => "error:database",
				'Data' => array()
			);

		        //Output the JSON data
		        echo json_encode($result_data); 
			//If we're on mobile, exit now
			exit;
		}
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);

?>

<?php
	
	// Get the poster to be modified.
	$ID = $_REQUEST['ID'];
	$result = mysql_query("SELECT * FROM posters WHERE ID='$ID'");
	$count = mysql_num_rows($result);
		
	// Exit if poster doesn't exist.
	if ($count == 0) exit;
	
	// Get poster information.
	$row = mysql_fetch_array($result);
	$Name = $row['User'];
	$File = $row['File'];
	$Flag = $row['Flag'];
	$feedback = "";
		
	// Process the update request if it exists.
	if(@$_REQUEST['submit']=='processing') {
		mysql_query("UPDATE posters SET Flag='Processing' WHERE ID='$ID'");
		$feedback = "Poster set to Processing.";
			
	} else if(@$_REQUEST['submit']=="printed") {
		mysql_query("UPDATE posters SET Flag='Printed' WHERE ID='$ID'");
			
		// Get contact info for the user whose poster status was changed.
		$result = mysql_query("SELECT Contact FROM users WHERE Name='$Name'");
		$Contact = mysql_result($result, 0);
		
		// Notify the user that the poster is ready for pickup.
		SendEmail($Contact,$Name,'Poster Printed',
		'Your poster has been printed and awaits pickup.  Please reply if there are any problems');
		$feedback = "Poster set to Printed.";
				 
	} else if(@$_REQUEST['submit']=="finished") {
		mysql_query("UPDATE posters SET Flag='Finished' WHERE ID='$ID'");
		$feedback = "Poster set to Finished.";
			
	} else if(@$_REQUEST['submit']=="wrong") {
		mysql_query("UPDATE posters SET Flag='Wrong Format/Size' WHERE ID='$ID'");
		
		// Get contact info for the user whose poster status was changed.
		$result = mysql_query("SELECT Contact FROM users WHERE Name='$Name'");
		$Contact = mysql_result($result, 0);
			
		// Notify the user that the poster is the wrong format or size.
		SendEmail($Contact,$Name,'Poster Error',
		'Your poster is in the wrong file format or has the wrong size.  Please correct and resubmit or contact us.');
		$feedback = "Poster set to Wrong Format/Size.";
		  
	} else if(@$_REQUEST['submit']=="other") {
		mysql_query("UPDATE posters SET Flag='Other Errors' WHERE ID='$ID'");
		
		// Get contact info for the user whose poster status was changed.
		$result = mysql_query("SELECT Contact FROM users WHERE Name='$Name'");
		$Contact = mysql_result($result, 0);
		
		// Notify the user that the poster has other problems.
		SendEmail($Contact,$Name,'Poster Has Errors',
		'Your poster has errors and cannot be printed.  Please correct and resubmit or contact us.');
		$feedback = "Poster set to Other Errors.";
	}
	
	if ($mobile){
		// Generate data for mobile platforms.
		// Array is formatted as follows:
		// There is one key for the next page and the session
		//     [ 'Next', 'Session', 'Data' ]
		// Data is an array containing user information
		//     [ 'Index' ][ 'ID', 'Name', 'Password', 'Access', 'Contact' ]
		// Put the next page, session, and poster information into an array.
		$result_data[] = array('ID' => $ID,
				'User'    => $Name,
				'File'    => $File,
				'Flag'    => $Flag
				);
		$json = array('Next'    => "changePosterStatus",
				'Session' => $Session,
				'Data' => $result_data
				);
		
		// Send the JSON data, then exit.
		echo json_encode($json);
		exit;
	}
?>

<?php
	//Generate the page, always show logout option
	BeginDoc('Poster Management System');
	PrintLink('checkposters.php',$Session,'Return to Previous');
	PrintSpace();
	PrintLink('logout.php',$Session,'Logout');
	PrintBreak();
	
	echo 'This shows an individual poster which may be viewed or its status changed.<br>';
	echo '<h2>Check Posters</h2>';
?>

<?php
	//Select access from database
	$result=mysql_query("SELECT Access FROM users WHERE Session='$Session'");
	$access = mysql_result($result, 0);
	echo "<h2>Access level: $access</h2>";

	$ID=$_REQUEST['ID'];
	$result=mysql_query("SELECT * FROM posters WHERE ID='$ID'");
	$count=mysql_num_rows($result);
	$row=mysql_fetch_array($result);
	$Name = $row['User'];
	echo $feedback;
	PrintBreak();

	//Select from database
	$result=mysql_query("SELECT * FROM posters WHERE ID='$ID'");
	//there should be at least one!
	while($row=mysql_fetch_array($result)) {
		$id=$row['ID'];
		$filename=$row['File'];
		echo "You may view the poster by clicking the linked filename.";
		PrintBreak();
		echo "Poster filename: ";
		PrintHref('upload/'.$filename, $filename);
		PrintBreak();
		//display other values
		echo "Poster status: " . $row['Flag'];
		PrintBreak();
		echo "Poster owner: " . $row['User'];
		PrintBreak();
		PrintBreak();

		//Setup the form
		PrintFormHeader('changeposterstatus.php','post',$Session);
		echo "<input type=hidden name=ID value=$id>";
		echo "Set status: ";
		PrintButtonEx('submit','processing','Processing');
		PrintButtonEx('submit','printed','Printed');
		PrintButtonEx('submit','wrong','Wrong Format/Size');
		PrintButtonEx('submit','other','Other Errors');
		//End the form
		PrintFormFooter();

		//Explaining the flags
		PrintBreak();
		echo "Processing: this means that the poster has been reviewed and is being printed.";
		PrintBreak();
		echo "Printed: this means that the poster has been printed and is awaiting pickup.";
		PrintBreak();
		echo "Wrong Format/Size: this means that the poster is in the wrong file format or size.";
		PrintBreak();
		echo "Other Errors: this means that there is some other error with the poster file.";
	}

	//End the document
	EndDoc();
?>
