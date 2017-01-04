<?php 
require_once('../includes/classes/core.php');

$ip = $_SERVER['REMOTE_ADDR'];

mysql_query("UPDATE users
             SET    users.last_actived_time = NOW(),
                    users.last_ip = '$ip'
             WHERE  users.id = $_SESSION[USERID]");

$array= array();
echo  json_encode($array);

?>