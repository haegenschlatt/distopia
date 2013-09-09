<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class DisFunctions {
	public function checkBan()
	{
		/*
		$CI =& get_instance();
		$ip = $_SERVER["REMOTE_ADDR"];
		$CI->load->database();
		$query = $CI->db->query("SELECT * FROM bans WHERE ip=? ORDER BY expire DESC",array($ip));
		if($query->num_rows()>0)
		{
			$result = $query->row_array();
			if($result["expire"]>time())
			{
				?>
	<h2>Banned</h2>
	<p>You are banned until <?php echo date("F d, Y",$result["expire"]); ?> for <?php echo $result["reason"]; ?>. Please come back then!</p>

				<?php
				exit();
			}
		}
		*/
	}
	
	public function checkArchive($thread)
	{
		// Returns true if the thread is in an archived state.
		$CI =& get_instance();
		$CI->load->database();
		$query = $CI->db->query("SELECT latest FROM posts WHERE id=? ORDER BY latest DESC",array($thread));
		if($query->num_rows()>0)
		{
			$results = $query->row_array();
			if((time()-21600)>$results["latest"] || $query->num_rows() > 100)
			{
				return true;
			} else
			{
				return false;
			}
		}
	}
}