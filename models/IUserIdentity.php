<?php

namespace Pails\Authentication;

interface IUserIdentity
{
	function has_permission($permission);
}