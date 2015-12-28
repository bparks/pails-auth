<?php
class PermissionController extends Pails\Controller
{
	public function index()
	{
		$this->model = Permission::all();
        return $this->view();
	}
}
