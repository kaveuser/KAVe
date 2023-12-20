<?php

	// get info submitted by html form in publishForm.php
	$title = addslashes($_SESSION['title']);
	$body = addslashes($_SESSION['body']);

	require '../common/sqlConnect.php';
	
	// get current timezone to use with entry timestamp
	$timezone = $_SESSION['timezone'];
	$timestamp = time();
	
	// insert the record for the entry
	if ($_SESSION['isFromEdit'] == 'true')
	{
		$origTitle = addslashes($_SESSION['origTitle']);
		$query = "UPDATE entries SET title='$title', body='$body' WHERE title='$origTitle'";
	}
	else
	{
		$query = "INSERT INTO entries VALUES ('$timestamp', '$timezone', '$title','$body')";
	}
	// if the query fails, post the entry so it doesn't disappear into the ether
	if (!mysql_query($query))
	{
		$_SESSION['fail'] = 'true';
		echo '<p>MySQL Error: ' . mysql_error() . '</p>';
		if ($_SESSION['isFromEdit'] == 'true')
			echo '<p>Click <a href="edit.php">continue</a> to try again.</p>';
		else
			echo '<p>Click <a href="publish.php">continue</a> to try again.</p>';
	}
	else
	{
		echo '<p>Database update success!</p>';
		echo '<a href="index.php">Return to Dashboard...</a>';
		session_unset();
		session_destroy();
	}
	
	mysql_close();
?>
