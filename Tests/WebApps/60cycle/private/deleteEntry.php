<?php
$unluckyEntry = addslashes(urldecode($_POST['entry']));

require '../common/sqlConnect.php';

$query = "DELETE FROM `entries` WHERE title='$unluckyEntry'";

if (mysql_query($query))
	echo '<p>Entry Deleted! Return to <a href="index.php">Dashboard</a>.</p>';
else
{
	echo '<p>Error deleting entry.  Return to <a href="index.php">Dashboard</a>.</p>';
	echo '<p>MySQL Error: ' . mysql_error() . '</p>';
}
mysql_close();




?>
