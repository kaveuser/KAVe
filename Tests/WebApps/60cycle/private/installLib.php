<?php
function createHtaccess()
{
	echo "Creating .htaccess file in private/...";
	$f = fopen(".htaccess", 'w') or die("Error: Can't create .htpasswd file!  Exiting");
		
	$currentDir = dirname(__FILE__);
	fwrite($f, "AuthType Basic\n") or die("Error: Couldn't write to .htaccess file!  Exiting...");
	fwrite($f, "AuthUserFile $currentDir/.htpasswd\n") or die("Error: Couldn't write to .htaccess file!  Exiting...");
	fwrite($f, "AuthName \"Site Administration\"\n") or die("Error: Couldn't write to .htaccess file!  Exiting...");
	fwrite($f, "require valid-user\n") or die("Error: Couldn't write to .htaccess file!  Exiting...");
	
	fclose($f);
	echo "success!<br/>";		
}

function createHtpasswd($user, $pass)
{
	echo "Creating .htpasswd file in private/...";
	$f = fopen(".htpasswd", 'w') or die("Error: Can't create .htpasswd file!  Exiting...");
	
	$cryptPass = crypt($pass, base64_encode($password));

	fwrite($f, "$user:$cryptPass") or die("Error: Couldn't write to .htpasswd file!  Exiting...");
	fclose($f);
	echo "success!<br/>";	
}

function createDatabase($sqlServer, $sqlUsername, $sqlPassword, $sqlDatabase, $existingDatabase)
{
	echo "Connecting to $sqlServer...";
	mysql_connect($sqlServer, $sqlUsername, $sqlPassword) or die('Error: Could not connect to MySQL server!  Exiting...');
	echo "success!<br/>";

	if (!$existingDatabase)
	{
		echo "Creating new database $sqlDatabase...";
		$query = "CREATE DATABASE `$sqlDatabase`";
		mysql_query($query) or die(mysql_error());
		echo "success!<br/>";
	}
	else
	{
		echo "Using existing database $sqlDatabase...<br/>";
	}	
	
	// connect to new DB
	echo "Selecting database $sqlDatabase...";
	@mysql_select_db($sqlDatabase) or die('Error: Could not select database!  Exiting...');
	echo "success!<br/>";

	// setup new table
	$queryArray = array();
	$queryArray[0] = 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"';
	$queryArray[1] = "CREATE TABLE IF NOT EXISTS `entries` (
	  `timestamp` int(10) unsigned NOT NULL,
	  `timezone` tinyint(4) NOT NULL,
	  `title` varchar(255) NOT NULL default 'Default Title',
	  `body` mediumtext NOT NULL,
	  UNIQUE KEY `title` (`title`),
	  KEY `timestamp` (`timestamp`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$queryArray[2] = "CREATE TABLE IF NOT EXISTS `comments` (
  `entry_id` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `author` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `text` text NOT NULL,
  KEY `timestamp` (`timestamp`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	echo "Creating tables in database $sqlDatabase...";
	foreach($queryArray as $query)
	{
		mysql_query($query) or die(mysql_error());
	}
	echo "success!<br/>";
	mysql_close();
}

function createConfig($sqlServer, $sqlUsername, $sqlPassword, $sqlDatabase, $entriesPerPage, $enable_comments, $enable_purifier, $enable_recaptcha, $publickey, $privatekey, $email_comments, $to, $from)
{
	$root = $_SERVER['DOCUMENT_ROOT'];
	$enable_comments = $enable_comments ? "true" : "false";
	$enable_purifier = $enable_purifier ? "true" : "false";
	$enable_recaptcha = $enable_recaptcha ? "true" : "false";
	$email_comments = $email_comments ? "true" : "false";
	echo "Attempting to create config.php file at webserver root...";
	if ($f = fopen("$root/config.php", 'w'))
	{ 
		fwrite($f, "<?php\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$username = '$sqlUsername';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$password = '$sqlPassword';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$database = '$sqlDatabase';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$sqlServer = '$sqlServer';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$entries_per_page = $entriesPerPage;\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$enable_comments = $enable_comments;\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$enable_htmlpurifier = $enable_purifier;\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$use_recaptcha = $enable_recaptcha;\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$publickey = '$publickey';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$privatekey = '$privatekey';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$email_comments = $email_comments;\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$to_addr = '$to';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "\$from_addr = '$from';\n") or die("Error:  Could not write to config.php!");
		fwrite($f, "?>") or die("Error:  Could not write to config.php!");
		fclose($f);
		echo "success!<br/>";
		echo "Attempting to move config.php to one level above website root...";
		if (!rename("$root/config.php", "$root/../config.php"))
		{
			echo("<br/><br/>**** WARNING: Could not move config.php to one level above website root.  Please do this MANUALLY *****<br/><br/>");
		}
		else
			echo("success!<br/>");
	}
	else
	{
		echo("<br/>**** Could not create config.php file!  You will have to do this MANUALLY.  See common/config.php for a template. ****<br/><br/>");
	}	
}
?>

<script type="text/javascript">
function checkForm(form)
{
	if (form.user.value.length <= 0) {
		alert("Enter a username");
	} else if ((form.pass.value != form.passConfirm.value) || form.pass.value.length <= 0) {
		alert("Desired passwords do not match!  Try again...");
	} else if ( (form.sqlDBServer.value.length <= 0) ||
				(form.sqlUser.value.length <= 0) ||
				(form.sqlPass.value.length <= 0) ||
				(form.sqlDB.value.length <= 0)) {
		alert("Please fill in all database information");
	} else if (!/^[1-9]{1}[0-9]*$/.test(form.entriesPerPage.value)) {
		alert("Enter a valid number for Entries per Page");
	} else if (form.comments[0].checked) {
		if (form.recaptcha[0].checked) {
			if (form.public.value.length <= 0) {
				alert("Please enter a RECAPTCHA public key");
				return;
			} else if (form.private.value.length <= 0) {
				alert("Please enter a RECAPTCHA private key");
				return;
			}
		}
		if (form.email[0].checked) {
			if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(form.to.value)) {
				alert("Enter a valid email address for the To: field");
				return;
			}
		}
		form.submit();
	}
	else
		form.submit();
}

function goToDB()
{
	document.location.href = "index.php"
}
</script>