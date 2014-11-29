<?php
trait PailsAuthentication
{
	protected function require_login($redirect_url = '/session/login')
	{
		if (!$this->is_logged_in())
		{
			header('Location: '.$redirect_url);
			exit();
		}
	}

	protected function require_anonymous($redirect_url = '/')
	{
		if ($this->is_logged_in())
		{
			header('Location: '.$redirect_url);
			exit();
		}
	}

	protected function is_logged_in()
	{
		if (!isset($_SESSION["userPieUser"]) || $_SESSION["userPieUser"] == NULL)
			return false;

		$user = User::find($_SESSION["userPieUser"]->user_id, array(
			'conditions' => array('password=? and active=1', $_SESSION["userPieUser"]->hash_pw)
		));
		
		if ($user)
			return true;
		else
		{
			//No result returned. kill the user session. user banned or deleted
			$_SESSION["userPieUser"]->userLogOut();
		
			return false;
		}
	}

	protected function current_user()
	{
		if ($this->is_logged_in())
			return $_SESSION["userPieUser"];
		return null;
	}
}