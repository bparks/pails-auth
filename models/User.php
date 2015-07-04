<?php
/*
	UserPie Version: 1.0
	http://userpie.com

*/


class User extends ActiveRecord\Model
{
	static $table_name = "users";
	static $primary_key = "user_id";

	static $belongs_to = array(
		array('group')
	);

	static $has_many = array(
		//array('groups'),
		array('permissions')
	);

	public $user_active = 0;
	private $clean_email;
	public $status = false;
	private $clean_password;
	private $clean_username;
	private $unclean_username;
	public $sql_failure = false;
	public $mail_failure = false;
	public $email_taken = false;
	public $username_taken = false;
	public $activation_token = 0;

	static function register($username,$pass,$email)
	{
		$user = new User();
		//Used for display only
		$user->unclean_username = $username;

		//Sanitize
		$user->clean_email = sanitize($email);
		$user->clean_password = trim($pass);
		$user->clean_username = sanitize($username);

		if(usernameExists($user->clean_username))
		{
			$user->username_taken = true;
		}
		else if(emailExists($user->clean_email))
		{
			$user->email_taken = true;
		}
		else
		{
			//No problems have been found.
			$user->status = true;
		}
		return $user;
	}

	public function has_permission($permission)
	{
		return $this->group->has_permission($permission) || Permission::user_has($this->username, $permission);
	}

	public function userPieAddUser()
	{

		//Prevent this function being called if there were construction errors
		if($this->status)
		{
			//Construct a secure hash for the plain text password
			$secure_pass = generateHash($this->clean_password);

			//Construct a unique activation token
			$this->activation_token = generateactivationtoken();

			//User must activate their account first
			$this->user_active = 0;

			$mail = new userPieMail();

			//Build the activation message
			$activation_message = lang("ACTIVATION_MESSAGE",array('http://'.$_SERVER['HTTP_HOST'].'/',$this->activation_token));

			//Define more if you want to build larger structures
			$hooks = array(
				"searchStrs" => array("#ACTIVATION-MESSAGE","#ACTIVATION-KEY","#USERNAME#"),
				"subjectStrs" => array($activation_message,$this->activation_token,$this->unclean_username)
			);

			/* Build the template - Optional, you can just use the sendMail function
			Instead to pass a message. */
			if(!$mail->newTemplateMsg("new-registration.txt",$hooks))
			{
				$this->mail_failure = true;
			}
			else
			{
				//Send the mail. Specify users email here and subject.
				//SendMail can have a third parementer for message if you do not wish to build a template.

				if(!$mail->sendMail($this->clean_email,"Welcome"))
				{
					$this->mail_failure = true;
				}
			}


			if(!$this->mail_failure)
			{
				$this->username = $this->unclean_username;
				$this->username_clean = $this->clean_username;
				$this->password = $secure_pass;
				$this->email = $this->clean_email;
				$this->activationtoken = $this->activation_token;
				$this->last_activation_request = time();
				$this->lostpasswordrequest = 0;
				$this->active = $this->user_active;
				$this->group_id = 1;
				$this->sign_up_date = time();
				$this->last_sign_in = 0;
				$this->save();

				return true;
			}
		}
	}
}

?>
