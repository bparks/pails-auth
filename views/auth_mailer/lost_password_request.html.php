<p>Hello <?php echo $this->model['user']->username; ?>,</p>

<p>A reset password request has been submitted for the username "<?php echo $this->model['user']->username; ?>".
Please click to confirm this request to receive a new, temporary password by email.</p>

<p>To confirm, click on the confirm button below.</p>

<table cellspacing="0" cellpadding="0"> <tr>
<td width="20">&nbsp;</td>
<td align="center" width="200" height="40" bgcolor="#e35800" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;">
<a href="<?php echo $this->model['confirm_url']; ?>" style="font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block"><span style="color: #FFFFFF">Click to Confirm</span></a>
</td>
</tr> </table>

<p style="margin-left:20px">Or, here's the full link:<br/>
<span style="margin-left:20px"><?php echo $this->model['confirm_url']; ?></span></p><br/>

<p>If you received this email in error, just ignore this email or click below to deny this password reset.</p>

<table cellspacing="0" cellpadding="0"> <tr>
<td width="20">&nbsp;</td>
<td align="center" width="200" height="40" bgcolor="#878787" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;">
<a href="<?php echo $this->model['deny_url']; ?>" style="font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block"><span style="color: #FFFFFF">Click to Deny</span></a>
</td>
</tr> </table>

<?php $this->renderPartial('auth_mailer/_footer'); ?>
