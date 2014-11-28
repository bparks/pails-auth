<?php
class SessionController extends Pails\Controller
{
	use PailsAuthentication;

	public $before_actions = array(
		'require_login' => array('except' => array('login', 'logout')),
		'require_anonymous' => array('except' => array('index', 'logout'))
	);

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