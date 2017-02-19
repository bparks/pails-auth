<?php
require_once(__DIR__."/Session.php");
require_once(__DIR__."/IUserIdentity.php");
require_once(__DIR__."/WrappedUser.php");
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
require_once(dirname(__DIR__)."/providers/GoogleAuthenticationProvider.php");

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
    static private $merge_local_users = true;
    static private $create_local_users = false;

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
			foreach ($options['providers'] as $key => $value) {
				$classname = $value[0];
				if (substr($classname, 0, 1) !== '\\')
					$classname = "\\Pails\\Authentication\\".$classname;

				if (count($value) > 1)
					self::$providers[$key] = new $classname($value[1]);
				else
					self::$providers[$key] = new $classname();
			}
		} else {
			self::$providers = ['local' => new \Pails\Authentication\LocalAuthenticationProvider];
		}

        if (isset($options['merge_local_users']))
            self::$merge_local_users = $options['merge_local_users'];
        if (isset($options['create_local_users']))
            self::$create_local_users = $options['create_local_users'];
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

    static function shouldMergeLocalUsers()
    {
        return self::$merge_local_users;
    }

    static function shouldCreateLocalUsers()
    {
        return self::$create_local_users;
    }
}
