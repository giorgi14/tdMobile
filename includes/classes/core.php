<?php
session_start();
class sql_db {
	public $db_connect_id;
	public $user;
	public $password;
	public $server;
	public $database;
	public $table;
	public $result;
	
	function sql_db($sqlserver, $sqluser, $sqlpassword, $database) {
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;
		
		$this->db_connect_id = mysql_connect ( $this->server, $this->user, $this->password );
		
		if ($this->db_connect_id) {
			if ($database != "") {
				$this->dbname = $database;
				$dbselect = mysql_select_db ( $this->dbname );
				//$dbselect = mysql_set_charset ( 'utf8', $this->db_connect_id );
				mysql_query("set names 'utf8'");
				
				if (! $dbselect) {
					mysql_close ( $this->db_connect_id );
					$this->db_connect_id = $dbselect;
				}
			}
			
			return $this->db_connect_id;
		} else {
			return false;
		}
	}
	
	function increment($table){
		$this->table = $table;
	
		$result   		= mysql_query("SHOW TABLE STATUS LIKE '$this->table'");
		$row   			= mysql_fetch_array($result);
		$increment   	= $row['Auto_increment'];
		$next_increment = $increment+1;
		mysql_query("ALTER TABLE $this->table AUTO_INCREMENT=$next_increment");
	
		return $increment;
	}
	
}


	$db = new sql_db ("212.72.155.176", "root", "Gl-1114", "tg_mobile" );

	
	function GetUserGroup($user_id){
	    $req = mysql_fetch_array(
	           mysql_query("SELECT `group_id`
                            FROM   `users`
                            WHERE  `id` = $user_id")
	           );
	    return $req[0];
	}

?>
