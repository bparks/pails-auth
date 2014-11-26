<?php

	/*
		UserCake Version: 1.0
		http://usercake.com


	*/

?>	 <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container clearfix">
	<ul class="nav pull-left">
        	<li><a href="/">Home</a></li>	
	</ul>


        <ul class="nav pull-right">
<?php if(isUserLoggedIn()) { ?>
            	<li><a href="/">Account Home</a></li>
       			<li><a href="change-password.php">Change password</a></li>
                <li><a href="update-email-address.php">Update email address</a></li>
 				<li><a href="logout.php">Logout</a></li>
<?php } else { ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
<?php } ?>
        </ul>

      </div>
    </div>
  </div>
