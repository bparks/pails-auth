<?php
class Permission extends ActiveRecord\Model
{
	static $belongs_to = array(
		array('user')
	);

	private static $permissions = array(
		'users' => array(),
		'groups' => array(),
	);
	private static $initialized = false;

	static function init_permissions($users, $groups)
	{
		if (self::$initialized)
			throw new RuntimeError('Permissions stack has already been initialized');

		self::$permissions['users'] = $users;
		self::$permissions['groups'] = $groups;
		self::$initialized = true;
	}

	static function grant_user($user_name, $permission)
	{
		self::grant('users', $user_name, $permission);
	}

	static function grant_group($group_name, $permission)
	{
		self::grant('groups', $group_name, $permission);
	}

	public static function grant($stack, $user_name, $permission)
	{
		self::$initialized = true;

		if (!isset(self::$permissions[$stack][$user_name]))
			self::$permissions[$stack][$user_name] = array();

		if (is_array($permission))
			self::$permissions[$stack][$user_name] = array_merge(self::$permissions[$stack][$user_name], $permission);
		else
			self::$permissions[$stack][$user_name][] = $permission;
	}

	public static function user_has($user_name, $permission)
	{
		return isset(self::$permissions['users'][$user_name]) &&
			is_array(self::$permissions['users'][$user_name]) &&
			in_array($permission, self::$permissions['users'][$user_name]);
	}

	public static function group_has($group_name, $permission)
	{
		return isset(self::$permissions['groups'][$group_name]) &&
			is_array(self::$permissions['groups'][$group_name]) &&
			in_array($permission, self::$permissions['groups'][$group_name]);
	}
}