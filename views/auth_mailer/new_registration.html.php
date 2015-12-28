<h4>Welcome!</h4>

<p>Hello <?php echo $this->model['user']->username; ?>,</p>

<p>Click on the link below to activate your new account.</p>

<table cellspacing="0" cellpadding="0"> <tr>
<td width="20">&nbsp;</td>
<td align="center" width="200" height="40" bgcolor="#e35800" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;">
<a href="<?php echo $this->model['activation_url']; ?>" style="font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block"><span style="color: #FFFFFF">Click to Activate</span></a>
</td>
</tr> </table>
<p style="margin-left:20px">Or, here's the full link:<br/>
<span style="margin-left:20px"><?php echo $this->model['activation_url']; ?></span></p>

<p>Feel free to contact us anytime with your questions.</p>

<?php $this->renderPartial('auth_mailer/_footer'); ?>
