<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Report script.

class Report extends CI_Controller {

	public function index()
	{
		if($this->input->get("p"))
		{
			$post = $this->input->get("p");
		} else
		{
			$post = "";
		}
?>
<h3>Report a post or thread</h3>
<p>If you see a post or thread that would be detrimental to the board, we encourage you to report it.</p>
<p>Reports can be for anything from a serious violation such as posting illegal content, to a minor incident such as an off-topic thread.</p>
<p>Submitting of frivolous or false reports will result in a ban. Your IP, <?php echo $_SERVER["REMOTE_ADDR"]; ?>, is submitted with this report.</p>
<form action="<?php echo base_url(); ?>report/send" method="post">
Post/thread number:
<br />
<input type="text" name="post" value="<?php echo $post; ?>"/>
<br />
Brief description of violation
<br />
<textarea name="description" cols="40" rows="5"></textarea>
<input type="submit" value="Submit report"</input>
</form>
<?php
	}

	public function send()
	{
		$this->checkBan();
		if(!($this->input->post()))
		{
			exit("Do not access this script directly.");
		}		
		$post = $this->input->post("post");
		$description = htmlentities($this->input->post("description"), ENT_QUOTES);
		$ip = $_SERVER["REMOTE_ADDR"];
		$time = time();
		$this->db->query("INSERT INTO reports VALUES('$post','$description','$ip','$time');");
		echo "Report submitted. You can now close this window.";
	}
}
?>
