<?php

require 'common/lib.php';
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/../config.php");

function writeNews($latestTimestampUnix, $earliestTimestampUnix, $enable_comments, $entriesPerPage)
{
	// if this is a vanilla index.php page set the time to present
	if ($latestTimestampUnix == '' && $earliestTimestampUnix == '')
		$earliestTimestampUnix = time();

	// get the timestamp of newest entry
	$query = "SELECT MAX(`timestamp`) FROM `entries`";
	$maxQuery = mysql_query($query) or die('MySQL Query fail!');
	$maxTimestamp = mysql_result($maxQuery,0);

	// get the timestamp of the oldest entry
	$query = "SELECT MIN(`timestamp`) FROM `entries`";
	$minQuery = mysql_query($query) or die('MySQL Query fail!');
	$minTimestamp = mysql_result($minQuery,0);

	if ($earliestTimestampUnix == '')
	{
		// a "Newer" link was clicked to get here
		$query="SELECT `title`,`timestamp`,`timezone`,`body` FROM `entries` WHERE timestamp > $latestTimestampUnix ORDER BY timestamp ASC
		LIMIT $entriesPerPage";		
	}
	else
	{
		// an "Older" link was clicked to get here
		$query="SELECT `title`,`timestamp`,`timezone`,`body` FROM `entries` WHERE timestamp < $earliestTimestampUnix ORDER BY timestamp DESC 
		LIMIT $entriesPerPage";	
	}

	// query MySQL server
	$result=mysql_query($query) or die('MySQL Query fail!');
	
	$numRows = mysql_num_rows($result);
	if ($numRows == 0)
	{
		// zero row result, meaning no entry with that title exists
		// send a 404 header
		header("HTTP/1.0 404 Not Found"); 
		require 'missing.php';
		return 'missing';		
	}

	global $etsu, $ltsu;

	if ($earliestTimestampUnix == '')
	{
		// from "Newer" link
		$i = 0;
		while ($i < $numRows)
		{
			$row = mysql_fetch_row($result);
			$temp = getEntryHTML($row[0], $row[1], $row[2], $row[3], false);
			if ($enable_comments) $temp .= getCommentsLine($row[0]);
			$entriesHTML = $temp . $entriesHTML;
			$i++;
		}
		$etsu = mysql_result($result, 0, 'timestamp');
		$ltsu = mysql_result($result, $numRows - 1, 'timestamp');		
	}
	else
	{
		// from "Earlier" link
		$i = 0;
		while ($i < $numRows)
		{
			$row = mysql_fetch_row($result);
			$temp = getEntryHTML($row[0], $row[1], $row[2], $row[3], false);
			if ($enable_comments) $temp .= getCommentsLine($row[0]);
			$entriesHTML .= $temp;
			$i++;
		}
		$etsu = mysql_result($result, $numRows - 1, 'timestamp');
		$ltsu = mysql_result($result, 0, 'timestamp');	
	}	

	// are we at the first or last pages of entries?
	global $atEarliestPost;
	$atEarliestPost = ($etsu == $minTimestamp);
	global $atLatestPost;
	$atLatestPost = ($ltsu == $maxTimestamp);
	return $entriesHTML;

}

// just print out one post (this is from a post heading link)
function writePost($postName, $enable_comments, $use_recaptcha, $publickey, $sqlServer, $username, $password, $database)
{
	
	if (get_magic_quotes_gpc())
   	{
   		$postName = stripslashes($postName);
    }
	
	// connect to SQL db
	$db_connection = new mysqli($sqlServer, $username, $password, $database);
	if(mysqli_connect_errno()) {
      echo "Connection Failed: " . mysqli_connect_errno();
      exit();
	}

	$postName = $db_connection->real_escape_string($postName);
	
	if ($result = $db_connection->query("SELECT `title`,`timestamp`,`timezone`,`body` FROM `entries` WHERE title='$postName'")) {
    if ($result->num_rows != 1) {
      header("HTTP/1.0 404 Not Found"); 
      require 'missing.php';
      return 'missing';
    }

		$row = $result->fetch_row();
		$entriesHTML = getEntryHTML($row[0], $row[1], $row[2], $row[3], false);
		if ($enable_comments) $entriesHTML .= getEntryComments($row[0]) . getCommentForm($use_recaptcha, $publickey);
		// here's a handy string with the entry title... use as you wish
		global $entryTitle;
		$entryTitle = $row[0];
		return $entriesHTML;
	}
	else
	{
		// zero row result, meaning no entry with that title exists
		// send a 404 header
		header("HTTP/1.0 404 Not Found"); 
		require 'missing.php';
		return 'missing';
	}

}		

// get params passed in URL
$ltsu = $_GET["ltsu"];
$etsu = $_GET["etsu"];
$post = $_GET["post"];

require 'common/sqlConnect.php';

if ($post != '')
{
	$post = urldecode($post);
	if (isValidPostName($post))
	{
		// followed an entry link to get here, only print one entry
		$entriesHTML = writePost($post, $enable_comments, $use_recaptcha, $publickey, $sqlServer, $username, $password, $database);
	}
	else
	{
		header("HTTP/1.0 404 Not Found"); 
		require 'missing.php';
		$entriesHTML = 'missing';
	}		
}
else
{
	if ((isValidUnixTimestamp($ltsu) and $etsu == '')
		or (isValidUnixTimestamp($etsu) and $ltsu == '')
		or ($ltsu == '' and $etsu == ''))
	{
		// default, print normal number of entries
		$entriesHTML = writeNews($ltsu, $etsu, $enable_comments, $entries_per_page);
	}
	else
	{
		header("HTTP/1.0 404 Not Found"); 
		require 'missing.php';
		$entriesHTML = 'missing';
	}
}

mysql_close();
?>
