<?php
class SessionController extends Pails\Controller
{
	use PailsAuthentication;

	public $before_actions = array(
		'require_login' => array('except' => array('login', 'logout', 'extend', 'get')),
		'require_anonymous' => array('except' => array('index', 'login', 'logout', 'unauthorized', 'get'))
	);

	public function index()
	{
		//
	}

    public function unauthorized()
    {
        return $this->view();
    }

	public function login()
	{
		$providers = PailsAuth::getProviders();

		if (isset($providers['local']) && $this->is_logged_in() && isset($_REQUEST['return_url']))
			header('Location: ' . $_REQUEST['return_url'] . '?token=' . $_SESSION[AUTH_COOKIE_NAME]->remember_me_sessid);

		if (isset($_REQUEST['token'])) {
			$token = $_REQUEST['token'];
			setcookie(AUTH_COOKIE_NAME, $token, time()+604800, '/');

            if (isset($_SESSION['return_url'])) {
                $return_url = $_SESSION['return_url'];
                unset($_SESSION['return_url']);
                return $this->redirect($return_url);
            } else {
                return $this->redirect("/");
            }
		}

		if(!empty($_POST))
		{
			$errors = array();
			$username = trim($_POST["username"]);
			$password = trim($_POST["password"]);
			$remember_choice = isset($_POST['remember_me']) || isset($_SESSION['return_url']) || isset($_REQUEST['return_url']) ? 1 : 0;

			//Perform some validation
			//Feel free to edit / change as required
			if($username == "")
			{
				$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
			}
			if($password == "")
			{
				$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
			}

			//End data validation
			if(count($errors) == 0)
			{
				//A security note here, never tell the user which credential was incorrect
				if(!usernameExists($username))
				{
					$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
				}
				else
				{
					$userdetails = fetchUserDetails($username);

					//See if the user's account is activation
					if($userdetails->active==0)
					{
						$errors[] = lang("ACCOUNT_INACTIVE");
					}
					else
					{
						//Hash the password and use the salt from the database to compare the password.
						$entered_pass = generateHash($password,$userdetails->password);

						if($entered_pass != $userdetails->password)
						{
							//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
							$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
						}
						else
						{
							//passwords match! we're good to go'

							//Construct a new logged in user object
							//Transfer some db data to the session object
							$loggedInUser = User::find($userdetails->user_id);
							$loggedInUser->hash_pw = $userdetails->password;
							$loggedInUser->remember_me = $remember_choice;
							$loggedInUser->remember_me_sessid = generateHash(uniqid(rand(), true));
							//$loggedInUser->provider_name = 'local';

							//Update last sign in
							$loggedInUser->updatelast_sign_in();

							$_SESSION[AUTH_COOKIE_NAME] = $loggedInUser;
							if($loggedInUser->remember_me == 1) {
								$session = new Session(array(
									"session_start" => time(),
									"session_data" => serialize($loggedInUser),
									"session_id" => $loggedInUser->remember_me_sessid
								));
								$session->save();
								setcookie(AUTH_COOKIE_NAME, $loggedInUser->remember_me_sessid, time()+604800, '/');
							}

							//Redirect to user account page
							if (isset($_SESSION['return_url'])) {
                                $return_url = $_SESSION['return_url'];
                                unset($_SESSION['return_url']);
								header('Location: ' . $return_url . '?token=' . $loggedInUser->remember_me_sessid);
							} else {
								header("Location: /");
                            }
						}
					}
				}
			}

			$this->flashNow('error', '<ul>' . implode('', array_map(function ($item) { return '<li>' . $item . '</li>'; }, $errors) . '</ul>'));
		}

		if (count($providers) == 1 && !isset($providers['local']))
		{
			$values = array_values($providers);
			$values[0]->redirectToLoginPage();
		}

		if (isset($_REQUEST['return_url']))
			$_SESSION['return_url'] = $_REQUEST['return_url'];

        $this->model = $providers;
        return $this->view();
	}

	public function get()
	{
		$providers = PailsAuth::getProviders();
		if (!isset($providers['local'])) {
			return $this->notFound();
		}
		if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'basic') !== 0) {
			return $this->notFound();
		}
		$session = Session::find(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
		return $this->content(unserialize($session->session_data)->to_json());
	}

	public function logout()
	{
		if($this->is_logged_in()) {
            $provider_name = $this->current_user()->provider_name;
			destroySession(AUTH_COOKIE_NAME);
            if ($provider_name == 'google') {
                return $this->view();
            }
		}
		return $this->redirect("http://".$_SERVER['HTTP_HOST']);
	}
}
