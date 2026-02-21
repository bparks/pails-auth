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

	/** Permissions that have been declared by the app (documented as available). */
	private static $declared = array();

	/**
	 * Declare a permission that your app uses. Call this in an initializer (e.g. config)
	 * to document available permissions. Undeclared permissions will trigger warnings
	 * when granted or when used with require_permission / has_permission.
	 * (Method is named declare_permission because 'declare' is reserved in PHP.)
	 *
	 * @param string $name Permission name (e.g. 'manage_users')
	 */
	static function declare_permission($name)
	{
		$key = strtolower(trim($name));
		if ($key !== '')
			self::$declared[$key] = true;
	}

	/** Returns the list of declared permission names (sorted). Use e.g. for dropdowns on grant screens. */
	static function declared_permissions()
	{
		$keys = array_keys(self::$declared);
		sort($keys);
		return $keys;
	}

	private static function is_declared($permission)
	{
		$key = strtolower(trim($permission));
		return $key !== '' && isset(self::$declared[$key]);
	}

	private static function warn_if_undeclared($permission, $context)
	{
		$p = trim($permission);
		if ($p === '') return;
		if (self::is_declared($p)) return;
		$msg = "Undeclared permission '" . $p . "' " . $context . ". "
			. "Add Permission::declare_permission('" . addslashes($p) . "') to an initializer to document available permissions.";
		trigger_error($msg, E_USER_WARNING);
	}

	static function init_permissions($users, $groups)
	{
		if (self::$initialized)
			throw new Exception('Permissions stack has already been initialized');

		self::$permissions['users'] = $users;
		self::$permissions['groups'] = $groups;
		self::$initialized = true;
	}

	/**
	 * Load DB permission records into the in-memory stack. Optional; if not called,
	 * user_has/group_has will query the DB on demand when a grant isn't found in memory.
	 * Call after init_permissions and after the app has declared all permissions, to avoid
	 * warnings for DB-stored permissions that are declared later in startup.
	 */
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
					if (!isset(self::$permissions['users'][$name]))
						self::$permissions['users'][$name] = array();
					if (!in_array($perm, self::$permissions['users'][$name]))
						self::$permissions['users'][$name][] = $perm;
				}
			} elseif ($p->group_id !== null) {
				$group = Group::find($p->group_id);
				if ($group) {
					$name = strtolower($group->group_name);
					if (!isset(self::$permissions['groups'][$name]))
						self::$permissions['groups'][$name] = array();
					if (!in_array($perm, self::$permissions['groups'][$name]))
						self::$permissions['groups'][$name][] = $perm;
				}
			}
		}
	}

	/** Resolve in-memory user key (e.g. "bparks" or "google:user@example.com") to User, or null. */
	private static function find_user_by_name($user_name)
	{
		$user_name = strtolower(trim($user_name));
		if ($user_name === '') return null;
		$provider = 'local';
		$uname = $user_name;
		if (strpos($user_name, ':') !== false) {
			$parts = explode(':', $user_name, 2);
			$provider = $parts[0];
			$uname = $parts[1];
		}
		$user = User::find('first', array('conditions' => array('LOWER(username) = ? AND provider_name = ?', $uname, $provider)));
		return $user;
	}

	/** Check DB for a user grant and cache in memory if found. */
	private static function user_has_from_db($user_name, $permission)
	{
		$user = self::find_user_by_name($user_name);
		if (!$user) return false;
		$row = self::find('first', array('conditions' => array('user_id = ? AND LOWER(permission) = ?', (int) $user->user_id, $permission)));
		if (!$row) return false;
		// Cache for this request so we don't hit DB again
		if (!isset(self::$permissions['users'][$user_name]))
			self::$permissions['users'][$user_name] = array();
		if (!in_array($permission, self::$permissions['users'][$user_name]))
			self::$permissions['users'][$user_name][] = $permission;
		return true;
	}

	/** Check DB for a group grant and cache in memory if found. */
	private static function group_has_from_db($group_name, $permission)
	{
		$group = Group::find('first', array('conditions' => array('LOWER(group_name) = ?', $group_name)));
		if (!$group) return false;
		$row = self::find('first', array('conditions' => array('group_id = ? AND LOWER(permission) = ?', (int) $group->group_id, $permission)));
		if (!$row) return false;
		if (!isset(self::$permissions['groups'][$group_name]))
			self::$permissions['groups'][$group_name] = array();
		if (!in_array($permission, self::$permissions['groups'][$group_name]))
			self::$permissions['groups'][$group_name][] = $permission;
		return true;
	}

	/** Grant a permission to a user (DB record). */
	static function grant_to_user($user_id, $permission)
	{
		self::warn_if_undeclared($permission, 'granted to user');
		$p = new self();
		$p->user_id = (int) $user_id;
		$p->group_id = null;
		$p->permission = trim($permission);
		$p->save();
	}

	/** Grant a permission to a group (DB record). */
	static function grant_to_group($group_id, $permission)
	{
		self::warn_if_undeclared($permission, 'granted to group');
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

		if (is_array($permission)) {
			foreach ($permission as $p)
				self::warn_if_undeclared($p, 'granted');
		} else {
			self::warn_if_undeclared($permission, 'granted');
		}

		if (!isset(self::$permissions[$stack][$user_name]))
			self::$permissions[$stack][$user_name] = array();

		if (is_array($permission))
			self::$permissions[$stack][$user_name] = array_merge(self::$permissions[$stack][$user_name], $permission);
		else
			self::$permissions[$stack][$user_name][] = $permission;
	}

	public static function user_has($user_name, $permission)
	{
		self::warn_if_undeclared($permission, 'used in permission check (user_has)');
		$user_name = strtolower($user_name);
		$permission = strtolower(trim($permission));
		if ($permission === '') return false;

		if (isset(self::$permissions['users'][$user_name]) &&
			is_array(self::$permissions['users'][$user_name]) &&
			in_array($permission, self::$permissions['users'][$user_name]))
			return true;

		if (self::user_has_from_db($user_name, $permission))
			return true;

		// Fall back to the user's group
		$user = self::find_user_by_name($user_name);
		if ($user && $user->group)
			return self::group_has(strtolower($user->group->group_name), $permission);

		return false;
	}

	public static function group_has($group_name, $permission)
	{
		self::warn_if_undeclared($permission, 'used in permission check (group_has)');
		$group_name = strtolower($group_name);
		$permission = strtolower(trim($permission));
		if ($permission === '') return false;

		if (isset(self::$permissions['groups'][$group_name]) &&
			is_array(self::$permissions['groups'][$group_name]) &&
			in_array($permission, self::$permissions['groups'][$group_name]))
			return true;

		return self::group_has_from_db($group_name, $permission);
	}
}
