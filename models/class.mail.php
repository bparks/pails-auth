<?php
/*
	UserPie Version: 1.0
	http://userpie.com
	
*/

class userPieMail {

	//UserPie uses a text based system with hooks to replace various strs in txt email templates
	public $contents = NULL;

	//Function used for replacing hooks in our templates
	public function newTemplateMsg($template,$additionalHooks)
	{
	
		$this->contents = file_get_contents(__DIR__."/mail-templates/".$template);

		//Check to see we can access the file / it has some contents
		if(!$this->contents || empty($this->contents))
		{
			if(Pails\Application::environment() == 'development')
			{
				if(!$this->contents)
				{ 
					echo lang("MAIL_TEMPLATE_DIRECTORY_ERROR",array(getenv("DOCUMENT_ROOT")));
							
					die(); 
				}
				else if(empty($this->contents))
				{
					echo lang("MAIL_TEMPLATE_FILE_EMPTY"); 
					
					die();
				}
			}
		
			return false;
		}
		else
		{
			//Replace default hooks
			$this->contents = replaceDefaultHook($this->contents);
		
			//Replace defined / custom hooks
			$this->contents = str_replace($additionalHooks["searchStrs"],$additionalHooks["subjectStrs"],$this->contents);

			//Do we need to include an email footer?
			//Try and find the #INC-FOOTER hook
			if(strpos($this->contents,"#INC-FOOTER#") !== FALSE)
			{
				$footer = file_get_contents(__DIR__."/mail-templates/"."email-footer.txt");
				if($footer && !empty($footer)) $this->contents .= replaceDefaultHook($footer); 
				$this->contents = str_replace("#INC-FOOTER#","",$this->contents);
			}
			
			return true;
		}
	}
	
	public function sendMail($email,$subject,$msg = NULL)
	{	
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$header .= "From: ". $_SERVER['SERVER_NAME'] . " <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n";
		
		 
		//Check to see if we sending a template email.
		if($msg == NULL)
			$msg = $this->contents;

		$message = wordwrap($msg, 70);
			
		return mail($email,$subject,$message,$header);
	}
}


?>
