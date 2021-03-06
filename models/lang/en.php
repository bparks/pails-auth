<?php

	/*
		%m1% - Dymamic markers which are replaced at run time by the relevant index.
	*/

	$lang = array();

	//Account
	$lang = array_merge($lang,array(
		"ACCOUNT_SPECIFY_USERNAME" 				=> "Please enter your username",
		"ACCOUNT_SPECIFY_PASSWORD" 				=> "Please enter your password",
		"ACCOUNT_SPECIFY_EMAIL"					=> "Please enter your email address",
		"ACCOUNT_INVALID_EMAIL"					=> "Invalid email address",
		"ACCOUNT_INVALID_USERNAME"				=> "Invalid username",
		"ACCOUNT_USER_OR_EMAIL_INVALID"			=> "Username or email address is invalid",
		"ACCOUNT_USER_OR_PASS_INVALID"			=> "Username or password is invalid",
		"ACCOUNT_ALREADY_ACTIVE"				=> "Your account is already activated",
		"ACCOUNT_INACTIVE"						=> "Your account is inactive. Check your email for account activation instructions",
		"ACCOUNT_USER_CHAR_LIMIT"				=> "Your username must be at least %m1% characters and no more than %m2%",
		"ACCOUNT_PASS_CHAR_LIMIT"				=> "Your password must be at least %m1% characters and no more than %m2%",
		"ACCOUNT_PASS_MISMATCH"					=> "Password mismatch. Your passwords must match",
		"ACCOUNT_USERNAME_IN_USE"				=> "Username %m1% is already in use",
		"ACCOUNT_EMAIL_IN_USE"					=> "Email %m1% is already in use",
		"ACCOUNT_LINK_ALREADY_SENT"				=> "An activation email has already been sent to this email address in the last %m1% hour(s)",
		"ACCOUNT_NEW_ACTIVATION_SENT"			=> "We have emailed you a new activation link. Please check your email",
		"ACCOUNT_NOW_ACTIVE"					=> "Your account is now active",
		"ACCOUNT_SPECIFY_NEW_PASSWORD"			=> "Please enter your new password",
		"ACCOUNT_NEW_PASSWORD_LENGTH"			=> "Your new password must be at least %m1% characters and no more than %m2%",
		"ACCOUNT_PASSWORD_INVALID"				=> "Current password doesn't match the one we have on record",
		"ACCOUNT_EMAIL_TAKEN"					=> "This email address is already taken by another user",
		"ACCOUNT_DETAILS_UPDATED"				=> "Your account details have been updated",
		"ACTIVATION_MESSAGE"					=> "%m1%account/activate/%m2%",
		"ACCOUNT_REGISTRATION_COMPLETE_TYPE1"	=> "You have successfully registered. You can now log in <a href=\"/session/login\">here</a>.",
		"ACCOUNT_REGISTRATION_COMPLETE_TYPE2"	=> "You have successfully registered. You will soon receive an activation email.
													You must activate your account before logging in.",
	));

	//Forgot password
	$lang = array_merge($lang,array(
		"FORGOTPASS_INVALID_TOKEN"				=> "Invalid token",
		"FORGOTPASS_NEW_PASS_EMAIL"				=> "We have emailed you a new password",
		"FORGOTPASS_REQUEST_CANNED"				=> "Your lost password request has been cancelled",
		"FORGOTPASS_REQUEST_EXISTS"				=> "There is already an outstanding lost password request on this account. Please recheck your inbox and spam folders for your temporary password.",
		"FORGOTPASS_REQUEST_SUCCESS"			=> "We have emailed you instructions on how to regain access to your account",
	));

	//Miscellaneous
	$lang = array_merge($lang,array(
		"CONFIRM"								=> "Confirm",
		"DENY"									=> "Deny",
		"SUCCESS"								=> "Success",
		"ERROR"									=> "Error",
		"NOTHING_TO_UPDATE"						=> "Nothing to update",
		"SQL_ERROR"								=> "Unable to communicate with the database. Please notify us.",
		"MAIL_ERROR"							=> "Unable to send an email to you. Please notify us.",
		"MAIL_TEMPLATE_BUILD_ERROR"				=> "Error building email template",
		"MAIL_TEMPLATE_DIRECTORY_ERROR"			=> "Unable to open mail-templates directory. Perhaps try setting the mail directory to %m1%",
		"MAIL_TEMPLATE_FILE_EMPTY"				=> "Template file is empty... nothing to send",
		"FEATURE_DISABLED"						=> "This feature is currently disabled",
	));
?>
