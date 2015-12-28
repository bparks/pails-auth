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

	public $hash_pw = NULL;
	public $remember_me = NULL;
	public $remember_me_sessid = NULL;

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
		return $this->group->has_permission($permission) || Permission::user_has(strtolower($this->username), $permission);
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

			//Build the activation message
			$activation_message = lang("ACTIVATION_MESSAGE",array('http://'.$_SERVER['HTTP_HOST'].'/',$this->activation_token));

            //Copy some poorly-named attributes into ones that match the DB
            $this->username = $this->unclean_username;
            $this->username_clean = $this->clean_username;
            $this->password = $secure_pass;
            $this->email = $this->clean_email;
            $this->activationtoken = $this->activation_token;

            try {
                $mail = AuthMailer::new_registration($this, $activation_message);
                $mail->deliver();
            } catch (Exception $e) {
                \Pails\Application::log($e->getMessage());
                $this->mail_failure = true;
            }

            if(!$this->mail_failure)
            {
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

		//Simple function to update the last sign in of a user
	public function updatelast_sign_in()
	{
		$user = User::find($this->user_id);
		$user->last_sign_in = time();
		$user->save();

		return true;
	}

	//Return the timestamp when the user registered
	public function signupTimeStamp()
	{
		global $db,$db_table_prefix;

		$sql = "SELECT
				sign_up_date
				FROM
				".$db_table_prefix."users
				WHERE
				user_id = '".$db->sql_escape($this->user_id)."'";

		$result = $db->sql_query($sql);

		$row = $db->sql_fetchrow($result);

		return ($row['sign_up_date']);
	}

	//Update a users password
	public function updatepassword($pass)
	{
		$secure_pass = generateHash($pass);

		$this->hash_pw = $secure_pass;

		if($this->remember_me == 1)
			updateSessionObj();

		$user = User::find($this->user_id);
		$user->password = $secure_pass;
		$user->save();

		return true;
	}

	//Update a users email
	public function updateemail($email)
	{
		$this->email = $email;

		if($this->remember_me == 1)
			updateSessionObj();

		$user = User::find($this->user_id);
		$user->email = $email;
		$user->save();

		return true;
	}

	//Fetch all user group information
	// public function groupID()
	// {
	// 	global $db,$db_table_prefix;
    //
	// 	$sql = "SELECT ".$db_table_prefix."users.group_id,
	// 		   ".$db_table_prefix."groups.*
	// 		   FROM ".$db_table_prefix."users
	// 		   INNER JOIN ".$db_table_prefix."groups ON ".$db_table_prefix."users.group_id = ".$db_table_prefix."groups.group_id
	// 		   WHERE
	// 		   user_id  = '".$db->sql_escape($this->user_id)."'";
    //
	// 	$result = $db->sql_query($sql);
    //
	// 	$row = $db->sql_fetchrow($result);
    //
	// 	return($row);
	// }
    //
	// //Is a user member of a group
	// public function isGroupMember($id)
	// {
	// 	global $db,$db_table_prefix;
    //
	// 	$sql = "SELECT ".$db_table_prefix."users.group_id,
	// 			".$db_table_prefix."groups.* FROM ".$db_table_prefix."users
	// 			INNER JOIN ".$db_table_prefix."groups ON ".$db_table_prefix."users.group_id = ".$db_table_prefix."groups.group_id
	// 			WHERE user_id  = '".$db->sql_escape($this->user_id)."'
	// 			AND
	// 			".$db_table_prefix."users.group_id = '".$db->sql_escape($db->sql_escape($id))."'
	// 			LIMIT 1
	// 			";
    //
	// 	if(returns_result($sql))
	// 		return true;
	// 	else
	// 		return false;
    //
	// }

	//Logout
	function userLogOut()
	{
		destroySession(AUTH_COOKIE_NAME);
	}
}

?>
