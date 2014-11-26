<?php 
	/*
		UserPie Version: 1.0
		http://userpie.com
		

	*/
	require_once("models/config.php");

	/*
	* Uncomment the "else" clause below if e.g. userpie is not at the root of your site.
	*/
	if (!isUserLoggedIn())
		header('Location: login.php');
	else
		header('Location: /');