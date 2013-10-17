<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->helper('timeago');
?>

<div class="singlepost preview">


<div class="threadInfo">
<?php
if($image != 0)
{
?>
	<div class="previewImage"><a href="<?=base_url();?>images/<?=$image;?>"><img src="<?=base_url();?>images/thumbs/<?=$image;?>" height="60px" width="60px" /></a></div>
<?php
}
?>
	<h3 class="threadTitle"><a href="<?=base_url();?>board/<?=$board;?>/thread/<?=$id;?>"><?=$title;?></a></h3>
</div>

<div class="threadMeta">
	<div class="threadMetaBlock">
		<h4>type</h4>
		<span><?=$type;?></span>
	</div>
	<div class="threadMetaBlock">
		<h4>posts/min</h4>
		<span><?=mt_rand(0,99);?></span>
	</div>
</div>
<?php
/*
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
*/ 
?>
</div>