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


		
				
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Account Activation</title>
</head>
<body>

	
	<div class="modal-ish">
  <div class="modal-header">
<h2>Activation</h2>
  </div>
  <div class="modal-body">
 
	<?php
				if(count($errors) > 0)
				{
      errorBlock($errors);
           		 } else { ?> 
       <p>Congratulations! You've successfully activated your new account. You may now <a href="/session/login">login.</a></p>    		 
           		 
    		    <?php }?>

                          

                          </div>

  
</div>

                </form>
                
        
            <div class="clear"></div>
<p style="margin-top:30px; text-align:center;">
<a href="/user/register">Sign Up</a> | <a href="/user/login">Login</a> | <a href="/account/forgot">Forgot Password?</a> | <a href="/">Home Page</a></p>
            
</body>
</html>


	
	
	


