<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->helper('timeago');
?>
<div class="singlePost reply <?=$id;?>"
<?php
if(isset($hierarchy)){ echo "style='margin-left:".($hierarchy*50)."px'"; } ?>
>


<div class="postInfo">
	<b><a href="<?=base_url();?>user/profile/<?=$username;?>"><?=$username;?></a></b> | <span title="<?=date("D d M Y, g:i:s A",$date);?>"><?=timeago($date);?></span> | <a href="#<?=$id;?>" name="<?=$id;?>">Link</a> | 
	In reply to <a href="<?=base_url()?>board/<?=$board;?>/thread/<?=$thread;?>#<?=$parent;?>">post <?=$parent;?></a>
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
