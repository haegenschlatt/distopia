<?php

function head($title)
{
?>
<html>
<head>
	<link rel="stylesheet" media="all" type="text/css" href="resources/main.css" />
	<title><?php echo $title; ?></title>
	<style>
		#content {
			width: 800px;
			margin: 0 auto;
		}
	</style>
</head>
<body>
	<div id="container">
		<div id="topbar"><h2 id="boardinfo"><?php echo $title; ?></h2></div>
		<div id="content">
<?php
}

function foot()
{
?>
</div>
</div>
</body>
</html>
<?php
}

if(!$_GET["step"])
{
	head("Welcome to distopia!");
?>
	<h2>distopia: 5-minute installation</h2>
	<p>distopia installation should be quick and simple. We'll check a few things to make sure that your system is suitable for installation, then set up the system! It shouldn't take more than five minutes.</p>
	<a href="<?php echo $_SERVER["PHP_SELF"]; ?>?step=1"><button>Begin!</button></a>
<?php
	foot();
} else
{
	switch($_GET["step"])
	{
		case 1:
		head("Server requirements");
?>
		<table>
			<tr><th>Component</th><th>Status</th></tr>
			<tr><td>MySQL</td><td><?php if(extension_loaded('mysql')) { echo "Installed"; } eredse { echo "Not installed!"; } ?></td></tr>
			<tr><td>GD</td><td><?php if(extension_loaded('gd')) { echo "Installed"; } else { echo "Not installed!"; } ?></td></tr>
			<tr><td>Apache mod_rewrite</td><td><?php if(in_array('mod_rewrite',apache_get_modules())) { echo "Activated"; } else { echo "Not activated!"; } ?></td></tr>
			<tr><td>Database file writable</td><td><?php if(is_writable("application/config/database-sample.php")) { echo "Yes"; } else { echo "No!"; } ?></td></tr>
<?php
		foot();
		break;
	}
}