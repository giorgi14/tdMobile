<?php
require_once('classes/core.php');

$error	   = '';
$data	   = '';

$id        = $_REQUEST['id'];
$client_id = $_REQUEST['client_id'];
$sms_phone = $_REQUEST['sms_phone'];
$text      = $_REQUEST['sms_text'];
$user	   = $_SESSION['USERID'];
$status    = 0;

$encodedtxt = urlencode($text);
$check 		= file_get_contents('http://msg.ge/bi/sendsms.php?username=calldato1&password=di48fj47sh0&client_id=330&service_id=0330&to='.$sms_phone.'&text='.$encodedtxt.')');

if($check){
	$status = 1;
	if ($id == '') {
	    mysql_query("INSERT INTO `sent_sms` 
    					        (`user_id`, `datetime`, `client_id`, `address`, `content`, `status`, `actived`) 
    		              VALUES 
    					        ('$user', NOW(), '$client_id', '$sms_phone', '$text', '$status', '1')");
	}else{
	    mysql_query("UPDATE `sent_sms`
            	        SET `user_id`   = '$user',
	                        `datetime`  =  NOW(),
                	        `client_id` = '$client_id',
                	        `address`   = '$sms_phone',
                	        `content`   = '$text',
	                        `status`    = '$status'
            	     WHERE  `id`        = '$id'");
	}
	
	$data = array("status" => $status);
}else{
	$status = 0;
	
	if ($id=='') {
	    mysql_query("INSERT INTO `sent_sms`
                    	        (`user_id`, `datetime`, `client_id`, `address`, `content`, `status`, `actived`)
                    	  VALUES
                    	        ('$user', NOW(), '$client_id', '$sms_phone', '$text', '$status', '1')");
	}else{
	    mysql_query("UPDATE `sent_sms`
        	            SET `user_id`   = '$user',
	                        `datetime`  =  NOW(),
                	        `client_id` = '$client_id',
                	        `address`   = '$sms_phone',
                	        `content`   = '$text',
                	        `status`    = '$status'
        	         WHERE  `id`        = '$id'");
	}
	
	$data = array("status" => $status);
}

$data['error'] = $error;


echo json_encode($data);

?>