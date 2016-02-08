<?php

namespace Pails\Authentication;

class WrappedUser implements IUserIdentity
{
	function __construct($object)
	{
		foreach ((array)$object as $key => $value) {
			$this->$key = $value;
		}
	}

	function has_permission($permission)
	{
		return isset($this->username) && \Permission::user_has(strtolower($this->username), $permission);
	}
}