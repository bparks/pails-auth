<?php
	/*
		UserPie Version: 1.0
		http://userpie.com
	*/

	//General Settings
	//--------------------------------------------------------------------------

	
	$db_table_prefix = "userpie_";

	$websiteName = "";
	$websiteUrl = "";

	//Do you wish UserPie to send out emails for confirmation of registration?
	//We recommend this be set to true to prevent spam bots.
	//False = instant activation
	//If this variable is falses the resend-activation file not work.
	$emailActivation = true;
	
	//Tagged onto our outgoing emails
	$emailAddress = "sysadmin@synapsesoftware.com";
	
	//Date format used on email's
	$emailDate = date("l \\t\h\e jS");
	
	//Directory where txt files are stored for the email templates.
	$mail_templates_dir = "models/mail-templates/";
	
	$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
	$default_replace = array($websiteName,$websiteUrl,$emailDate);
	
	//Display explicit error messages?
	$debug_mode = false;
	
	//---------------------------------------------------------------------------
?>
