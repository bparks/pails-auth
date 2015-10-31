<?php
	/*
		Below is a very simple example of how to process a new user.
		 Some simple validation (ideally more is needed).

		The first goal is to check for empty / null data, to reduce workload here we let the user class perform it's own internal checks, just in case they are missed.
	*/

//Forms posted
if(!empty($_POST))
{
		$errors = array();
		$email = trim($_POST["email"]);
		$username = trim($_POST["username"]);
		$password = trim($_POST["password"]);
		$confirm_pass = trim($_POST["passwordc"]);

		//Perform some validation
		//Feel free to edit / change as required

		if(minMaxRange(5,25,$username))
		{
			$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(5,25));
		}
		if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
		{
			$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
		}
		else if($password != $confirm_pass)
		{
			$errors[] = lang("ACCOUNT_PASS_MISMATCH");
		}
		if(!isValidemail($email))
		{
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}
		//End data validation
		if(count($errors) == 0)
		{
				//Construct a user object
				$user = User::register($username,$password,$email);

				//Checking this flag tells us whether there were any errors such as possible data duplication occured
				if(!$user->status)
				{
					if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
					if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));
				}
				else
				{
					//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
					if(!$user->userPieAddUser())
					{
						if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
						if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
					}
				}
		}
	   if(count($errors) == 0)
	   {
           $message = lang("ACCOUNT_REGISTRATION_COMPLETE_TYPE2");
		       //header("Location: ");
	   }
	   else
	   {
	   			$message = '<span style="color: red;">'.implode(", ", $errors).'</span>';
	   }
	}
?>
<div class="modal-ish">
	<div class="modal-header">
		<h2>Sign Up</h2>
	</div>
	<div class="modal-body">
		<div id="success">
	    	<p><?php if (isset($message)) echo $message; ?></p>
        </div>

        <div id="regbox">
            <form name="newUser" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
            <p>
                <label>Username:</label>
                <input type="text" name="username" />
            </p>

            <p>
                <label>Password&nbsp;<span title="Requires 8 or more characters." class="glyphicon glyphicon-question-sign"></span>&nbsp;:</label>
                <input type="password" name="password" />
            </p>

            <p>
                <label>Re-type Password:</label>
                <input type="password" name="passwordc" />
            </p>

            <p>
                <label>Email:</label>
                <input type="text" name="email" />
            </p>

			<input type="submit" class="btn btn-primary" name="new" id="newfeedform" value="Register" />
            </form>
      	</div>
    </div>
</div>
