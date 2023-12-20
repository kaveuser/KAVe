<?php
/**
 * If the installer informs you that it could not create the config.php file,
 * use this file as a template, fill in your own info, and copy it to one
 * level above your webserver root.
 */

// SQL info
$username = 'myuser';
$password = 'mypass';
$database = 'my_db';
$sqlServer = 'mysql.myserver.com';

// how many entries/posts to appear on each page, must be > 0
$entries_per_page = 3;

// enable comments? (true/false)
$enable_comments = true;

// enable HTML Purifier (http://htmlpurifier.org/) for visitor comments
// HIGHLY RECOMMENDED to prevent people from screwing with your website
$enable_htmlpurifier = true;

// use RECAPTCHA (http://recaptcha.net/) to verify humanity of commenters
// HIGHLY RECOMMENDED to prevent comment spam
$use_recaptcha = true;
$publickey = "my_public_key";
$privatekey = "my_private_key";

// email comments as they are posted?
$email_comments = true;
// not terribly important, but a legit email address will help the comment email get through spam detection
$from_addr = 'example@domain.com';
// where you want the comment emails delivered
$to_addr = 'me@domain.com';
?>
