<h4>Thank you for contacting us</h4>

<p>Hello <?php echo $this->model['user']->username; ?>,</p>

<p>We have setup a temporary password for you. Please login and customize your password.</p>

<p style="margin-left:20px">Your temporary password: <?php echo $this->model['new_password']; ?></p>

<?php $this->renderPartial('auth_mailer/_footer'); ?>
