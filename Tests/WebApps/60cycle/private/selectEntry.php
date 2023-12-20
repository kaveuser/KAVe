<?php
	$action = $_GET['act'];
	echo "<h2>Select Entry to $action</h2>";

	require '../common/sqlConnect.php';
	
	$query = "SELECT `title` FROM `entries` ORDER BY timestamp DESC";
	
	// query MySQL server
	$result=mysql_query($query) or die('MySQL Query fail!');

	$numRows = mysql_num_rows($result);
	
	if ($action == 'Delete')
		echo '<form action="delete.php" method="post">';
	else if ($action == 'Edit')
		echo '<form action="edit.php" method="post">';		
		
	echo '<select size="15" name="entry">';
	echo '<option value="cancel" selected="selected">[Cancel]</option>';
	for ($i = 0; $i < $numRows; $i++)
	{
		$row = mysql_fetch_row($result);
		echo '<option value="' . urlencode($row[0]) . '">' . $row[0] . '</option>';
	}
	echo '</select><br />';
	if ($action == 'Delete')
		echo "<input type=\"button\" value=\"$action\" onclick=\"verifyDelete(this.form)\">";
	else if ($action == 'Edit')
		echo '<input type="button" value="Edit" onclick="verifyEdit(this.form)">';
	mysql_close();
?>
<input type="button" value="Back to Dashboard" onclick="javascript:goToDB()">
</form>
<script type="text/javascript">
function verifyDelete(form) {
	var index = form.entry.selectedIndex;
	var entry = form.entry.options[index].text;
	if (entry == "[Cancel]")
	{
		history.go(-1);
	}
	else
	{	
		var question = "Do you really want to delete " + entry + "?";
		var answer = confirm(question);
		
		if (answer == true)
		{
			form.submit();
		}
		else
		{
			return;
		}
	}
}
function verifyEdit(form) {
	var index = form.entry.selectedIndex;
	var entry = form.entry.options[index].text;
	if (entry == "[Cancel]")
	{
		history.go(-1);
	}
	else
	{	
		form.submit();
	}
}
function goToDB()
{
	document.location.href = "index.php"
}


</script>
<noscript>
<p>Please enable JavaScript to delete entries!</p>
</noscript>
