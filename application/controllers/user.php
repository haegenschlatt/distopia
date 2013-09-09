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

	public function message()
	{
		
	}
}