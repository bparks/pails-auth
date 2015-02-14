<div class="modal-ish">
	<div class="modal-header">
		<h2>Activation</h2>
	</div>
	<div class="modal-body">
		<?php
		if(count($this->model) > 0):
	    	errorBlock($errors);
		else:
		?> 
       <p>Congratulations! You've successfully activated your new account. You may now <a href="/session/login">login.</a></p>   		 
		<?php
		endif;
		?>
	</div>
</div>


	
	
	


