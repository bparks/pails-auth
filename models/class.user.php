<?php
/*
	UserPie Version: 1.0
	http://userpie.com
	
*/

class loggedInUser {

	public $email = NULL;
	public $hash_pw = NULL;
	public $user_id = NULL;
	public $clean_username = NULL;
	public $display_username = NULL;
	public $remember_me = NULL;
	public $remember_me_sessid = NULL;
	
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
	public function groupID()
	{
		global $db,$db_table_prefix;
		
		$sql = "SELECT ".$db_table_prefix."users.group_id, 
			   ".$db_table_prefix."groups.* 
			   FROM ".$db_table_prefix."users
			   INNER JOIN ".$db_table_prefix."groups ON ".$db_table_prefix."users.group_id = ".$db_table_prefix."groups.group_id 
			   WHERE
			   user_id  = '".$db->sql_escape($this->user_id)."'";
		
		$result = $db->sql_query($sql);
		
		$row = $db->sql_fetchrow($result);

		return($row);
	}
	
	//Is a user member of a group
	public function isGroupMember($id)
	{
		global $db,$db_table_prefix;
	
		$sql = "SELECT ".$db_table_prefix."users.group_id, 
				".$db_table_prefix."groups.* FROM ".$db_table_prefix."users 
				INNER JOIN ".$db_table_prefix."groups ON ".$db_table_prefix."users.group_id = ".$db_table_prefix."groups.group_id
				WHERE user_id  = '".$db->sql_escape($this->user_id)."'
				AND
				".$db_table_prefix."users.group_id = '".$db->sql_escape($db->sql_escape($id))."'
				LIMIT 1
				";
		
		if(returns_result($sql))
			return true;
		else
			return false;
		
	}
	
	//Logout
	function userLogOut()
	{
		destroySession("userPieUser");
	}

}
?>