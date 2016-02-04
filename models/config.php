<?php
require_once(__DIR__."/Session.php");
require_once(__DIR__."/User.php");
require_once(__DIR__."/Group.php");
require_once(__DIR__."/Permission.php");
require_once(__DIR__."/funcs.user.php");
require_once(__DIR__."/funcs.general.php");
require_once(dirname(__DIR__)."/PailsAuthentication.trait.php");
require_once(dirname(__DIR__)."/mailers/AuthMailer.php");
require_once(dirname(__DIR__)."/providers/IAuthenticationProvider.php");
require_once(dirname(__DIR__)."/providers/LocalAuthenticationProvider.php");
require_once(dirname(__DIR__)."/providers/RemoteAuthenticationProvider.php");

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
	static private $providers;

	static function initialize($options = [])
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

		if (isset($options['providers'])) {
			self::$providers = [];
			foreach ($options['providers'] as $item) {
				if ($item == 'local')
					self::$providers['local'] = new LocalAuthenticationProvider;

				$parts = explode(';', $item, 3);

				if ($parts[0] == 'remote')
					self::$providers[$parts[1]] = new RemoteAuthenticationProvider($parts[2]);
			}
		} else {
			self::$providers = ['local' => new LocalAuthenticationProvider];
		}
	}

	static function getProviders()
	{
		return self::$providers;
	}

	static function getProvider($key)
	{
		if (!isset(self::$providers[$key]))
			return null;
		return self::$providers[$key];
	}
}
