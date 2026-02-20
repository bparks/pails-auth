<?php
class Permission extends ActiveRecord\Model
{
	static $table_name = 'permissions';
	static $belongs_to = array(
		array('user'),
		array('group')
	);

	private static $permissions = array(
		'users' => array(),
		'groups' => array(),
	);
	private static $initialized = false;

	static function init_permissions($users, $groups)
	{
		if (self::$initialized)
			throw new Exception('Permissions stack has already been initialized');

		self::$permissions['users'] = $users;
		self::$permissions['groups'] = $groups;
		self::$initialized = true;
	}

	/** Load DB permission records into the in-memory stack so checks apply. Call after init_permissions. */
	static function load_db_grants()
	{
		$rows = self::all();
		foreach ($rows as $p) {
			$perm = strtolower(trim($p->permission));
			if ($perm === '') continue;
			if ($p->user_id !== null) {
				$user = User::find($p->user_id);
				if ($user) {
					$prefix = (isset($user->provider_name) && $user->provider_name != 'local') ? $user->provider_name . ':' : '';
					$name = $prefix . strtolower($user->username);
					self::grant('users', $name, $perm);
				}
			} elseif ($p->group_id !== null) {
				$group = Group::find($p->group_id);
				if ($group) {
					self::grant('groups', strtolower($group->group_name), $perm);
				}
			}
		}
	}

	/** Grant a permission to a user (DB record). */
	static function grant_to_user($user_id, $permission)
	{
		$p = new self();
		$p->user_id = (int) $user_id;
		$p->group_id = null;
		$p->permission = trim($permission);
		$p->save();
	}

	/** Grant a permission to a group (DB record). */
	static function grant_to_group($group_id, $permission)
	{
		$p = new self();
		$p->user_id = null;
		$p->group_id = (int) $group_id;
		$p->permission = trim($permission);
		$p->save();
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

		$user_name = strtolower($user_name);

		if (!isset(self::$permissions[$stack][$user_name]))
			self::$permissions[$stack][$user_name] = array();

		if (is_array($permission))
			self::$permissions[$stack][$user_name] = array_merge(self::$permissions[$stack][$user_name], $permission);
		else
			self::$permissions[$stack][$user_name][] = $permission;
	}

	public static function user_has($user_name, $permission)
	{
		$user_name = strtolower($user_name);
        $permission = strtolower(trim($permission));

		return $permission != '' &&
            isset(self::$permissions['users'][$user_name]) &&
			is_array(self::$permissions['users'][$user_name]) &&
			in_array($permission, self::$permissions['users'][$user_name]);
	}

	public static function group_has($group_name, $permission)
	{
		$group_name = strtolower($group_name);
        $permission = strtolower(trim($permission));

		return $permission != '' &&
            isset(self::$permissions['groups'][$group_name]) &&
			is_array(self::$permissions['groups'][$group_name]) &&
			in_array($permission, self::$permissions['groups'][$group_name]);
	}
}
