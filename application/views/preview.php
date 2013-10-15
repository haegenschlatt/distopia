<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->helper('timeago');
?>

<div class="singlepost preview">

<div class="postinfo">
	<b><a href="<?=base_url();?>user/profile/<?=$username;?>"><?=$username;?></a></b> | created <span title="<?=date("D d M Y, g:i:s A",$date);?>"><?=timeago($date);?></span> | updated <span title="<?=date("D d M Y, g:i:s A",$latest);?>"><?=timeago($latest);?></span>
	[<a href="<?=base_url();?>board/<?=$board;?>/thread/<?=$id;?>">View thread</a>]
</div>
<?php
if($image != 0)
{
?>
<div class="postimage"><a href="<?=base_url();?>images/<?=$image;?>"><img src="<?=base_url();?>images/thumbs/<?=$image;?>" /></a></div>
<?php
}
?>
<div class="postcontent">
<?=$content;?>
</div>
</div>