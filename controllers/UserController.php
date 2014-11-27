<?php
require_once(__DIR__.'/AuthControllerBase.php');

class UserController extends AuthControllerBase
{
	public function index()
	{
		if (!$this->is_logged_in())
		{
			header('Location: /session/login');
			exit();
		}
	}
	
	public function register()
	{
		//
	}
	
	public function update()
	{
		//
	}
}