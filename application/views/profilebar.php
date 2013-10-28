<?php
$auth = $this->Users->getAuth();
?>
		<div id="topbar">
			<div id="userinfo">
<?php
if(!$auth)
{
?>
Not logged in. <a href="<?=base_url();?>user/login">Log in</a>
<?php
} else
{
?>
Logged in as [<?=$auth["username"];?>] <a href="<?=base_url();?>user/logout">Log out</a>
<?php
}
?>
			</div>

			<div id="pageTitle">
				<h1>
					<a href="">[<?php echo $name; ?>]</a> - <?php echo $description; ?>
				</h1>
			</div>

		</div>
		<div id="posts">
