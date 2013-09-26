<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->helper('timeago');
?>
<div class="singlepost original <?=$id;?>">

<div class="postinfo">
<?php
if($color!="none")
{
?>
<div style="width: 20px; height: 20px; background: #<?=$color;?>; float: left; margin-right: 10px;"></div>
<?php
}
?>	<b><a href="<?=base_url();?>user/profile/<?=$username;?>"><?=$username;?></a></b> | <?=timeago($date);?> 
	[<a class="clickToReply <?=$id;?>" href="#" onclick="document.getElementById('parent').value=<?=$id;?>">Reply</a>]
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