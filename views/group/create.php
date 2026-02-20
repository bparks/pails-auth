<?php
$errors = array();
if (!empty($_POST)) {
	$name = trim(isset($_POST['group_name']) ? $_POST['group_name'] : '');
	if ($name === '') {
		$errors[] = 'Group name is required.';
	} else {
		$existing = Group::find_by_group_name($name);
		if ($existing) {
			$errors[] = 'A group with that name already exists.';
		} else {
			$group = new Group();
			$group->group_name = $name;
			$group->save();
			header('Location: /group/show/' . $group->group_id);
			exit();
		}
	}
}
?>
<h2>Add group</h2>
<?php if (count($errors) > 0): ?>
<div id="errors">
	<?php errorBlock($errors); ?>
</div>
<?php endif; ?>
<form action="/group/create" method="post">
	<p>
		<label>Group name:</label>
		<input type="text" name="group_name" value="<?php echo isset($_POST['group_name']) ? htmlspecialchars($_POST['group_name']) : ''; ?>" />
	</p>
	<input type="submit" class="btn btn-primary" value="Create" />
</form>
<p><a href="/group">Back to groups</a></p>
