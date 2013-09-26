<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	public function index()
	{
		// Redirect to profile for this user, or the login page if they're not logged in
		//$this->profile("user");
	}

	public function profile($user = null)
	{
		if(is_null($user))
		{
			show_404();
		} else
		{
			
		}
	}

	public function inbox()
	{
		
	}

	public function message()
	{
		
	}

	// Front-facing login page
	public function login($message = null)
	{
		$this->load->view("loginpage",array("message" => $message));
	}

	// POST login handler
	// Error messages are temporary.
	public function auth()
	{
		$username = $this->input->post("user");
		$passhash = sha1($this->input->post("pass"));
		$q = $this->db->query("SELECT id,username FROM users WHERE username=? AND passhash=?;",array($username,$passhash));
		if($q->num_rows() != 1)
		{
			exit("Invalid username or password.");
		}
		// If execution reaches here, we can guarantee username and id are available.
		// Reassign $username from the DB - just to be sure it's sanitary
		$username = $q->row_array()["username"];
		$userid = $q->row_array()["id"];
		// If we got this far, it should be valid
		$this->session->set_userdata(array("username"=>$username,"userid"=>$userid));
		echo "Apparent success";
	}
	
	// Logout page, redirects to front page
	public function logout()
	{
		$this->session->sess_destroy();
		header("Location: " . base_url());
	}

	public function test()
	{
		echo "Your session data: <br>";
		echo "Username: ";
		echo $this->session->userdata("username");
		echo "<br>";
		echo "userid: " ;
		echo $this->session->userdata('userid');
	}
}