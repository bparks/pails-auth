<?php

class LocalAuthenticationProvider implements \Pails\Authentication\IAuthenticationProvider
{
	function validate($user_id, $hash)
	{
		$user = User::find($user_id, array(
			'conditions' => array('password=? and active=1', $hash)
		));

		return ($user != null);
	}

	function getSession($session_key)
	{
		$session = Session::find($session_key);
		return unserialize($session->session_data);
	}

	function redirectToLoginPage()
	{
		//This is never used on the local authentication provider
	}
}