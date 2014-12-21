<?php
	/*
		Below is a very simple example of how to process a login request.
		Some simple validation (ideally more is needed).
	*/

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);
	$remember_choice = isset($_POST['remember_me']) ? trim($_POST["remember_me"]) : 0;

	//Perform some validation
	//Feel free to edit / change as required
	if($username == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
	}
	if($password == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	}

	//End data validation
	if(count($errors) == 0)
	{
		//A security note here, never tell the user which credential was incorrect
		if(!usernameExists($username))
		{
			$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
		}
		else
		{
			$userdetails = fetchUserDetails($username);

			//See if the user's account is activation
			if($userdetails->active==0)
			{
				$errors[] = lang("ACCOUNT_INACTIVE");
			}
			else
			{
				//Hash the password and use the salt from the database to compare the password.
				$entered_pass = generateHash($password,$userdetails->password);

				if($entered_pass != $userdetails->password)
				{
					//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
					$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
				}
				else
				{
					//passwords match! we're good to go'

					//Construct a new logged in user object
					//Transfer some db data to the session object
					$loggedInUser = new loggedInUser();
					$loggedInUser->email = $userdetails->email;
					$loggedInUser->user_id = $userdetails->user_id;
					$loggedInUser->hash_pw = $userdetails->password;
					$loggedInUser->display_username = $userdetails->username;
					$loggedInUser->clean_username = $userdetails->username_clean;
					$loggedInUser->remember_me = $remember_choice;
					$loggedInUser->remember_me_sessid = generateHash(uniqid(rand(), true));

					//Update last sign in
					$loggedInUser->updatelast_sign_in();

					$_SESSION["userPieUser"] = $loggedInUser;
					if($loggedInUser->remember_me == 1) {
						$session = new Session(array(
							"session_start" => time(),
							"session_data" => serialize($loggedInUser),
							"session_id" => $loggedInUser->remember_me_sessid
						));
						$session->save();
						setcookie("userPieUser", $loggedInUser->remember_me_sessid, time()+604800, '/');
					}

					//Redirect to user account page
					header("Location: /");
				}
			}
		}
	}
}
?>
<div class="modal-ish">
	<div class="modal-header">
		<h2>Sign In</h2>
	</div>
	<div class="modal-body">
        <?php
        if(!empty($_POST)):
        	if(count($errors) > 0):
        ?>
        	<div id="errors">
        		<?php errorBlock($errors); ?>
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
		    <input type="checkbox" name="remember_me" value="1" />
            <label><small>Remember Me?</small></label>
        </p>

		<input type="submit" class="btn btn-primary" name="new" id="newfeedform" value="Sign In" />
        </form>
    </div>
</div>
