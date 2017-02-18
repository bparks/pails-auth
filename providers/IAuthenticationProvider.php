<?php

namespace Pails\Authentication;

interface IAuthenticationProvider
{
	function getSession($session_key);
	function validate($user_id, $hash);
    function getLoginUrl();
	function redirectToLoginPage();
}
