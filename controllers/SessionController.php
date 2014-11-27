<?php
require_once(__DIR__.'/AuthControllerBase.php');

class SessionController extends AuthControllerBase
{
	public function index()
	{
		//
	}
	
	public function login()
	{
		//
	}
	
	public function logout()
	{
		if($this->is_logged_in())
			$this->current_user()->userLogOut();
		header("Location: http://".$_SERVER['HTTP_HOST']);
		exit();
	}
}