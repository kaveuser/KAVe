<html>
<head>
<?php require 'installLib.php'; ?>
</head>
<body>
<h2>Change Username or Password</h2>
<p>Enter desired username and password.</p>
<form action="changeUserPass.php" method="post" name="changeUserPass">
Desired Username: <input name="user" type="text" /><br />
Desired Password: <input name="pass" type="password"/><br />
Confirm Desired Password: <input name="passConfirm" type="password"/><br />
<input type="button" value="Submit" onclick="checkForm(this.form)" />
<input type="button" value="Back to Dashboard" onclick="javascript:goToDB()">
</form>
</body>
</html>