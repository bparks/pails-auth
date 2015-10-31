<?php
	/*
		Below is a very simple example of how to process a login request.
		Some simple validation (ideally more is needed).
	*/

//Forms posted
if(!empty($_POST))
{
		$errors = array();
		$email = $_POST["email"];

		//Perform some validation
		//Feel free to edit / change as required

		if(trim($email) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
		}
		else if(!isValidEmail($email))
		{
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}
		else if($email == $_SESSION[AUTH_COOKIE_NAME]->email)
		{
				$errors[] = lang("NOTHING_TO_UPDATE");
		}
		else if(emailExists($email))
		{
			$errors[] = lang("ACCOUNT_EMAIL_TAKEN");
		}

		//End data validation
		if(count($errors) == 0)
		{
			$_SESSION[AUTH_COOKIE_NAME]->updateEmail($email);
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
        <form name="changePass" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <p>
                <label>Email:</label>
                <input type="text" name="email" value="<?php echo $_SESSION[AUTH_COOKIE_NAME]->email; ?>" />
            </p>
			<input type="submit" class="btn btn-primary" name="new" id="newfeedform" value="Update" />
        </form>
    </div>
</div>
