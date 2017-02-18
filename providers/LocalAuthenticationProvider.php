<?php

namespace Pails\Authentication;

class LocalAuthenticationProvider implements IAuthenticationProvider
{
	function validate($user_id, $hash)
	{
		$user = \User::find($user_id, array(
			'conditions' => array('password=? and active=1', $hash)
		));

		return ($user != null);
	}

	function getSession($session_key)
	{
		$session = \Session::find_by_session_id($session_key);
        if ($session == null) return null;
		return unserialize($session->session_data);
	}

    function getLoginUrl()
    {
        return '/session/login';
    }

	function redirectToLoginPage()
	{
		//This is never used on the local authentication provider
	}
}
