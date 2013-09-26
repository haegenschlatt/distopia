<?php
// Temporary (ugly) login page
?>
<html>
<head>
	<title>Login</title>
</head>
<body>
	<?php
	if(!is_null($message))
	{
	?>
		<p><?=$message;?></p>
	<?php
	}
	?>
	<form action="<?=base_url();?>user/auth" method="post">
		<label for="user">Username:</label>
		<input type="text" name="user" id="user" />
		<label for="pass">Password:</label>
		<input type="password" name="pass" id="pass"/>
		<input type="submit" value="Log in" />
	</form>
</body>