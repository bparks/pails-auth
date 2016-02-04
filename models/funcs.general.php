<?php
	/*
		UserPie Version: 1.0
		http://userpie.com


	*/

	function sanitize($str)
	{
		return strtolower(strip_tags(trim($str)));
	}

	function isValidemail($email)
	{
		return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",trim($email));
	}

	function minMaxRange($min, $max, $what)
	{
		if(strlen(trim($what)) < $min)
		   return true;
		else if(strlen(trim($what)) > $max)
		   return true;
		else
		   return false;
	}

	//@ Thanks to - http://phpsec.org
	function generateHash($plainText, $salt = null)
	{
		if ($salt === null)
		{
			$salt = substr(md5(uniqid(rand(), true)), 0, 25);
		}
		else
		{
			$salt = substr($salt, 0, 25);
		}

		return $salt . sha1($salt . $plainText);
	}

	function getUniqueCode($length = "")
	{
		$code = md5(uniqid(rand(), true));
		if ($length != "") return substr($code, 0, $length);
		else return $code;
	}

	function errorBlock($errors)
	{
		if(!count($errors) > 0)
		{
			return false;
		}
		else
		{
			foreach($errors as $error) {
				echo '<p style="color:red">'.$error.'</p>';
			}
		/*
			echo "<ul>";
			foreach($errors as $error)
			{
				echo "<li>".$error."</li>";
			}
			echo "</ul>";
		*/
		}
	}

	function lang($key,$markers = NULL)
	{
		require(__DIR__."/lang/en.php");

		if($markers == NULL)
		{
			$str = $lang[$key];
		}
		else
		{
			//Replace any dyamic markers
			$str = $lang[$key];

			$iteration = 1;

			foreach($markers as $marker)
			{
				$str = str_replace("%m".$iteration."%",$marker,$str);

				$iteration++;
			}
		}

		//Ensure we have something to return
		if($str == "")
		{
			return ("No language key found");
		}
		else
		{
			return $str;
		}
	}

// Destroy the session data
// Remember-Me Hack v0.03
// <http://rememberme4uc.sourceforge.net/>
function destroySession($name)
{
	$token = isset($_COOKIE[$name]) ? $_COOKIE[$name] : $_SESSION[$name]->remember_me_sessid;

	//Clear pails-auth session
	if (isset($_SESSION[$name]->remember_me) && $_SESSION[$name]->provider == 'local' && $_SESSION[$name]->remember_me == 1) {
		$session = Session::find($token);
		$session->delete();
	}

	//Clear PHP session
	if(isset($_SESSION[$name])) {
		$_SESSION[$name] = NULL;
		unset($_SESSION[$name]);
	}

	//Clear the cookie
	if (isset($_COOKIE[$name])) {
		setcookie($name, "", time() - 604800, '/');
	}
}

// Update the session data
// Remember-Me Hack v0.03
// <http://rememberme4uc.sourceforge.net/>

function updateSessionObj()
{
    $newObj = serialize($_SESSION[AUTH_COOKIE_NAME]);
    $session = Session::find_by_session_id($_SESSION[AUTH_COOKIE_NAME]->remember_me_sessid);
    $session->session_data = $newObj;
    $session->save();
}

?>
