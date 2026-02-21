<?php $group = $this->model; ?>
<h2>Group: <?php echo htmlspecialchars($group->group_name); ?></h2>

<p><a href="/group/update/<?php echo $group->group_id; ?>">Edit</a> | <a href="/group">Back to groups</a></p>

<h3>Members</h3>
<?php $members = $group->users; ?>
<?php if (count($members) > 0): ?>
<table style="width: 100%; border-collapse: collapse; border: solid 1px black;">
	<tr>
		<th>User ID</th>
		<th>Username</th>
		<th>Email</th>
		<th></th>
	</tr>
	<?php foreach ($members as $user): ?>
	<tr>
		<td><?php echo $user->user_id; ?></td>
		<td><?php echo htmlspecialchars($user->username); ?></td>
		<td><?php echo htmlspecialchars($user->email); ?></td>
		<td>
			<form action="/group/remove_member/<?php echo $group->group_id; ?>" method="post" style="display:inline;">
				<input type="hidden" name="user_id" value="<?php echo $user->user_id; ?>" />
				<input type="hidden" name="group_id" value="<?php echo $group->group_id; ?>" />
				<button type="submit">Remove</button>
			</form>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
<?php else: ?>
<p>No members.</p>
<?php endif; ?>

<h3>Add member</h3>
<?php
	$all_users = User::all();
	$member_ids = array_map(function ($u) { return $u->user_id; }, $members->to_array());
	$non_members = array_filter($all_users, function ($u) use ($member_ids) { return !in_array($u->user_id, $member_ids); });
?>
<?php if (count($non_members) > 0): ?>
<form action="/group/add_member/<?php echo $group->group_id; ?>" method="post">
	<input type="hidden" name="group_id" value="<?php echo $group->group_id; ?>" />
	<label>User:</label>
	<select name="user_id">
		<?php foreach ($non_members as $u): ?>
		<option value="<?php echo $u->user_id; ?>"><?php echo htmlspecialchars($u->username); ?> (<?php echo htmlspecialchars($u->email); ?>)</option>
		<?php endforeach; ?>
	</select>
	<button type="submit" class="btn btn-primary">Add</button>
</form>
<?php else: ?>
<p>All users are already in this group.</p>
<?php endif; ?>
