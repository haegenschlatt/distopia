<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if($type!="thread")
{
if($page!=0)
{
	$previous = $page-1;
?>
<a href="<?php echo base_url(); ?>board/<?php echo $board; ?>/page/<?php echo $previous; ?>">&laquo; Previous</a>
<?php	
}
$next = $page+1;
?>
<a href="<?php echo base_url(); ?>board/<?php echo $board; ?>/page/<?php echo $next; ?>">Next &raquo;</a>
<?php
}
?>
<div id="footerinfo">
<h2 style="font-weight: 100; display: inline;">[<a href='<?php echo base_url(); ?>'>distopia</a>]</h2>
<?php
$query = $this->db->query("SELECT name FROM boardmeta ORDER BY name ASC");
foreach($query->result() as $board)
{
	echo "[<a href='" . base_url() . "board/" . $board->name . "'>" . $board->name . "</a>] ";
}
?>
</div>
<?php // Close #posts ?>
</div>
<?php // Close #container ?>
</div>
</body>
</html>