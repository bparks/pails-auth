<h2>Groups</h2>

<p><a href="/group/create">Add group</a></p>

<table style="width: 100%; border-collapse: collapse; border: solid 1px black;">
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th></th>
	</tr>
	<?php foreach ($this->model as $group): ?>
	<tr>
		<td><?php echo $group->group_id; ?></td>
		<td><a href="/group/show/<?php echo $group->group_id; ?>"><?php echo htmlspecialchars($group->group_name); ?></a></td>
		<td>
			<a href="/group/update/<?php echo $group->group_id; ?>">Edit</a>
			<?php if ($group->group_id != 1): ?>
			| <a href="/group/delete/<?php echo $group->group_id; ?>" onclick="return confirm('Delete this group?');">Delete</a>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
