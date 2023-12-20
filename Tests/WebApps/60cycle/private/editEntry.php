<?php

if (isset($_SESSION['title']))
	$haveEntry = true;
else
	$haveEntry = false;

if (!$haveEntry)
{
	$entryForEdit = addslashes(urldecode($_POST['entry']));
	require '../common/sqlConnect.php';

	$query = "SELECT `title`, `body`, `timestamp`, `timezone` FROM `entries` WHERE title='$entryForEdit'";
	$result=mysql_query($query) or die('MySQL Query fail!');
	$row = mysql_fetch_row($result);
	$title = $row[0];
	$body = $row[1];
	$time = $row[2];
	$timezone = $row[3];
	$_SESSION['origTitle'] = $title;
}
else
{
	$title = $_SESSION['title'];
	$body = $_SESSION['body'];
	$time = $_SESSION['timestamp'];
	$timezone = $_SESSION['timezone'];
}
$_SESSION['isFromEdit'] = 'true';
?>
<form action="preview.php" method="post" name="entryForm">
Title:  <br /><input type="text" size="80" name="title" value=""><br /><br />
Body (HTML allowed): <br /><textarea rows="20" cols="80" name="body" value=""></textarea><br /><br />
<input type="Submit" value="Preview Entry">
<input type="button" value="Cancel" onclick="javascript:history.go(-1)">
<input type="text" name="time" value="" style="display: none">
<input type="text" name="timezone" value="" style="display: none">
</form>
<script type="text/javascript">
	var title = "<?php echo addslashes($title); ?>";
	var body = "<?php echo addslashes($body); ?>";
	document.entryForm.body.value = body;
	document.entryForm.title.value = title;
	document.entryForm.time.value = "<?php echo $time; ?>";
	document.entryForm.timezone.value = "<?php echo $timezone; ?>";

</script>
<noscript>
<p>Turn on JavaScript please....</p>
</noscript>	

