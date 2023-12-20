<?php
	include '../common/lib.php';
	
	// get info submitted by html form in publishForm.php
	if (get_magic_quotes_gpc())
	{
    	$title = stripslashes($_POST['title']);
      	$body = str_replace(array("\n\r","\n","\r")," ", stripslashes($_POST['body']));
   	}
   	else
   	{
		$title = $_POST['title'];
		$body = str_replace(array("\n\r","\n","\r"),"", $_POST['body']);
    }
	$timestamp = $_POST['time'];
	$timezone = $_POST['timezone'];
	// add to session data
	$_SESSION['title'] = $title;
	$_SESSION['body'] = $body;
	$_SESSION['timestamp'] = $timestamp;
	$_SESSION['timezone'] = $timezone;
	
	if ($_SESSION['isFromEdit'] == 'true')
		$action = "edit.php";
	else
		$action = "publish.php";
?>

<h2>Post Preview:</h2>
<form action="" method="post">
<input type="button" value="Edit Post" onclick="submitForm(this)">
<input type="button" value="Submit Post" onclick="submitForm(this)">
</form>

<script type="text/javascript">
function submitForm(button)
{
	if (button.value == "Edit Post")
		button.form.action = "<?php echo $action ?>";
	else
		button.form.action = "submit.php";
		
	button.form.submit();
}

</script>
	
<?php	
	echo getEntryHTML($title, intval($timestamp), $timezone, $body, true);	
?>
