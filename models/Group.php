<?php
class Group extends ActiveRecord\Model
{
	//

	function has_permission($permission)
	{
		return Permission::group_has(strtolower($this->name), $permission);
	}
}