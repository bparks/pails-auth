<?php
class UserController extends Pails\Controller
{
	use PailsAuthentication;

	public $before_actions = array(
		'require_login' => array('except' => array('register')),
		'require_permission' => array('only' => array('index', 'toggle_active'), 'options' => array('manage_users')),
		'require_anonymous' => array('except' => array('index', 'update', 'toggle_active'))
	);

	public function index()
	{
		$this->model = User::all();
        return $this->view();
	}

	public function toggle_active($args) {
		$this->view = false;
		$user = User::find($args[0]);
		$user->active = !$user->active;
		$user->save();
		return $this->content('true');
	}

	public function register()
	{
		return $this->view();
	}

	public function update()
	{
		return $this->view();
	}
}
