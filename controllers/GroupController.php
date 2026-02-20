<?php
class GroupController extends Pails\Controller
{
	use PailsAuthentication;

	public $before_actions = array(
		'require_login' => array(),
		'require_permission' => array('options' => array('manage_users'))
	);

	public function index()
	{
		$this->model = Group::all();
		return $this->view();
	}

	public function show($args)
	{
		$id = is_array($args) ? (isset($args[0]) ? $args[0] : null) : $args;
		$this->model = Group::find($id);
		if (!$this->model) {
			header('Location: /group');
			exit();
		}
		return $this->view();
	}

	public function create()
	{
		return $this->view();
	}

	public function update($args)
	{
		$id = is_array($args) ? (isset($args[0]) ? $args[0] : null) : $args;
		$this->model = Group::find($id);
		if (!$this->model) {
			header('Location: /group');
			exit();
		}
		return $this->view();
	}

	public function delete($args)
	{
		$id = is_array($args) ? (isset($args[0]) ? $args[0] : null) : $args;
		if ($id == 1) {
			header('Location: /group');
			exit();
		}
		$group = Group::find($id);
		if ($group) {
			foreach ($group->users as $user) {
				$user->group_id = 1;
				$user->save();
			}
			foreach ($group->permissions as $p) {
				$p->delete();
			}
			$group->delete();
		}
		header('Location: /group');
		exit();
	}

	public function add_member($args)
	{
		$this->view = false;
		$group_id = isset($_POST['group_id']) ? (int) $_POST['group_id'] : (is_array($args) && isset($args[0]) ? (int) $args[0] : null);
		$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : null;
		if ($group_id && $user_id) {
			$user = User::find($user_id);
			if ($user && Group::find($group_id)) {
				$user->group_id = $group_id;
				$user->save();
			}
		}
		header('Location: /group/show/' . $group_id);
		exit();
	}

	public function remove_member($args)
	{
		$this->view = false;
		$group_id = isset($_POST['group_id']) ? (int) $_POST['group_id'] : (is_array($args) && isset($args[0]) ? (int) $args[0] : null);
		$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : null;
		if ($group_id && $user_id) {
			$user = User::find($user_id);
			if ($user && (int) $user->group_id === $group_id) {
				$user->group_id = 1;
				$user->save();
			}
		}
		header('Location: /group/show/' . $group_id);
		exit();
	}
}
