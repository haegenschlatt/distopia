<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->helper('timeago');
$this->load->library("DisFunctions");
?>
<?php 
/*
if ($class =="originalpost" && $thread!=-1)
{
	$query = $this->db->query("SELECT thread FROM posts WHERE id=".$id);
	$results = $query->row_array();
	echo "<span style=".'"'."margin: 10px".'"'."><p>You are viewing an individual comment's thread. <a href='".base_url()."/board/".$board."/thread/".$results["thread"]."'>View the rest of the comments</a></p></span>";
}
*/
?>

<div class="singlepost <?php echo $class; 
if($class=="reply" || $class=="originalpost")
{
	echo " " . $id;
}
?>"
<?php


if(isset($hierarchy)){ echo "style='margin-left:".($hierarchy*50)."px'"; } ?>
>
<?php
if($class=="reply")
{
	echo "<a name='".$id."'></a>";
}
if($color!="none")
{
	echo '<div style="width: 20px; height: 20px; background: #'.$color.'; float: left; margin-right: 10px;"></div>';
}
echo "<div class='postinfo'>";
echo "<b>" . $name . "</b>" . " | " . timeago($date) . " | No." . $id;
if($class=="reply" || $class=="replypreview")
{
	echo " | In reply to <a href='" . base_url() . "board/" . $board . "/thread/" . $thread . "#" . $parent . "'>" . "post " . $parent . "</a>";
}
if(($class=="originalpost" || $class=="postpreview") && $this->disfunctions->checkArchive($id))
{
	echo " | Archived";
}

if($class=="postpreview")
{
	echo " [<a href='". base_url() . "board/" . $board . "/thread/" . $id . "'>View thread</a>] ";
}
if($class=="reply" || $class=="originalpost")
{
	echo " [<a class='clickToReply " . $id . "' href='#'" . ' onclick="document.getElementById('. "'parent'" . ').value=' . $id . '"'. " >Reply</a>] ";
}
echo " <span class='report'>[<a href='".base_url()."report?p=".$id."' target='_blank'>Report</a>]</span>";
// Closes "postinfo" div
echo "</div>";

if($image != 0)
{
	echo "<div class='postimage'><a class='postimg' href='". base_url() . "images/$image'><img src='".base_url()."images/thumbs/$image' /></a></div>";
}

echo $content;
if($class=="postpreview")
{
		$query = $this->db->query("SELECT * FROM posts WHERE thread=? AND board=?",array($id,$board));
		if($query->num_rows()>0)
		echo "<br /><small style='color:#888'>Latest replies to this thread: (" . $query->num_rows() ." total)</small>";
}
?>
</div>
