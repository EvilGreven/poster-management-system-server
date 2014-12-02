<?	//Purpose:		This will handle authentication
  //We're doing it this way due to Android integration
	//Original Author:	Brett Jones
	//Creation Date:	9/14/2013
	//Modification Date:	11/18/2013
	//Modification Purpose:	Audit for Coding Convention exchange

	//Inclusions
	include "system.php";

	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{	//Connection failed, notify and abort
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);

//This function returns True if login:testuser and password:testpass are provided
//Otherwise it returns False
function CheckAccess($session)
{  
	$session=mysql_real_escape_string($session);
	$result=mysql_query("SELECT * FROM users WHERE Session='$session'");
	$count=mysql_num_rows($result);
	if($count==1) {
		return true;
	} else {  return false; }
} 
?>
