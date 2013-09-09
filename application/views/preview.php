<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->helper('timeago');
?>

<div class="singlepost preview">
	<?php
if($color!="none")
{
?>
<div style="width: 20px; height: 20px; background: #<?=$color;?>; float: left; margin-right: 10px;"></div>
<?php
}
?>
<div class="postinfo">
	<b><?=$name;?></b> | <?=timeago($date);?>
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