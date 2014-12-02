<?	//Purpose:		This is the poster submission page
	//Original Author:	Brett Jones
	//Creation Date:	11/23/2013

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
	$Session = NewSessionKey(@$_REQUEST['Session']);

	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);
	//Generate the page, always show logout option, show PDF option
	BeginDoc('Poster Management System');
	PrintLink('welcome.php',$Session,'Return to Previous');
	PrintSpace();
	PrintLink('logout.php',$Session,'Logout');
	PrintBreak();

if(isset($_FILES["file"])) {
$allowedExts = array("ppt", "pptx", "pdf");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
//ppt = application/vnd.ms-powerpoint 
//pptx = application/vnd.openxmlformats-officedocument.presentationml.presentation
//pdf = application/pdf
if ((($_FILES["file"]["type"] == "application/vnd.ms-powerpoint")
|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.presentationml.presentation")
|| ($_FILES["file"]["type"] == "application/pdf"))
&& ($_FILES["file"]["size"] < 10485760)
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
	$filename = $_FILES["file"]["name"];
    echo "Upload: " . $filename . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
//    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

	$filename = str_replace(' ','_', $filename);
    if (file_exists("upload/" . $filename))
      {
	PrintBreak();
      echo $filename . " was NOT submitted!";
	PrintBreak();
      echo "A file with this name was already submitted.";
	PrintBreak();
	echo "Please rename your file and then submit.";
	PrintBreak();
      echo "Suggestions on naming include adding your name and date to the filename.";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $filename);
//      echo "Stored in: " . "upload/" . $filename;
	PrintBreak();
	echo "Verify your file here: ";
	PrintHref("upload/" . $filename,"File Link");
	$result=mysql_query("SELECT Name FROM users WHERE Session='$Session'");
	$Name = mysql_result($result, 0);
	mysql_query("INSERT INTO posters (User,File,Flag) VALUES('$Name','$filename','Submitted');");
	PrintBreak();
      echo $filename . " was successfully submitted.";
      }
    }
  }
else
  {
  echo "Invalid file or file exceeds 10 megabytes (10,240kB) in size.";
  }
} else {
	//Setup the form
	PrintBreak();
	echo "Please select a valid poster to upload using the Browse button";
}

	PrintBreak();
	PrintBreak();
	PrintFormHeaderEnc('submitposter.php','post','multipart/form-data',"$Session");
	//assign initial values to textboxes
	//PrintTextbox('Filename','',50,"");
	//Caption the buttons appropriately
	PrintFile('file', 'file', true);
	PrintButton('submit', 'Upload', false);
	//End the form
	PrintFormFooter();
	//End the document
	EndDoc();
?>
