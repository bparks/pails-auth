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

	protected function require_permission($permissions)
	{
		if (is_array($permissions))
		{
			\Pails\Application::log('This page requires '.implode(', ', $permissions).'.');
			foreach ($permissions as $perm)
			{
				if (!$this->current_user()->has_permission($permissions))
					return false;
			}
			return true;
		}
		else
		{
			\Pails\Application::log('This page requires '.$permissions.'.');
			return $this->current_user()->has_permission($permissions);
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
			return User::find($_SESSION["userPieUser"]->user_id);
		return null;
	}
}