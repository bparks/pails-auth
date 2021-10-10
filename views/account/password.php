<?php
	/*
		Below is a very simple example of how to process a login request.
		Some simple validation (ideally more is needed).
	*/

//Forms posted
if(!empty($_POST))
{
		$errors = array();
		$password = $_POST["password"];
		$password_new = $_POST["passwordc"];
		$password_confirm = $_POST["passwordcheck"];

		//Perform some validation
		//Feel free to edit / change as required

		if(trim($password) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
		}
		else if(trim($password_new) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_NEW_PASSWORD");
		}
		else if(minMaxRange(8,50,$password_new))
		{
			$errors[] = lang("ACCOUNT_NEW_PASSWORD_LENGTH",array(8,50));
		}
		else if($password_new != $password_confirm)
		{
			$errors[] = lang("ACCOUNT_PASS_MISMATCH");
		}

		//End data validation
		if(count($errors) == 0)
		{
			//Confirm the hash's match before updating a users password
			$entered_pass = generateHash($password,$_SESSION[AUTH_COOKIE_NAME]->hash_pw);

			//Also prevent updating if someone attempts to update with the same password
			$entered_pass_new = generateHash($password_new,$_SESSION[AUTH_COOKIE_NAME]->hash_pw);

			if($entered_pass != $_SESSION[AUTH_COOKIE_NAME]->hash_pw)
			{
				//No match
				$errors[] = lang("ACCOUNT_PASSWORD_INVALID");
			}
			else if($entered_pass_new == $_SESSION[AUTH_COOKIE_NAME]->hash_pw)
			{
				//Don't update, this fool is trying to update with the same password
				$errors[] = lang("NOTHING_TO_UPDATE");
			}
			else
			{
				//This function will create the new hash and update the hash_pw property.
				$_SESSION[AUTH_COOKIE_NAME]->updatePassword($password_new);
			}
		}
	}
?>
<div class="modal-ish">
	<div class="modal-body">
		<?php
        if(!empty($_POST)):
            if(count($errors) > 0):
        ?>
            <div id="errors">
            	<?php errorBlock($errors); ?>
            </div>
        <?php
        	else:
        ?>
            <div id="success">
               <p><?php echo lang("ACCOUNT_DETAILS_UPDATED"); ?></p>
            </div>
        <?php
        	endif;
        endif;
        ?>
        <form name="changePass" action="/account/password" method="post">
        <p>
            <label>Password:</label>
            <input type="password" name="password" />
        </p>

        <p>
            <label>New Pass:</label>
            <input type="password" name="passwordc" />
        </p>

        <p>
            <label>Confirm Pass:</label>
            <input type="password" name="passwordcheck" />
        </p>
		<input type="submit" class="btn btn-primary" name="new" id="newfeedform" value="Update" />
        </form>
	</div>
</div>
