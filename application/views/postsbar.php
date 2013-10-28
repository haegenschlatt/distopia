<?php
$auth = $this->Users->getAuth();
?>
		<div id="topBar">
<?php
if($auth && $thread == -1)
{
?>
			<a id='toggleCreateThread' href='#'>Create new thread &raquo;</a>
<?php
} else if($thread!=-1)
{
?>
			<a href="<?php echo base_url(); ?>board/<?php echo $name; ?>/" id="backToBoard">&laquo; back to [<?=$name;?>]</a>
<?php
}
?>
			<div id="userInfo">
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
					<a href="<?php echo base_url(); ?>board/<?php echo $name; ?>/">[<?php echo $name; ?>]</a> - <?php echo $description; ?>
				</h1>
			</div>

		</div>
<?php
if($auth)
{
?>
		<div id="createThread">
			<form action="<?php echo base_url(); ?>post/" method="post" enctype="multipart/form-data">
				<?php
				if($thread == -1)
				{
				?>
				<input type="text" id="title" name="title" placeholder="Title" size="50"/>
				<?php
				}
				?>
<?php if($thread!=-1)
{ ?>
				<input type="hidden" id="parent" name="parent" value="" />
<?php
}
?>
				<br />
				<textarea name="content" id="content" rows="8" cols="100" placeholder="Post text"></textarea>
				<br />
				<label for="file">Image: </label><small>Not required.</small>
				<input type="file" name="upload" id="upload" />
				<br />
<?php
if($thread!=-1)
{
?>
				<label for="sage">Don't bump thread</label>
				<input type="checkbox" name="sage" id="sage" value="y" />
				<input type="hidden" name="type" id="type" value="reply" />
				<br />
<?php
} else
{
?>
				<label for="type">Type</label>
				<select name="type" id="type">
					<option value="thread" selected>Thread</option>
					<option value="gallery">Gallery</option>
					<option value="stream">Stream</option>
				</select>
				<br />
<?php
}
?>
				<script>
				 var RecaptchaOptions = {
				    theme : 'clean'
				 };
				 </script>
				<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>" >
				</script>
				<noscript>
					<iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo CAPTCHA_PUBLIC_KEY; ?>"
					 height="300" width="500" frameborder="0"></iframe><br>
					<textarea name="recaptcha_challenge_field" rows="3" cols="40">
					</textarea>
					<input type="hidden" name="recaptcha_response_field"
					 value="manual_challenge">
				</noscript>
				<input id="submitButton" type="submit" value="Post!" />
				<input type="hidden" value="<?php echo $name; ?>" name="board"/>
				<input type="hidden" value="<?php echo $thread; ?>" name="thread"/>
				<input type="hidden" value="<?php echo current_url(); ?>" name="origin"/>
			</form>
		</div>
<?php
}
?>
		<div id="posts">
