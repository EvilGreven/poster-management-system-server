<?	//Purpose:		This will handle session generation
  //It is being done this way due to Android integration
	//Original Author:	Brett Jones
	//Creation Date:	9/14/2013
	//Modification Date:	11/18/2013
	//Modification Purpose:	Audit for Coding Convention exchange

	//Inclusions
	include "system.php";
	include "rand.php";

	//Attempt to connect to MySQL
	if(!mysql_connect($TEXT['database_address'],$TEXT['database_user'],$TEXT['database_password']))
	{	//Connection failed, notify and abort
		echo "<h2>Error connecting to database!</h2>";
		die();
	}
	//Connect to the database
	mysql_select_db($TEXT['database_name']);

	//This function returns a new session key and edits the user session
	function NewSessionKey($session)
	{  
		$session=mysql_real_escape_string($session);
		$NewSession = GetRandID(20);
		mysql_query("UPDATE users SET Session = REPLACE(Session, '$session', '$NewSession')");
		return $NewSession;
	} 
?>
