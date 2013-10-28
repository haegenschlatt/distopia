<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->helper('timeago');
?>
<div class="singlePost original <?=$id;?>">

<div class="postInfo">
<b><a href="<?=base_url();?>user/profile/<?=$username;?>"><?=$username;?></a></b> | created <span title="<?=date("D d M Y, g:i:s A",$date);?>"><?=timeago($date);?></span> 
<?php
if($this->Users->getAuth())
{
?>
	[<a class="clickToReply <?=$id;?>" href="#">Reply</a>]
<?php
}
?>
</div>
<?php
if($image != 0)
{
?>
<div class="postImage"><a href="<?=base_url();?>images/<?=$image;?>"><img src="<?=base_url();?>images/thumbs/<?=$image;?>" /></a></div>
<?php
}
?>
<div class="postContent">
	<?=$content;?>
</div>
</div>