<?php
class Session extends ActiveRecord\Model
{
	static $table_name = "userpie_sessions";
	static $primary_key = "session_id";
}