<?php
class AccountController extends Pails\Controller
{
	use PailsAuthentication;

	public $before_actions = array(
		'require_login' => array('except' => array('activate', 'forgot', 'resend')),
		'require_anonymous' => array('except' => array('index', 'password'))
	);

	public function index()
	{
		//
	}

	public function activate()
	{
		//
	}
	
	public function forgot()
	{
		//
	}
	
	public function password()
	{
		//
	}
	
	public function resend()
	{
		//
	}
}