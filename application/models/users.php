<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	// Returns an array containg ["username"] and ["userid"] or false if not logged in
	function getAuth()
	{
		if(!$this->session->userdata("username") || !$this->session->userdata("userid"))
		{
			return false;
		} else
		{
			return array(
				"username" => $this->session->userdata("username"),
				"userid" => $this->session->userdata("userid")
				);
		}
	}
}