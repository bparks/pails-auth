<?php
class UserController extends Pails\Controller
{
	use PailsAuthentication;

	public $before_actions = array(
		'require_login' => array('except' => array('register')),
		'require_anonymous' => array('except' => array('index', 'update'))
	);

	public function index()
	{
		//
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