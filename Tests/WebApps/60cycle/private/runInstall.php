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

createHtaccess();
createHtpasswd($user, $pass);

$sqlUsername = $_POST['sqlUser'];
$sqlPassword = $_POST['sqlPass'];
$sqlDatabase = $_POST['sqlDB'];
$sqlServer = $_POST['sqlDBServer'];
$hasDatabase = $_POST['hasDatabase'] == 'on' ? true : false;
$entriesPerPage = $_POST['entriesPerPage'];
$enable_comments = $_POST['comments'] == 'Yes' ? true : false;
$enable_purifier = $_POST['purifier'] == 'Yes' ? true : false;
$enable_recaptcha = $_POST['recaptcha'] == 'Yes' ? true : false;
$publickey = $_POST['public'];
$privatekey = $_POST['private'];
$email_comments = $_POST['email'] == 'Yes' ? true : false;
$to = $_POST['to'];
$from = $_POST['from'];

createDatabase($sqlServer, $sqlUsername, $sqlPassword, $sqlDatabase, $hasDatabase);

createConfig($sqlServer, $sqlUsername, $sqlPassword, $sqlDatabase, $entriesPerPage, $enable_comments, $enable_purifier, $enable_recaptcha, $publickey, $privatekey, $email_comments, $to, $from);
echo "Installation complete!<br/>";
?>
<a href="index.php">Back to Dashboard</a>
</body>
</head>