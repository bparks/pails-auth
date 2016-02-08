<?php

namespace Pails\Authentication;

class RemoteAuthenticationProvider implements IAuthenticationProvider
{
	private $root_url;

	function __construct($options)
	{
		if (!isset($options['server']))
			throw new Exception("Option 'server' must be specified for a RemoteAuthenticationProvider");
		$this->root_url = $options['server'];
	}

	function validate($user, $hash)
	{
		//All of the validation is done by way of retrieving the session from the remote server
		return true;
	}

	function getSession($key)
	{
		$url = $this->root_url . '/session/get';
		$basic_auth = $key;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ; 
		curl_setopt($ch, CURLOPT_USERPWD, $basic_auth); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$data = curl_exec($ch);
		curl_close($ch);

		return json_decode($data);
	}

	function redirectToLoginPage()
	{
		header('Location: ' . $this->root_url . '/session/login?return_url=http'.'://'.$_SERVER['HTTP_HOST'].'/session/login');
	}
}