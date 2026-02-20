<?php
class Group extends ActiveRecord\Model
{
	static $table_name = 'groups';
	static $primary_key = 'group_id';

	static $has_many = array(
		array('users', 'foreign_key' => 'group_id'),
		array('permissions', 'foreign_key' => 'group_id')
	);

	function has_permission($permission)
	{
		return Permission::group_has(strtolower($this->group_name), $permission);
	}
}