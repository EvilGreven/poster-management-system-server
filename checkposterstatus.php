<?	//Purpose:		This lists the posters that a user has submitted, along with their status
	//Original Author:	Brett Jones
	//Creation Date:	11/24/2013

	//inclusions
	include 'myauth.php';
	include "system.php";
	include 'session.php';
	include 'displayfunctions.php';

	//verify that the system is expecting the user here
	if (!CheckAccess(@$_REQUEST['Session']))
	{
		//show the access denied message and exit script
		echo 'Invalid session.  Please login again.';
		exit;
	} 

	//generate a new session key
	$Session = NewSessionKey(@$_REQUEST['Session']);

	//Generate the page, always show logout option
	BeginDoc('Poster Management System');
	PrintLink('welcome.php',$Session,'Return to Previous');
	PrintSpace();
	PrintLink('logout.php',$Session,'Logout');
	PrintBreak();
?>

<?='This lists posters that you have submitted, along with their status.'?><br>

<?
	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);

?>

<h2><?='Check Poster Status'?></h2>

<? 
	//Select access from database
	$result=mysql_query("SELECT Access FROM users WHERE Session='$Session'");
	$access = mysql_result($result, 0);
	echo "<h2>Access level: $access</h2>";

	//Setup the Poster list table
	PrintListHeader(); 
	PrintListCaption('', 50);
	PrintListCaption('Poster', 200);
	PrintListCaption('Status', 200);
	//The caption is a row, which must be ended
	EndRow();

	$result=mysql_query("SELECT Name FROM users WHERE Session='$Session'");
	$Name = mysql_result($result, 0);
	//Select from database
	$result=mysql_query("SELECT * FROM posters WHERE User='$Name' ORDER BY Flag");

	$i=0;
	while( $row=mysql_fetch_array($result) )
	{	//Display each poster in a row
		$id = $row['ID'];
		//Don't divide the first row
		if($i>0) PrintListDivider();

		//start the row
		BeginRow('center');

		//Print normally
		RowEntry('','');
		//add the linked file
		$filename = $row['File'];
		LinkedRowEntry('upload/'.$filename, $filename,'');
		//display other values
		RowEntry($row['Flag'],'');
		//finish off the row
		RowEntry('','');
		EndRow();
		$i++;
	}
	//Close out the list
	PrintListFooter();

	//End the document
	EndDoc();
?>
