<html>
<head><?php require 'installLib.php'; ?></head>
<body>
<h2>Install 60cycleCMS</h2>
<p>This page will assist you in installing (or re-installing) 60cycleCMS.  You must follow this process
before using the system.</p>
<p>Note:  You must have a MySQL server set up before running this install process.  Many
hosting providers will assist you with this process.</p>
<p>To get started, please fill in the following information:</p>
<form action="runInstall.php" method="post" name="installInfo">
<h3>Administration</h3>
Desired Username (for site administration): <input name="user" type="text" /><br />
Desired Password: <input name="pass" type="password"/><br />
Confirm Desired Password: <input name="passConfirm" type="password"/><br />
<h3>Database</h3>
MySQL Server (e.g. mysql.myserver.com): <input name="sqlDBServer" type="text" /><br />
MySQL Username: <input name="sqlUser" type="text" /><br />
MySQL Password: <input name="sqlPass" type="password" /><br />
<p>Some hosting providers (such as DreamHost) do not allow users to create
databases except through the hosting provider's interface.  If you have such 
a provider, or you already have an empty database setup on your server, check the box below.</p>
I have a database already: <input name="hasDatabase" type="checkbox" /><br/>
Name of new or existing database (e.g. my_database): <input name="sqlDB" type="text" /><br />
<h3>Configuration</h3>
How many posts/entries per page should 60cycleCMS display? (e.g. 3)
<input name="entriesPerPage" type="text" /><br/>
Would you like to allow visitors to leave comments?<br/>
<input name="comments" type="radio" value="Yes" checked="checked" /> Yes<br/>
<input name="comments" type="radio" value="No"  /> No<br />
<p>If you selected "No", there is no need to fill in the rest of the form.</p>
Enable HTML Purifier for visitor comments?</br>
NOTE:  This is HIGHLY recommended.  If enabled, this will only allow visitors to use the &lt;b&gt;, &lt;i&gt;, and &lt;a&gt; tags.
If not enabled, vistors can inject arbitrary HTML and Javascript into your pages.  See http://htmlpurifier.org/.<br/>
<input name="purifier" type="radio" value="Yes" checked="checked" /> Yes<br/>
<input name="purifier" type="radio" value="No"  /> No<br /><br />
Enable RECAPTCHA for visitor comments?<br/>
NOTE: This is HIGHLY recommended to thwart comment spam.  If enabled, visitors will have to pass the RECAPTCHA test before posting a comment.  To enable this option, 
you must already have a RECAPTCHA private and public key.  See http://recaptcha.net.<br/>
<input name="recaptcha" type="radio" value="Yes" checked="checked" /> Yes<br/>
<input name="recaptcha" type="radio" value="No"  /> No<br />
If yes, fill in your RECAPTCHA keys:<br/>
Public: <input name="public" type="text" size="40" /><br/>
Private: <input name="private" type="text" size="40" /><br/><br/>
Enable email notification of new comments?<br/>
<input name="email" type="radio" value="Yes" checked="checked" /> Yes<br/>
<input name="email" type="radio" value="No"  /> No<br />
If yes, specify the To: and From: fields in the email.  From: is not terribly important, but a valid address will help the comment
emails get through spam filters.  The To: field is where the comment notification will be delivered.<br/>
From: <input name="from" type="text" size="40" /><br/>
To: <input name="to" type="text" size="40" /><br/>
<br/>

<input type="button" value="Install" onclick="checkForm(this.form)" />
<input type="button" value="Back to Dashboard" onclick="javascript:goToDB()">
</form>
</body></html>