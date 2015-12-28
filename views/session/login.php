<div class="modal-ish">
	<div class="modal-header">
		<h2>Sign In</h2>
	</div>
	<div class="modal-body">
        <?php
        if(!empty($_POST)):
        	if(isset($this->model) && is_array($this->model) && count($this->model) > 0):
        ?>
        	<div id="errors">
        		<?php errorBlock($this->model); ?>
        	</div>
        <?php
        	endif;
        endif;
        ?>
        <?php
        if(isset($_GET['status']) && ($_GET['status']) == "success"):
        ?>
        <p>Your account was created successfully. Please login.</p>
        <?php
        endif;
    	?>
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
    </div>
</div>
