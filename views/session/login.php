<div class="modal-ish">
	<div class="modal-header">
		<h2>Sign In</h2>
	</div>
	<div class="modal-body">
        <?php
        if(isset($_GET['status']) && ($_GET['status']) == "success"):
        ?>
        <p>Your account was created successfully. Please login.</p>
        <?php
        endif;
    	?>
<?php if (isset($this->model['local'])): ?>
        <form name="newUser" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
        <p>
            <label>Username:</label>
            <input type="text"  name="username" />
        </p>

        <p>
            <label>Password:</label>
            <input type="password" name="password" />
        </p>

		<p>
		    <input type="checkbox" name="remember_me" value="1" checked />
            <label><small>Remember Me?</small></label>
        </p>
				<p>
					<a href="/account/forgot">Forgot Password?</a> | <a href="/user/register">Register</a>
				</p>

		<input type="submit" class="btn btn-primary" name="new" id="newfeedform" value="Sign In" />
        </form>
<?php endif; ?>
<?php foreach ($this->model as $key => $value): ?>
    <?php if ($key == 'local' || get_class($value) == 'Pails\Authentication\LocalAuthenticationProvider') continue; ?>
        <div class="login-option">
            <a href="<?php echo $value->getLoginUrl(); ?>">Log in with <?php echo $key; ?></a>
        </div>
<?php endforeach; ?>
    </div>
</div>
