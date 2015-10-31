<?php
require_once(__DIR__."/settings.php");
require_once(__DIR__."/Session.php");
require_once(__DIR__."/User.php");
require_once(__DIR__."/Group.php");
require_once(__DIR__."/Permission.php");
require_once(__DIR__."/class.mail.php");
require_once(__DIR__."/funcs.user.php");
require_once(__DIR__."/funcs.general.php");
require_once(dirname(__DIR__)."/PailsAuthentication.trait.php");

/*
//This code really needs to go somewhere else
set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext)
{
	throw new Exception("$errstr");
}, E_ERROR | E_WARNING);
*/

define('AUTH_COOKIE_NAME', 'pails_auth_token');

class PailsAuth
{
	static function initialize()
	{
		global $USER_PERMISSIONS;
		global $GROUP_PERMISSIONS;

		Permission::init_permissions(
			isset($USER_PERMISSIONS) ? $USER_PERMISSIONS : array(),
			isset($GROUP_PERMISSIONS) ? $GROUP_PERMISSIONS : array()
		);

		Permission::grant_group('admin', 'manage_users');

		if (defined('ADMIN_MENU_SLUG'))
		{
			Menu::add_static_item(ADMIN_MENU_SLUG, 'Users', '/user', array(
				'Groups' => '/group',
				'Permissions' => '/permission'
			));
		}
	}
}
