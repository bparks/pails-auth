<?php
class GroupController extends Pails\Controller
{
	public function index()
	{
		$this->model = Group::all();
        return $this->view();
	}
}
