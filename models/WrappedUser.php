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
        $fq_username = ($this->provider_name != 'local' ? $this->provider_name . ':' : '') . strtolower($this->username);
		return isset($this->username) && \Permission::user_has(strtolower($fq_username), $permission);
	}
}
