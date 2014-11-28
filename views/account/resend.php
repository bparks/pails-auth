<?php
	/* 
		Below process a new activation link for a user, as they first activation email may have never arrived.
	*/
	
$errors = array();
$success_message = "";

//Forms posted
//----------------------------------------------------------------------------------------------
if(!empty($_POST))
{
		$email = $_POST["email"];
		$username = $_POST["username"];
		
		//Perform some validation
		//Feel free to edit / change as required
		
		if(trim($email) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
		}
		//Check to ensure email is in the correct format / in the db
		else if(!isValidemail($email) || !emailExists($email))
		{
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}
		
		if(trim($username) == "")
		{
			$errors[] =  lang("ACCOUNT_SPECIFY_USERNAME");
		}
		else if(!usernameExists($username))
		{
			$errors[] = lang("ACCOUNT_INVALID_USERNAME");
		}
		
		
		if(count($errors) == 0)
		{
			//Check that the username / email are associated to the same account
			if(!emailusernameLinked($email,$username))
			{
				$errors[] = lang("ACCOUNT_USER_OR_EMAIL_INVALID");
			}
			else
			{
				$userdetails = fetchUserDetails($username);
			
				//See if the user's account is activation
				if($userdetails->active==1)
				{
					$errors[] = lang("ACCOUNT_ALREADY_ACTIVE");
				}
				else
				{
					$hours_diff = round((time()-$userdetails->last_activation_request) / 3600, 0);

					if($hours_diff <= 1)
					{
						$errors[] = lang("ACCOUNT_LINK_ALREADY_SENT",array(1));
					}
					else
					{
						//For security create a new activation url;
						$new_activation_token = generateactivationtoken();
						
						if(!updatelast_activation_request($new_activation_token,$username,$email))
						{
							$errors[] = lang("SQL_ERROR");
						}
						else
						{
							$mail = new userPieMail();
							
							$activation_url = 'http://'.$_SERVER['HTTP_HOST']."/account/activate?token=".$new_activation_token;
						
							//Setup our custom hooks
							$hooks = array(
								"searchStrs" => array("#ACTIVATION-MESSAGE","#USERNAME#"),
								"subjectStrs" => array($activation_url,$userdetails->username)
							);
							
							if(!$mail->newTemplateMsg("resend-activation.txt",$hooks))
							{
								$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
							}
							else
							{
								if(!$mail->sendMail($userdetails->email,"Activate your UserPie Account"))
								{
									$errors[] = lang("MAIL_ERROR");
								}
								else
								{
									//Success, user details have been updated in the db now mail this information out.
									$success_message = lang("ACCOUNT_NEW_ACTIVATION_SENT");
								}
							}
						}
					}
				}
			}
		}
}
?>
<div class="modal-ish">
	<div class="modal-header">
		<h2>Resend Activation E-mail</h2>
	</div>
	<div class="modal-body">
	    <?php
	    if(!empty($_POST) || !empty($_GET["confirm"]) || !empty($_GET["deny"])):
			if(count($errors) > 0):
		?>
	    	<div id="errors">
	        	<?php errorBlock($errors); ?>
	        </div> 
	    <?php
			else:
		?>
	        <div id="success">
	        	<p><?php echo $success_message; ?></p>
	    	</div>
	    <?php
			endif;
	    endif;
	    ?> 
	    <form name="resendActivation" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
		<p>
		    <label>username:</label>
		    <input type="text" name="username" />
		</p>     
		    
		<p>
			<label>email:</label>
			<input type="text" name="email" />
		</p>
	    <p><input type="submit" class="btn btn-primary btn-large" name="activate" id="newfeedform" value="Resend" /></p>
		</form>
	</div>           
</div>