<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

function checkBan()
{
	$ip = $_SERVER["REMOTE_ADDR"];
	$query = $this->db->query("SELECT * FROM bans WHERE ip='".$ip."' ORDER BY expire DESC");
	if($query->num_rows()>0)
	{
		$result = $query->row_array();
		if($result["expire"]>time())
		{
			?>
<h2>banned</h2>
<p>You have been banned until <?php echo date("F d, Y",$result["expire"]); ?> for <?php echo $result["reason"]; ?>. Please come back then!</p>

			<?php
			exit();
		}
	}
}