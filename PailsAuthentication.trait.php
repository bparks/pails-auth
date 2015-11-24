<?php
trait PailsAuthentication
{
	private $is_auth_initialized = false;

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
				if (!$this->current_user()->has_permission($perm))
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
		$this->init_authentication();

		if (!isset($_SESSION[AUTH_COOKIE_NAME]) || $_SESSION[AUTH_COOKIE_NAME] == NULL)
			return false;

		$user = User::find($_SESSION[AUTH_COOKIE_NAME]->user_id, array(
			'conditions' => array('password=? and active=1', $_SESSION[AUTH_COOKIE_NAME]->hash_pw)
		));

		if ($user)
			return true;
		else
		{
			//No result returned. kill the user session. user banned or deleted
			destroySession(AUTH_COOKIE_NAME);

			return false;
		}
	}

	protected function current_user()
	{
		if ($this->is_logged_in())
			return User::find($_SESSION[AUTH_COOKIE_NAME]->user_id);
		return null;
	}

	private function init_authentication()
	{
		if ($this->is_auth_initialized)
			return;

		$this->is_auth_initialized = true;

		$remember_me_length = 604800;
		if(isset($_SESSION[AUTH_COOKIE_NAME]) && is_object($_SESSION[AUTH_COOKIE_NAME])) {
			$loggedInUser = $_SESSION[AUTH_COOKIE_NAME];
		} elseif(isset($_COOKIE[AUTH_COOKIE_NAME])) {
			try
			{
				$session = Session::find($_COOKIE[AUTH_COOKIE_NAME]);
				$loggedInUser = unserialize($session->session_data);
			}
			catch (Exception $e)
			{
				//Really? Why kill the session?
				$loggedInUser = NULL;
				setcookie(AUTH_COOKIE_NAME, "", -$remember_me_length);
			}
		} else {
			$sessions = Session::find('all', array(
				'conditions' => array(time()." >= (session_start+".$remember_me_length.")")
			));
			foreach ($sessions as $session) {
				$session->delete();
			}
			$loggedInUser = NULL;
		}
		$_SESSION[AUTH_COOKIE_NAME] = $loggedInUser;
	}
}
