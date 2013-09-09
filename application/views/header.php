<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
<!DOCTYPE html>
<html>
<head>
<title>[<?php echo $name; ?>] - <?php echo $description; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>resources/base.js"></script>
<link rel="stylesheet" media="all" type="text/css" 
href="<?php echo base_url(); ?>resources/main.css" />
</head>
<body>
<div id="container">
<div id="topbar">
<h2 id="boardinfo"><a href="<?php echo base_url(); ?>board/<?php echo $name; ?>/">[<?php echo $name; ?>] - <?php echo $description; ?></a>
</h2>
<?php
// If the header is appearing over the front page, $thread will be -1. Otherwise it will hold the number of the thread.
if($thread!=-1)
{
	echo "<span style='padding:0; margin:0; color:#777'>[thread #" . $thread . "]</span>";

	$query = $this->db->query("SELECT latest FROM posts WHERE id=?",array($thread));
	$result = $query->row_array();
} else
{
	echo "<a id='showCreatePost' href='#'>Create new thread</a>";
}
?>
<div id="createpost">
<a id="hideCreatePost" href="#">[Close]</a>
<form action="<?php echo base_url(); ?>post/" method="post" enctype="multipart/form-data">
<label for="name">Name: </label><small>Leave blank for Anonymous.</small>
<br />
<input type="text" name="name" id="name"/>
<?php if($thread!=-1)
{ ?>
<br />
<?php // This div must be set to display:none rather than setting the input to type hidden because JS needs to modify the value. ?>
<div style="display:none;">
<label for="parent"><small>Replying to post</label>
<input type="text" name="parent" id="parent" value="" /> </small>
</div>
<?php
}
?>
<br />
<textarea name="content" id="content" rows="8" cols="100"></textarea>
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
</div>
<div id="posts">
