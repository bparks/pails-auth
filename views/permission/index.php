<h2>Permissions</h2>

<p><a href="/permission/create">Grant permission</a></p>

<table style="width: 100%; border-collapse: collapse; border: solid 1px black;">
	<tr>
		<th>ID</th>
		<th>Type</th>
		<th>User / Group</th>
		<th>Permission</th>
		<th></th>
	</tr>
	<?php foreach ($this->model as $p): ?>
	<tr>
		<td><?php echo $p->id; ?></td>
		<td><?php echo $p->user_id !== null ? 'User' : 'Group'; ?></td>
		<td>
			<?php
			if ($p->user_id !== null) {
				$u = User::find($p->user_id);
				echo $u ? htmlspecialchars($u->username) : 'User #' . $p->user_id;
			} else {
				$g = Group::find($p->group_id);
				echo $g ? htmlspecialchars($g->group_name) : 'Group #' . $p->group_id;
			}
			?>
		</td>
		<td><?php echo htmlspecialchars($p->permission); ?></td>
		<td>
			<a href="/permission/delete/<?php echo $p->id; ?>" onclick="return confirm('Revoke this permission?');">Revoke</a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
