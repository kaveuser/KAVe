<?php
session_start();
require_once('lib/recaptchalib.php');
require_once('lib/htmlpurifier-4.0.0/HTMLPurifier.standalone.php');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/../config.php");

if ($use_recaptcha)
{
	$resp = recaptcha_check_answer ($privatekey,
									$_SERVER["REMOTE_ADDR"],
									$_POST["recaptcha_challenge_field"],
									$_POST["recaptcha_response_field"]);
}

$referrer = $_POST['referrer'];
if (get_magic_quotes_gpc())
{
	$name = stripslashes($_POST['name']);
	$email = stripslashes($_POST['email']);
	$comment = stripslashes($_POST['comment']);
  $negative_captcha = stripslashes($_POST['blah']);
}
else
{
	$name = $_POST['name'];
	$email = $_POST['email'];
	$comment = $_POST['comment'];
  $negative_captcha = $_POST['blah'];
}

if ($name == '' || $email == '' || $comment == '') exit;

$comment = trim($comment);

if ($enable_htmlpurifier)
{
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Cache.DefinitionImpl', null);
	$config->set('HTML.Allowed', '');
	$purifier = new HTMLPurifier($config);
	$name = $purifier->purify($name);	
	$email = $purifier->purify($email);

	$config2 = HTMLPurifier_Config::createDefault();
	$config2->set('Cache.DefinitionImpl', null);
	$config2->set('HTML.Allowed', 'b,i,a[href]');
	$purifier = new HTMLPurifier($config2);
	$comment = $purifier->purify($comment);
}

if ((!$resp->is_valid and $use_recaptcha) or ($use_recaptcha and !empty($negative_captcha)) ) {
	if (get_magic_quotes_gpc())
	{

		$comment = str_replace("\r\n","{:+++NEWLINE|||:}", $comment);
		$comment = str_replace(array("\n","\r"),"{:+++NEWLINE|||:}", $comment);
		$comment = addslashes($comment);
		$comment = str_replace("{:+++NEWLINE|||:}", "\\n", $comment);
	}
	else
	{
		$comment = str_replace("\r\n","{:+++NEWLINE|||:}", $comment);
		$comment = str_replace(array("\n","\r"),"{:+++NEWLINE|||:}", $comment);
		$comment = addslashes($comment);
		$comment = str_replace("{:+++NEWLINE|||:}", "\\n", $comment);
	}
	$_SESSION['name'] = $name;
	$_SESSION['email'] = $email;
	$_SESSION['comment'] = $comment;
  $_SESSION['blah'] = $negative_captcha;
	$_SESSION['result'] = "fail";	
	header("Location: $referrer");
}
else
{
	$_SESSION['result'] = "success";
	$comment = str_replace("\r\n","<br/>", $comment);
	$comment = str_replace(array("\n","\r"),"<br/>", $comment);
	
	preg_match('/post=(.*)#?/', $referrer, $matches);
	$entry_id = urldecode($matches[1]);
	
	// email comment
	if ($email_comments)
	{
		$headers = "From: 60cycleCMS <$from_addr>" . "\r\n" .
		'Reply-To: ' . $email . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
		mail($to_addr, 'Comment submitted for '.$entry_id, wordwrap("Name: $name\nEmail: $email\nComment: $comment", 70), $headers);
	}

	// connect to SQL db
	$db_connection = new mysqli($sqlServer, $username, $password, $database);
	if(mysqli_connect_errno()) {
      echo "Connection Failed: " . mysqli_connect_errno();
      exit();
	}

	if ($statement = $db_connection->prepare("INSERT INTO comments VALUES(?, ?, ?, ?, ?)"))
	{
		$statement->bind_param("sisss", $entry_id, time(), $name, $email, $comment);
		$statement->execute();
		if ($statement->affected_rows < 0)
		{
			echo mysqli_error($db_connection);
		}
		$statement->close();
		$db_connection->close();
		header("Location: $referrer");
	}
	else
	{
		echo mysqli_error($db_connection);
		$db_connection->close();
	}
		
}

?>
