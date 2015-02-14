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

	public function activate($opts = array())
	{
		$this->model = array();

		//Get token param
		if(count($opts) > 0)
		{
				
				$token = $opts[0];
				
				if(!isset($token))
				{
					$this->model[] = lang("FORGOTPASS_INVALID_TOKEN");
				}
				else if(!validateactivationtoken($token)) //Check for a valid token. Must exist and active must be = 0
				{
					$this->model[] = "Token does not exist / This account is already activated";
				}
				else
				{
					//Activate the users account
					if(!setUseractive($token))
					{
						$this->model[] = lang("SQL_ERROR");
					}
				}
		}
		else
		{
			$this->model[] = lang("FORGOTPASS_INVALID_TOKEN");
		}
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