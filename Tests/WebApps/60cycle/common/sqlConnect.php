<?php

// include your sql info file here
$root = $_SERVER['DOCUMENT_ROOT'];
require "$root/../config.php";

// connect to sql server and select database
mysql_connect($sqlServer, $username, $password);
@mysql_select_db($database) or die('Achtung! No get database!');
?>
