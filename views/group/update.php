<?php
$group = $this->model;
$errors = array();
if (!empty($_POST)) {
	$name = trim(isset($_POST['group_name']) ? $_POST['group_name'] : '');
	if ($name === '') {
		$errors[] = 'Group name is required.';
	} else {
		$existing = Group::find_by_group_name($name);
		if ($existing && $existing->group_id != $group->group_id) {
			$errors[] = 'A group with that name already exists.';
		} else {
			$group->group_name = $name;
			$group->save();
			header('Location: /group/show/' . $group->group_id);
			exit();
		}
	}
}
?>
<h2>Edit group</h2>
<?php if (count($errors) > 0): ?>
<div id="errors">
	<?php errorBlock($errors); ?>
</div>
<?php endif; ?>
<form action="/group/update/<?php echo $group->group_id; ?>" method="post">
	<p>
		<label>Group name:</label>
		<input type="text" name="group_name" value="<?php echo htmlspecialchars($group->group_name); ?>" />
	</p>
	<input type="submit" class="btn btn-primary" value="Update" />
</form>
<p><a href="/group/show/<?php echo $group->group_id; ?>">Back to group</a><?php if ($group->group_id != 1): ?> | <a href="/group/delete/<?php echo $group->group_id; ?>" onclick="return confirm('Delete this group?');">Delete group</a><?php endif; ?></p>
