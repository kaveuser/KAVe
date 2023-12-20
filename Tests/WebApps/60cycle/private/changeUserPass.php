<html>
<head>
<?php
require 'installLib.php';
?>
</head>
<body>
<?php
$user = $_POST['user'];
$pass = $_POST['pass'];

createHtpasswd($user, $pass);
?>
<a href="index.php">Back to Dashboard</a>
</body>
</head>
