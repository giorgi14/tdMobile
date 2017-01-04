<?php
include 'core.php';

class Authorization {
	
	private  $username;
	private  $password;
	private  $ext;
	
	private  $user_id;
	private  $sess_id;
	
	private  $date;
	private  $ip;
	
	private  $time = 3699999;
	
	
	/**
	 * constructor
	 */
	function Authorization() {
	}
	
	/**
	 * @param string $username სისტემის მომხმარებელი
	 */
	function set_username($username) {
		$this->username = $username;
	}
	
	/**
	 * @param string $password მომხმარებლის პაროლი
	 */
	function set_password($password) {
		$this->password = md5($password);
	}
	
	function set_ext($ext) {
	    $this->ext = $ext;
	}
	/**
	 * სისტემის მომხარებლის შემოწმება
	 * @return boolean
	 */
	function checklogin() {
	   if ($this->username != '' && $this->password != '' ) {
			$result = mysql_query("	SELECT 	`id`,
			                                 group_id
									 FROM	`users`
									 WHERE	`password` = '$this->password' AND username = '$this->username' AND `actived` = 1");
			
			if (mysql_num_rows($result) == 1) {
			
			    $uid = mysql_fetch_assoc($result);
			    $this->user_id = $uid['id'];
			
			    return true;
			}else{
			    return false;
			}
		}
		
	}
	
	function ip()
	{
		global $REMOTE_ADDR;
		global $HTTP_X_FORWARDED_FOR, $HTTP_X_FORWARDED, $HTTP_FORWARDED_FOR, $HTTP_FORWARDED;
		global $HTTP_VIA, $HTTP_X_COMING_FROM, $HTTP_COMING_FROM;
		if (empty($REMOTE_ADDR)) {
			if (!empty($_SERVER) && isset($_SERVER['REMOTE_ADDR'])) {
				$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
			}
			else if (!empty($_ENV) && isset($_ENV['REMOTE_ADDR'])) {
				$REMOTE_ADDR = $_ENV['REMOTE_ADDR'];
			}
			else if (@getenv('REMOTE_ADDR')) {
				$REMOTE_ADDR = getenv('REMOTE_ADDR');
			}
		}
		if (empty($HTTP_X_FORWARDED_FOR)) {
			if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else if (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED_FOR'])) {
				$HTTP_X_FORWARDED_FOR = $_ENV['HTTP_X_FORWARDED_FOR'];
			}
			else if (@getenv('HTTP_X_FORWARDED_FOR')) {
				$HTTP_X_FORWARDED_FOR = getenv('HTTP_X_FORWARDED_FOR');
			}
		}
		if (empty($HTTP_X_FORWARDED)) {
			if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED'])) {
				$HTTP_X_FORWARDED = $_SERVER['HTTP_X_FORWARDED'];
			}
			else if (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED'])) {
				$HTTP_X_FORWARDED = $_ENV['HTTP_X_FORWARDED'];
			}
			else if (@getenv('HTTP_X_FORWARDED')) {
				$HTTP_X_FORWARDED = getenv('HTTP_X_FORWARDED');
			}
		}
		if (empty($HTTP_FORWARDED_FOR)) {
			if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED_FOR'])) {
				$HTTP_FORWARDED_FOR = $_SERVER['HTTP_FORWARDED_FOR'];
			}
			else if (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED_FOR'])) {
				$HTTP_FORWARDED_FOR = $_ENV['HTTP_FORWARDED_FOR'];
			}
			else if (@getenv('HTTP_FORWARDED_FOR')) {
				$HTTP_FORWARDED_FOR = getenv('HTTP_FORWARDED_FOR');
			}
		}
		if (empty($HTTP_FORWARDED)) {
			if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED'])) {
				$HTTP_FORWARDED = $_SERVER['HTTP_FORWARDED'];
			}
			else if (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED'])) {
				$HTTP_FORWARDED = $_ENV['HTTP_FORWARDED'];
			}
			else if (@getenv('HTTP_FORWARDED')) {
				$HTTP_FORWARDED = getenv('HTTP_FORWARDED');
			}
		}
		if (empty($HTTP_VIA)) {
			if (!empty($_SERVER) && isset($_SERVER['HTTP_VIA'])) {
				$HTTP_VIA = $_SERVER['HTTP_VIA'];
			}
			else if (!empty($_ENV) && isset($_ENV['HTTP_VIA'])) {
				$HTTP_VIA = $_ENV['HTTP_VIA'];
			}
			else if (@getenv('HTTP_VIA')) {
				$HTTP_VIA = getenv('HTTP_VIA');
			}
		}
		if (empty($HTTP_X_COMING_FROM)) {
			if (!empty($_SERVER) && isset($_SERVER['HTTP_X_COMING_FROM'])) {
				$HTTP_X_COMING_FROM = $_SERVER['HTTP_X_COMING_FROM'];
			}
			else if (!empty($_ENV) && isset($_ENV['HTTP_X_COMING_FROM'])) {
				$HTTP_X_COMING_FROM = $_ENV['HTTP_X_COMING_FROM'];
			}
			else if (@getenv('HTTP_X_COMING_FROM')) {
				$HTTP_X_COMING_FROM = getenv('HTTP_X_COMING_FROM');
			}
		}
		if (empty($HTTP_COMING_FROM)) {
			if (!empty($_SERVER) && isset($_SERVER['HTTP_COMING_FROM'])) {
				$HTTP_COMING_FROM = $_SERVER['HTTP_COMING_FROM'];
			}
			else if (!empty($_ENV) && isset($_ENV['HTTP_COMING_FROM'])) {
				$HTTP_COMING_FROM = $_ENV['HTTP_COMING_FROM'];
			}
			else if (@getenv('HTTP_COMING_FROM')) {
				$HTTP_COMING_FROM = getenv('HTTP_COMING_FROM');
			}
		}
	
		if (!empty($REMOTE_ADDR)) {
			$direct_ip = $REMOTE_ADDR;
		}
	
		$proxy_ip	 = '';
		if (!empty($HTTP_X_FORWARDED_FOR)) {
			$proxy_ip = $HTTP_X_FORWARDED_FOR;
		} else if (!empty($HTTP_X_FORWARDED)) {
			$proxy_ip = $HTTP_X_FORWARDED;
		} else if (!empty($HTTP_FORWARDED_FOR)) {
			$proxy_ip = $HTTP_FORWARDED_FOR;
		} else if (!empty($HTTP_FORWARDED)) {
			$proxy_ip = $HTTP_FORWARDED;
		} else if (!empty($HTTP_VIA)) {
			$proxy_ip = $HTTP_VIA;
		} else if (!empty($HTTP_X_COMING_FROM)) {
			$proxy_ip = $HTTP_X_COMING_FROM;
		} else if (!empty($HTTP_COMING_FROM)) {
			$proxy_ip = $HTTP_COMING_FROM;
		}
	
		if (empty($proxy_ip)) {
			$this->ip =  $direct_ip;
		} else {
			$is_ip = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxy_ip, $regs);
			if ($is_ip && (count($regs) > 0)) {
				$this->ip =  $regs[0];
			} else {
				$this->ip =  'unknow';
			}
		} 
	}
	
	/**
	 * სესიის მონაცემების შენახვა
	 */
	function savelogin() {
						
		$_SESSION['USERID']   = $this->user_id;
		$_SESSION['USERGR']   = $this->group_id;
		
		$_SESSION['lifetime'] = time();

		session_regenerate_id();
		$this->sess_id 	= session_id();
		$this->date		= date("Y-m-d H:i:s");
			
		mysql_query("INSERT INTO `user_log`
                     (`user_id`, `session_id`, `ip`, `login_date`)
                     VALUES
                     ($this->user_id, '$this->sess_id', '$this->ip', '$this->date')");
		
		mysql_query("UPDATE `users` SET 
                		    `logged`     = '1', 
                		    `login_date` = '$this->date',
                			`ip` 		 = '$this->ip' 
	                 WHERE  `id`         = $this->user_id");
        
	}
	
	function expire($time){
		$this->expire = $time;
		session_cache_limiter('private');
		session_cache_expire($time / 60);
	}
	
	function logout(){
		session_start();
		session_destroy();
		$date = date("Y-m-d H:i:s");
		$user_id = $_SESSION['USERID'];
		mysql_query("UPDATE `user_log` SET
                            `logout_date`='$date'
                     WHERE  `user_id` = '$user_id' AND ISNULL(logout_date)");
		
		mysql_query("UPDATE `users` 
		                SET `logged`='0' 
		             WHERE `id` = $user_id");
		unset($_SESSION['USERID']);	
		unset($_SESSION['lifetime']);
		return true;
	}
}

?>