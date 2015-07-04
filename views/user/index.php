<script>
function toggle_active(id) {
	$.get('/user/toggle_active/' + id, function() {
		var orig = $('.active-' + id).text();
		if (orig == 'Active')
			$('.active-' + id).text('Inactive');
		else
			$('.active-' + id).text('Active');
	})
}
</script>

<h2>Users</h2>

<table style="width: 100%; border-collapse: collapse; border: solid 1px black;">
	<tr>
		<th>ID</th>
		<th>Username</th>
		<th>Email</th>
		<th>Last Activation Request</th>
		<th>Active<br />(click to toggle)</th>
		<th>Signed Up</th>
		<th>Last Signed In</th>
	</tr>
	<?php foreach ($this->model as $user): ?>
	<tr>
		<td><?php echo $user->user_id; ?></td>
		<td><?php echo $user->username; ?></td>
		<td><?php echo $user->email; ?></td>
		<td><?php echo date('d M, Y H:i:s', $user->last_activation_request); ?></td>
		<td><a href="javascript:toggle_active(<?php echo $user->user_id; ?>);" class="active-<?php echo $user->user_id; ?>"><?php echo $user->active ? 'Active': 'Inactive'; ?></a></td>
		<td><?php echo date('d M, Y H:i:s', $user->sign_up_date); ?></td>
		<td><?php echo date('d M, Y H:i:s', $user->last_sign_in); ?></td>
	</tr>
	<?php endforeach; ?>
</table>