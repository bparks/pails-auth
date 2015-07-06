<?php	
require_once(__DIR__."/settings.php");
require_once(__DIR__."/Session.php");
require_once(__DIR__."/User.php");
require_once(__DIR__."/Group.php");
require_once(__DIR__."/Permission.php");
require_once(__DIR__."/class.user.php");
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

class PailsAuth
{
	static function initialize()
	{
		global $USER_PERMISSIONS;
		global $GROUP_PERMISSIONS;

		$remember_me_length = 604800;
		if(isset($_SESSION["userPieUser"]) && is_object($_SESSION["userPieUser"]))
			$loggedInUser = $_SESSION["userPieUser"];
		else if(isset($_COOKIE["userPieUser"])) {
			try
			{
				$session = Session::find($_COOKIE['userPieUser']);
				$loggedInUser = unserialize($session->session_data);
			}
			catch (Exception $e)
			{
				$loggedInUser = NULL;
				setcookie("userPieUser", "", -$remember_me_length);
			}
		} else {
			$sessions = Session::find('all', array(
				'conditions' => array(time()." >= (session_start+".$remember_me_length.")")
			));
			foreach ($sessions as $session) {
				$session->delete();
			}
			$loggedInUser = NULL;
		}
		$_SESSION["userPieUser"] = $loggedInUser;

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
