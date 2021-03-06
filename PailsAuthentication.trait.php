<?php
trait PailsAuthentication
{
	private $is_auth_initialized = false;

	protected function require_login($redirect_url = '/session/login')
	{
		if (!$this->is_logged_in())
		{
            $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
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
			//\Pails\Application::log('This page requires '.implode(', ', $permissions).'.');
			foreach ($permissions as $perm)
			{
				if (!$this->current_user()->has_permission($perm))
                {
					header('Location: /session/unauthorized');
                    exit();
                }
			}
			return true;
		}
		else
		{
			//\Pails\Application::log('This page requires '.$permissions.'.');
			if (!$this->current_user()->has_permission($permissions))
            {
                header('Location: /session/unauthorized');
                exit();
            }
            return true;
		}
	}

	protected function is_logged_in()
	{
		$this->init_authentication();

		if (!isset($_SESSION[AUTH_COOKIE_NAME]) || $_SESSION[AUTH_COOKIE_NAME] == NULL)
			return false;

		return true;
	}

	protected function current_user()
	{
		if ($this->is_logged_in()) {
			$providers = PailsAuth::getProviders();
			if (isset($providers['local']) && $_SESSION[AUTH_COOKIE_NAME]->provider_name == 'local')
				return User::find($_SESSION[AUTH_COOKIE_NAME]->user_id);
            if (PailsAuth::shouldMergeLocalUsers() && $_SESSION[AUTH_COOKIE_NAME]->email != '') {
                $user = User::find_by_email($_SESSION[AUTH_COOKIE_NAME]->email);
                if ($user != null) {
                    //\Pails\Application::log('Found local user ID ' . $user->user_id);
                    $user->provider_name = $_SESSION[AUTH_COOKIE_NAME]->provider_name;
                    $user->provider = $_SESSION[AUTH_COOKIE_NAME]->provider;
                    return $user;
                }

                if (PailsAuth::shouldCreateLocalUsers()) {
                    $user = User::create([
                        //'user_id' => CREATED AUTOMATICALLY
                        'username' => $_SESSION[AUTH_COOKIE_NAME]->email,
                        'username_clean' => strtolower(trim($_SESSION[AUTH_COOKIE_NAME]->email)),
                        'password' => $_SESSION[AUTH_COOKIE_NAME]->provider_name . ':' . $_SESSION[AUTH_COOKIE_NAME]->user_id,
                        'email' => $_SESSION[AUTH_COOKIE_NAME]->email,
                        'activationtoken' => md5(time()),
                        'last_activation_request' => time(),
                        'LostpasswordRequest' => 0,
                        'active' => 1,
                        'group_id' => 1,
                        'sign_up_date' => time(),
                        'last_sign_in' => time(),
                    ]);
                    //\Pails\Application::log('Created local user ID ' . $user->user_id);
                    $user->provider_name = $_SESSION[AUTH_COOKIE_NAME]->provider_name;
                    $user->provider = $_SESSION[AUTH_COOKIE_NAME]->provider;
                    return $user;
                }
            }
			return new \Pails\Authentication\WrappedUser($_SESSION[AUTH_COOKIE_NAME]);
		}
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
			$loggedInUser->provider = PailsAuth::getProvider($loggedInUser->provider_name);
		} elseif(isset($_COOKIE[AUTH_COOKIE_NAME])) {
			try
			{
				foreach (PailsAuth::getProviders() as $key => $provider) {
					$loggedInUser = $provider->getSession($_COOKIE[AUTH_COOKIE_NAME]);
					if ($loggedInUser != null) {
						$loggedInUser->provider_name = $key;
						$loggedInUser->provider = $provider;
						break;
					}
				}
			}
			catch (Exception $e)
			{
				\Pails\Application::log($e->getMessage());
				//Really? Why kill the session?
				$loggedInUser = NULL;
				setcookie(AUTH_COOKIE_NAME, "", -$remember_me_length);
			}
		} else {
			//IF LOCAL IS NOT A REGISTERED PROVIDER
			$providers = PailsAuth::getProviders();
			if (isset($providers['local'])) {
				$sessions = Session::find('all', array(
					'conditions' => array(time()." >= (session_start+".$remember_me_length.")")
				));
				foreach ($sessions as $session) {
					$session->delete();
				}
			}
			$loggedInUser = NULL;
		}

		if ($loggedInUser != null) {
			if (!is_subclass_of($loggedInUser->provider, '\\Pails\\Authentication\\IAuthenticationProvider') || !$loggedInUser->provider->validate($loggedInUser->user_id, $loggedInUser->password)) {
				$loggedInUser = NULL;
				setcookie(AUTH_COOKIE_NAME, "", -$remember_me_length);
			}

			$_SESSION[AUTH_COOKIE_NAME] = $loggedInUser;
		}
	}
}
