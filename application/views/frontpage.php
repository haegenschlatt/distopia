<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<html>
<head>
<link rel="stylesheet" media="all" type="text/css" href="<?php echo base_url(); ?>resources/main.css" />
<title>[distopia] - community is addicting</title>
<style>
p {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 13px;
	line-height: 23px;
}
a {
	text-decoration: none;
	color: #1292BA;
}
.section {
	width: 800px;
	margin: 0 auto;
}
.section h2{
	font-weight: 100;
}
.section ul {
	list-style-type: none;
	margin: 0;
	padding: 0;
}
.section ul h4 {
	color: #999;
}
.section ul a:hover {
	text-decoration: underline;
}
</style>
</head>
<body>
<div id="container">
<div id="topbar">
<h2 id="boardinfo">[distopia]
</h2>
<?php
$query = $this->db->query("SELECT name,description FROM boardmeta ORDER BY name ASC");
foreach($query->result() as $board)
{
	echo "[<a href='" . base_url() . "board/" . $board->name . "' title='".$board->description."'>" . $board->name . "</a>] ";
}
?>
</div>
<div id="content">
<div class="section">
<h2>distopia</h2>
<p>distopia is a discussion site designed to allow active, interesting, and raw discussion. Posting is anonymous, and there is no "karma" or "point" system to work for, so you can truly say what you believe, not what everyone else wants to hear. For more information, read the <a href="help.html">help page</a> and <a href="rules.html">rule list</a>.</p>
</div>
<div class="section">
<h2>boards</h2>
<ul>
<li><h4>General</h4>
<?php
$query = $this->db->query("SELECT name,description FROM boardmeta ORDER BY name ASC");
foreach($query->result() as $board)
{
	echo "<li><a href='" . base_url() . "board/" . $board->name . "' title='".$board->description."'>[" . $board->name . "] - " . $board->description . "</a> ";
}
?>
</ul>
</div>
</div>
<div class="section" id="posts" style="margin: 0 auto;">
<h2>recent threads:</h2>
<?php
$query = $this->db->query("SELECT * FROM posts WHERE thread = -1 ORDER BY latest DESC LIMIT 5");
if($query->num_rows()>0)
{
	foreach($query->result_array() as $postdata)
	{
		//	The only difference between different types of posts are the places they are used:
		//	OP on the front page, reply previews on the front page, the OP in the thread, and replies in the thread.
		//	Thus, the only thing that changes is the styling (and, for OPs on the front page, logic that shows the number of replies.)
		//	So we just pass the desired CSS class to the view.
		?>
		<h4>Thread <?php echo $postdata["id"]; ?> on [<?php echo $postdata["board"]; ?>]</h4>
		<?php
		$postdata['class'] = "postpreview";
		//	Load in the post preview.
		$this->load->view("showpost",$postdata);
		
	}
}
?>
</div>
</body>
</html>