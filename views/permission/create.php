<?php
$errors = array();
if (!empty($_POST)) {
	$grant_to = isset($_POST['grant_to']) ? $_POST['grant_to'] : '';
	$permission = trim(isset($_POST['permission']) ? $_POST['permission'] : '');
	if ($permission === '') {
		$errors[] = 'Permission name is required.';
	} elseif ($grant_to === 'user') {
		$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
		if (!$user_id) {
			$errors[] = 'Please select a user.';
		} else {
			Permission::grant_to_user($user_id, $permission);
			header('Location: /permission');
			exit();
		}
	} elseif ($grant_to === 'group') {
		$group_id = isset($_POST['group_id']) ? (int) $_POST['group_id'] : 0;
		if (!$group_id) {
			$errors[] = 'Please select a group.';
		} else {
			Permission::grant_to_group($group_id, $permission);
			header('Location: /permission');
			exit();
		}
	} else {
		$errors[] = 'Please choose User or Group.';
	}
}
$users = User::all();
$groups = Group::all();
?>
<h2>Grant permission</h2>
<?php if (count($errors) > 0): ?>
<div id="errors">
	<?php errorBlock($errors); ?>
</div>
<?php endif; ?>
<form action="/permission/create" method="post">
	<p>
		<label>Grant to:</label>
		<select name="grant_to" id="grant_to">
			<option value="">-- choose --</option>
			<option value="user" <?php echo (isset($_POST['grant_to']) && $_POST['grant_to'] === 'user') ? 'selected' : ''; ?>>User</option>
			<option value="group" <?php echo (isset($_POST['grant_to']) && $_POST['grant_to'] === 'group') ? 'selected' : ''; ?>>Group</option>
		</select>
	</p>
	<p id="user_row" style="display:none;">
		<label>User:</label>
		<select name="user_id">
			<option value="">-- choose user --</option>
			<?php foreach ($users as $u): ?>
			<option value="<?php echo $u->user_id; ?>"><?php echo htmlspecialchars($u->username); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p id="group_row" style="display:none;">
		<label>Group:</label>
		<select name="group_id">
			<option value="">-- choose group --</option>
			<?php foreach ($groups as $g): ?>
			<option value="<?php echo $g->group_id; ?>"><?php echo htmlspecialchars($g->group_name); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label>Permission:</label>
		<input type="text" name="permission" value="<?php echo isset($_POST['permission']) ? htmlspecialchars($_POST['permission']) : ''; ?>" />
	</p>
	<input type="submit" class="btn btn-primary" value="Grant" />
</form>
<p><a href="/permission">Back to permissions</a></p>
<script>
document.getElementById('grant_to').onchange = function() {
	document.getElementById('user_row').style.display = this.value === 'user' ? 'block' : 'none';
	document.getElementById('group_row').style.display = this.value === 'group' ? 'block' : 'none';
};
if (document.getElementById('grant_to').value === 'user') document.getElementById('user_row').style.display = 'block';
if (document.getElementById('grant_to').value === 'group') document.getElementById('group_row').style.display = 'block';
</script>
