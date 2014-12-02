<?	//Purpose:		This will handle all display functions
	//Original Author:	Brett Jones
	//Creation Date:	9/14/2013
	//Modification Date:	11/18/2013
	//Modification Purpose:	Audit for Coding Convention exchange
	//Modification Date:	11/23/2013
	//Modification Purpose:	Added functionality for file submission
	//Modification Date:	12/05/2013
	//Modification Purpose:	Patched a security hole with links

//Function BeginDoc lets us start a document easily
//$title is what will be displayed in the title bar of the window
function BeginDoc($title) {
	echo "<html><head>";
	echo "<title>$title</title>";
	echo "<link href=xampp.css rel=stylesheet type=text/css>";
	echo "</head><body>";
}

//Function EndDoc lets us end a document easily
function EndDoc() {
	echo "</body></html>";
}

//Function PrintBreak prints a break
function PrintBreak() {
	echo "<br>";
}

//Function PrintSpace prints a space
function PrintSpace() {
	echo "&nbsp;";
}

//Function PrintLink builds on PrintHref by sending session id
//$link is the actual link
//$session sends the generated session id
//Caption is what the link is displayed as
function PrintLink($link, $session, $caption) {
	$linkid = $link;
	$linkid = str_replace('.','', $linkid);
	echo "<form id=$linkid method=post action=$link>";
	echo "<input type=hidden name=Session value=$session></form>";
	echo "<a href=\"#\" onclick=\"$linkid.submit()\">$caption</a>";
	echo "<noscript> is blocked. Please enable scripts for security, use: ";
	PrintHref($link.'?Session='.$session,$caption);
	echo "</noscript>";
}

//Function PrintHref prints a link
//$link is the actual link
//Caption is what the link is displayed as
function PrintHref($link, $caption) {
	echo "<a onclick=\ href=$link>$caption</a>";
}

//Function PrintFormHeader sets up a form easily
//$action is where the form sends the user
//$method is get or post
//$session is the session ID
function PrintFormHeader($action,$method,$session) {
	echo "<table border=0 cellpadding=0 cellspacing=0>";
	echo "<form action=$action method=$method>";
	echo "<input type=hidden name=Session value='$session'>";
}

//Function PrintFormHeaderEnc sets up a form easily
//$action is where the form sends the user
//$method is get or post
//$encode is the encode type
//$session is the session ID
function PrintFormHeaderEnc($action,$method,$encode,$session) {
	echo "<table border=0 cellpadding=0 cellspacing=0>";
	echo "<form action=$action method=$method enctype=$encode>";
	echo "<input type=hidden name=Session value='$session'>";
}

//Function PrintFormFooter ends a form easily
function PrintFormFooter() {
	echo "</form>";
	echo "</table>";
}

//Function PrintTextbox prints a textbox with a preceding caption
//$caption can be used to describe the textbox, and will have : appended
//$name may differ from the $caption, which is the variable sent by the form
//$size is the number of characters across
//$value is the initial value of the textbox contents
function PrintTextbox($caption,$name,$size,$value) {
	if($name=="") $name=$caption;
	if($caption!="") $caption=$caption.':';
	echo "<tr><td>$caption</td><td><input type=text size=$size name=$name value='$value'></td></tr>";
} 

//Function PrintPasswordbox prints a Password (hidden text) textbox with a preceding caption
//$caption can be used to describe the textbox, and will have : appended
//$name may differ from the $caption, which is the variable sent by the form
//$size is the number of characters across
//$value is the initial value of the textbox contents
function PrintPasswordbox($caption,$name,$size,$value) {
	if($name=="") $name=$caption;
	if($caption!="") $caption=$caption.':';
	echo "<tr><td>$caption</td><td><input type=Password size=$size name=$name value='$value'></td></tr>";
} 

//Function PrintButton prints a button easily
//$type is the type of the buttom - reset, submit
//$value is the caption of the button
//$pre is for alignment with tables
function PrintButton($type,$value,$pre) {
	$prefix="";
	if($pre==true) $prefix="<tr><td></td><td>";
	echo "$prefix<input type=$type border=0 value='$value'>";
} 

//Function PrintButtonEx prints a button easily - auto submit type
//$name is the name of the buttom - used for $_REQUEST['name']
//$value is the value of the button - compares the above
//$caption is the button's caption
function PrintButtonEx($name, $value, $caption) {
  echo "<button name='$name' value='$value'>$caption</button>";
}

//Function PrintFile prints a File button easily
//$type is the type of the buttom - reset, submit
//$value is the caption of the button
//$pre is for alignment with tables
function PrintFile($type,$value,$pre) {
	$prefix="";
	if($pre==true) $prefix="<tr><td></td><td>";
	echo "$prefix<input type=$type name=$value id='$value'>";
} 

//Function PrintListHeader lets us easily start a table
function PrintListHeader() {
	$image = "img/blank.gif";
	echo "<table border=0 cellpadding=0 cellspacing=0>";
	echo "<tr bgcolor=#f87820>";
	echo "<td><img src=$image width=13 height=25></td>";
}

//Function PrintListFooter lets us easily end a table
function PrintListFooter() {
	$image = "img/blank.gif";
	echo "<tr valign=bottom>";
        echo "<td bgcolor=#fb7922 colspan=13><img src=$image width=1 height=8></td>";
	EndRow();
      echo "</table>";
}

//Function PrintListCaption is used to label columns in a table
//$caption is the label for a column
//$width is the width for a column
function PrintListCaption($caption, $width) {
	$image = "img/blank.gif";
	echo "<td class=tabhead><img src=$image width=$width height=6><br><b>$caption</b></td>";
}

//Function PrintListDivider is used to divide rows visually
function PrintListDivider() {
	$image = "img/blank.gif";
	$background = "img/strichel.gif";
	echo "<tr valign=bottom>";
	echo "<td bgcolor=#ffffff background=$background colspan=13><img src=$image width=1 height=1></td>";
	EndRow();
}

//function LinkedRowEntry prints a linkable row cell
//$link is the link
//$entry is the text to be put in the cell
//$color is an optional background color for the cell
function LinkedRowEntry($link, $entry, $color) {
	$span = "<span>";
	$ref = "<a>";
	if($color != "") $span = "<span class=$color>";
	if($link != "") $ref = "<a href=$link>";
	echo "<td class=tabval>$ref$span [$entry] </span></a></td>";
}

//function RowEntry prints a row cell
//$entry is the text to be put in the cell
//$color is an optional background color for the cell
function RowEntry($entry, $color) {
	$span = "<span>";
	if($color != "") $span = "<span class=$color>";
	echo "<td class=tabval>$span".htmlspecialchars($entry)."</span></td>";
}

//function RowButton prints a button as the row cell
//$type is the type of button
//$value is the text to be put in the cell
function RowButton($type,$value) {
	echo "<td class=tabval><input type=$type border=0 value='$value'></td>";
} 

//Function RowFormHeader lets us make part of a row into a form
//$action is where to send the user
//$method is get or post
//$session sends the session ID
function RowFormHeader($action,$method,$session) {
	echo "<form action=$action method=$method>";
	echo "<input type=hidden name=Session value='$session'>";
}

//Function RowFormFooter ends the row form
function RowFormFooter() {
	echo "</form>";
}

//function RowTextbox prints a textbox as the row cell
//$name is the variable to be sent with the form
//$size is the number of characters across
//$value is the text to be put in the cell
function RowTextbox($name,$size,$value) {
	echo "<td class=tabval><input type=text size=$size name=$name value='$value'></td>";
} 

//Start a row easily
function BeginRow($align) {
	$image = "img/blank.gif";
	if($align != "") echo "<tr valign=$align>";
	echo "<td class=tabval><img src=$image width=13 height=20></td>";
}

//End a row easily
function EndRow() {
	echo "</tr>";
}
?>
