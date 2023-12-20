<?php
// important that this is included before anything is output to the browser
session_start();
require 'news.php'; 
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style/style.css" media="screen,projection" />
</head>

<body>
<?php
if ($entriesHTML != 'missing')
{
	require 'printEntries.php';
}
?>
</body>
</html>
