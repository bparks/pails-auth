<?php
	/* 
		Activate a users account
	*/
$errors = array();

//Get token param
if(isset($_GET["token"]))
{
		
		$token = $_GET["token"];
		
		if(!isset($token))
		{
			$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
		}
		else if(!validateactivationtoken($token)) //Check for a valid token. Must exist and active must be = 0
		{
			$errors[] = "Token does not exist / This account is already activated";
		}
		else
		{
			//Activate the users account
			if(!setUseractive($token))
			{
				$errors[] = lang("SQL_ERROR");
			}
		}
}
else
{
	$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
}
?>
<div class="modal-ish">
	<div class="modal-header">
		<h2>Activation</h2>
	</div>
	<div class="modal-body">
		<?php
		if(count($errors) > 0):
	    	errorBlock($errors);
		else:
		?> 
       <p>Congratulations! You've successfully activated your new account. You may now <a href="/session/login">login.</a></p>   		 
		<?php
		endif;
		?>
	</div>
</div>


	
	
	


