<?	//Purpose:		This is the manage user page and will handle user management
	//Original Author:	Brett Jones
	//Creation Date:	9/14/2013
	//Modification Date:	11/18/2013
	//Modification Purpose:	Audit for Coding Convention exchange

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
?>
<?	//May or may not keep this... generates PDF from userlist
	if (urlencode(@$_REQUEST['action']) == "getpdf")
	{	//Don't generate a new session ID when making PDF
		//but connect to the database
	 	mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']);
		mysql_select_db($TEXT['database_name']);

		//include the PDF definitions
	        include ('fpdf/fpdf.php');
		//Setup the file
        	$pdf = new FPDF();
	        $pdf->AddPage();
		//initial info
	        $pdf->SetFont('Helvetica', '', 14);
        	$pdf->Write(5, 'Users');
	        $pdf->Ln();
	        $pdf->SetFontSize(10);
        	$pdf->Write(5, '© 2012 Brett Jones, evil_greven@yahoo.com');
	        $pdf->Ln();
        	$pdf->Ln(5);
	        $pdf->SetFont('Helvetica', 'B', 10);
		//setup the table
        	$pdf->Cell(50 ,7, 'Name', 1);
		$pdf->Cell(50 ,7, 'Password',1);
        	$pdf->Cell(50 ,7, 'Access', 1);
	        $pdf->Ln();
	        $pdf->SetFont('Helvetica', '', 10);

		//Get the users
		$result=mysql_query("SELECT Name,Password,Access,Contact FROM users ORDER BY Name");

		//For each user print information
		while ($row = mysql_fetch_array($result))
		{	//One row contains the short info
			$pdf->Cell(50, 7, $row['Name'], 1);
			$pdf->Cell(50, 7, $row['Password'], 1);
			$pdf->Cell(50, 7, $row['Access'], 1);
        		$pdf->Ln();
			//Another row contains the contact info
			$pdf->SetFont('Helvetica', 'B', 10);
			$pdf->Cell(50, 7, 'Contact Info', 1);
			$pdf->SetFont('Helvetica', '', 10);
			$pdf->Cell(100 ,7, $row['Contact'], 1);
			$pdf->Ln();
		}
		//show the user the generated PDF and stop the page generation
		$pdf->Output();
		exit;
	} else {//otherwise, we're not doing a PDF, so make a session key
		$Session = NewSessionKey(@$_REQUEST['Session']);
	}

	//Generate the page, always show logout option, show PDF option
	BeginDoc('Poster Management System');
	PrintLink('welcome.php',$Session,'Return to Previous');
	PrintSpace();
	PrintHref('?action=getpdf&Session='.$Session,'User list As PDF');
	PrintSpace();
	PrintLink('logout.php',$Session,'Logout');
	PrintBreak();
?>
<?='This lists users for the Poster Management System.'?><br>
<?='Only Administrators should have access to this page.'?><p>
<?='Access Levels: Administrator, User'?><br>
<?='Administrators have access Manage Users and Check Posters.'?><br>
<?='Users have access to Check Poster Status, Submit Posters, and Cancel Print Request.'?><br>

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

<h2><?='Users'?></h2>

<?
	//Setup the user list table
	PrintListHeader(); 
	PrintListCaption('Command', 50);
	PrintListCaption('', 0);
	PrintListCaption('Name', 200);
	PrintListCaption('Password', 200);
	PrintListCaption('Access', 200);
	PrintListCaption('Contact Information', 200);
	//The caption is a row, which must be ended
	EndRow();

	//Only accept non-empty username
	if(@$_REQUEST['Name']!="")
	{	//use mysql_real_escape_string to protect against SQL inejction
		$Name=mysql_real_escape_string($_REQUEST['Name']);
		$Password=mysql_real_escape_string($_REQUEST['Password']);
		$Access=mysql_real_escape_string($_REQUEST['Access']);
		$Contact=mysql_real_escape_string($_REQUEST['Contact']);

		//This is for modifying the user info		
		if(@$_REQUEST['ID']!="")
		{	//If we have an ID, we are EDITING a user
			$ID=mysql_real_escape_string($_REQUEST['ID']);
			$result=mysql_query("SELECT * FROM users WHERE ID='$ID'");
			$count=mysql_num_rows($result);
			if($count>0)
			{	//found the user, so UPDATE the user
				mysql_query("UPDATE users SET Name='$Name' WHERE ID='$ID'");
				mysql_query("UPDATE users SET Password='$Password' WHERE ID='$ID'");
				mysql_query("UPDATE users SET Access='$Access' WHERE ID='$ID'");
				mysql_query("UPDATE users SET Contact='$Contact' WHERE ID='$ID'");
			} else
			{	//if we DON'T find the user, we are ADDING a new user
				mysql_query("INSERT INTO users (Name,Password,Access,Contact) VALUES('$Name','$Password','$Access','$Contact');");
			}
		} else
		{	//if we don't have an ID, we are ADDING a new user
			mysql_query("INSERT INTO users (Name,Password,Access,Contact) VALUES('$Name','$Password','$Access','$Contact');");
		}
	}

	//If the action is to delete, we need to remove a user
	if(@$_REQUEST['action']=="del")
	{	//This will delete a user if s/he is there
		mysql_query("DELETE FROM users WHERE ID=".round($_REQUEST['ID']));
	}

	//Select from database
	$result=mysql_query("SELECT * FROM users ORDER BY Name");

	$i=0;
	while( $row=mysql_fetch_array($result) )
	{	//Display each user in a row
		$id = $row['ID'];
		//Don't divide the first row
		if($i>0) PrintListDivider();

		//start the row
		BeginRow('center');
		//add the remove option
		echo "<td class=tabval><a onclick=\"return confirm('".'Are you sure?'."');\" href=manageusers.php?action=del&Session=$Session&ID=$id><span class=red>[".'Remove'."]</span></a></td>";

		//conditionally add the edit option
		if(@$_REQUEST['action']=="ed" && $_REQUEST['ID'] == $id)
		{	//If we are editing, and THIS user is the one we are editing...
			//pull the values from the user to populate the textboxes
			$name_edit=$row['Name'];
			$password_edit=$row['Password'];
			$access_edit=$row['Access'];
			$contact_edit=$row['Contact'];
			//and don't display remove or values
			RowEntry('','');
			RowEntry('Editing...','green');
			RowEntry('','');
			RowEntry('','');
			RowEntry('','');
			RowEntry('','');
		} else
		{	//Otherwise print normally
			//add the edit option
			LinkedRowEntry('manageusers.php?action=ed&Session='.$Session.'&ID='.$id,'Edit','green');
			//display values
			RowEntry($row['Name'],'');
			RowEntry($row['Password'],'');
			RowEntry($row['Access'],'');
			RowEntry($row['Contact'],'');
		}
		//finish off the row
		RowEntry('','');
		EndRow();
		$i++;
	}
	//Close out the list
	PrintListFooter();

	//Setup the form
	echo "Known issue: The use of single or double quotation marks will cause problems";
	PrintFormHeader('manageusers.php','post',"$Session");
	if(@$_REQUEST['action']=="ed")
	{	//if we are editing...
		$ID=mysql_real_escape_string($_REQUEST['ID']);
		echo "<input type=hidden name=ID value=$ID>";
	} else
	{	//otherwise make sure these variables are empty
		$name_edit=$password_edit=$access_edit=$contact_edit="";
	}
	//assign initial values to textboxes
	PrintTextbox('Name','',50,"$name_edit");
	PrintTextbox('Password','',50,"$password_edit");
	PrintTextbox('Access','',50,"$access_edit");
	PrintTextbox('Contact','',50,"$contact_edit");

	//Caption the submit button appropriately
	//and if editing, don't show the clear button
	if(@$_REQUEST['action']=="ed")
	{	
		PrintButton('submit', 'Save User', true);
	} else
	{
		PrintButton('submit', 'Add User or Refresh', true);
		PrintButton('reset', 'Clear', false);
	}
	//End the form
	PrintFormFooter();
	//End the document
	EndDoc();
?>
