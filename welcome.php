<?	//Purpose:		This is the welcome page, which is primarily for navigation
	//Original Author:	Brett Jones
	//Creation Date:	9/14/2013
	//Modification Date:	11/18/2013
	//Modification Purpose:	Audit for Coding Convention exchange

	//inclusions
	include "system.php";
	include 'myauth.php';
	include 'session.php';
	include 'displayfunctions.php';

	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);

	//verify that the system is expecting the user here
	if (!CheckAccess(@$_REQUEST['Session']))
	{
		//show the access denied message and exit script
		echo 'Invalid session.  Please login again.';
		exit;

	} 
?>
<?
	//Generate new session ID
	$Session = NewSessionKey(@$_REQUEST['Session']);

	//Generate the welcome page, always show logout option
	BeginDoc('Poster Management System');
	PrintLink('logout.php',$Session,'Logout'); ?>
&nbsp;<p>
<h1>Welcome</h1>
<?	//Determine access level of the user and generate page accordingly
	$result=mysql_query("SELECT Access FROM users WHERE Session='$Session'");
	$access = mysql_result($result, 0);
	$result=mysql_query("SELECT Name FROM users WHERE Session='$Session'");
	$name = mysql_result($result, 0);
	echo "<h2>Access level: $access</h2>";
	if($access=="Administrator")
	{	//Admins can access Check Posters and Manage Users
		PrintLink('checkposters.php',$Session, 'Check Posters');
		echo " - You can check posters here.";
		PrintBreak();
		PrintLink('manageusers.php',$Session, 'Manage Users');
		echo " - You can can add/change/remove users here.";
		PrintBreak();
	} else if($access=="User")
	{	//Users can access Check Poster Status and Submit Poster
		PrintLink('checkposterstatus.php',$Session,'Check Poster Status');
		echo " - You can check the status of your poster(s) here.";
		PrintBreak();
		PrintLink('submitposter.php',$Session,'Submit Poster');
		echo " - You can submit a poster here.";
		PrintBreak();
		PrintLink('cancelprintrequest.php',$Session,'Cancel Print Request');
		echo " - You can cancel a submitted poster here.";
	}
	//end the page
	EndDoc();
?>
