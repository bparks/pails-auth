<?php
	/*
		UserPie Version: 1.0
		http://userpie.com
		

	*/
	
	
	function usernameExists($username)
	{
		$user = User::find_by_username_clean(sanitize($username));
		return $user ? true : false;
	}
	
	function emailExists($email)
	{
		$user = User::find_by_email(sanitize($email));
		return $user ? true : false;	
	}
	
	function validateactivationtoken($token,$lostpass=NULL)
	{
		$user = User::find_by_activationtoken(trim($token));
		return $user != null && $user->active == ($lostpass != null);
	}
	
	
	function setUseractive($token)
	{
		$user = User::find_by_activationtoken(trim($token));
		$user->active = 1;
		$user->save();
		return true;
	}
	
	//You can use a activation token to also get user details here
	function fetchUserDetails($username=NULL,$token=NULL)
	{
		if($username!=NULL) 
		{
			return User::find_by_username_clean(sanitize($username));
		}
		else
		{
			return User::find_by_activationtoken(sanitize($token));
		}
	}
	
	function flagLostpasswordRequest($username,$value)
	{
		$user = User::find_by_username_clean(sanitize($username));
		$user->lostpasswordrequest = $value;
		$user->save();
		
		return true;
	}
	
	function updatepasswordFromToken($pass,$token)
	{
		$new_activation_token = generateactivationtoken();

		$user = User::find_by_activationtoken(sanitize($token));
		$user->activationtoken = $new_activation_token;
		$user->password = $pass;
		$user->save();
		
		return true;
	}
	
	function emailusernameLinked($email,$username)
	{
		$user = User::find_by_username_clean(sanitize($username));

		if ($user != null && $user->email == sanitize($email))
			return true;

		return false;
	}
	
	//This function should be used like num_rows, since the PHPBB Dbal doesn't support num rows we create a workaround
	function returns_result($sql)
	{
		global $db;
		
		$count = 0;
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
		  $count++;
		}
		
		$db->sql_freeresult($result);
		
		return ($count);
	}
	
	//Generate an activation key 
	function generateactivationtoken()
	{
		$gen;
	
		do
		{
			$gen = md5(uniqid(mt_rand(), false));
		}
		while(validateactivationtoken($gen));
	
		return $gen;
	}
	
	function updatelast_activation_request($new_activation_token,$username,$email)
	{
		$user = User::find_by_username_clean(sanitize($username));
		if ($user->email != sanitize($email))
			return false;

		$user->activationtoken = $new_activation_token;
		$user->last_activation_request = time();
		$user->save();
		
		return true;
	}
?>