<?php

if (! isset($_SESSION['title'], $_SESSION['body']) )
{
	$newSession = true;
}
// get title and body if this was a failed post
$title = $_SESSION['title'];
$body = $_SESSION['body'];

		if (! $newSession)
		{
			if ($_SESSION['fail'] == 'true')
			{
				echo '<p>Post failed, try again:</p>';
			}
			else
			{
				echo '<h2>Edit Entry</h2>';
			}
		}
		else
		{
			echo '<h2>New Entry</h2>';
		}    
?>
<form action="preview.php" method="post" name="entryForm">
Title:  <br /><input type="text" size="80" name="title" value=""><br /><br />
Body (HTML allowed): <br /><textarea rows="20" cols="80" name="body" value=""></textarea><br /><br />
<input type="Submit" value="Preview Entry" onclick="getTime()">
<input type="button" value="Back to Dashboard" onclick="javascript:goToDB()">
<input type="text" name="timezone" value="" style="display: none">
</form>
<script type="text/javascript">
	var bodyText = "<?php echo addslashes($_SESSION['body']); ?>";
	document.entryForm.body.value = bodyText;
	var titleText = "<?php echo addslashes($_SESSION['title']); ?>";
	document.entryForm.title.value = titleText;
	
	function getTime()
	{
		var tzo=(new Date().getTimezoneOffset()/60); 
		document.entryForm.timezone.value = tzo;
	}
	
	function goToDB()
	{
		document.location.href = "index.php"
	}
</script>
<noscript>
	<p>Your previous failed entry was not filled in to the form because Javascript is not enabled in this browser.</p>
	<p>Here's the body of your post (you might try copy/paste here....):</p>
	<p><?php echo $_SESSION['body']; ?></p>
</noscript>	

