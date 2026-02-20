<?php
class PermissionController extends Pails\Controller
{
	use PailsAuthentication;

	public $before_actions = array(
		'require_login' => array(),
		'require_permission' => array('options' => array('manage_users'))
	);

	public function index()
	{
		$this->model = Permission::all();
		return $this->view();
	}

	public function create()
	{
		return $this->view();
	}

	public function delete($args)
	{
		$this->view = false;
		$id = is_array($args) ? (isset($args[0]) ? $args[0] : null) : $args;
		if ($id) {
			$p = Permission::find($id);
			if ($p) $p->delete();
		}
		header('Location: /permission');
		exit();
	}
}
