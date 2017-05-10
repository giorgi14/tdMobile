<?php
require_once('classes/core.php');

$user	   = $_SESSION['USERID'];
$status    = 0;

$result = mysqli_query("SELECT DATE_FORMAT(client_loan_schedule.pay_date, '%d.%m.%Y') AS `pay_date`,
                               CONCAT(client_loan_schedule.pay_amount,IF(client_loan_agreement.loan_currency_id = 1, ' GEL', ' $')) AS `pay_amount`,
                        	   client.`name` AS cl_name,
                    		   client.id AS cl_id,
                               client.phone,
                    		   client_loan_agreement.id AS agr_id,
                    		   CONCAT('(',client_car.car_marc,' ',client_car.registration_number,')') AS cl_car_info
                        FROM   client_loan_schedule
                        JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                        JOIN   client ON client.id = client_loan_agreement.client_id
                        JOIN   client_car ON client.id = client_car.client_id
                        WHERE  client_loan_schedule.actived = 1 AND client.actived = 1 
                        AND    client_loan_agreement.canceled_status = 0
                        AND    DATEDIFF(client_loan_schedule.pay_date,CURDATE()) IN (SELECT sent_day.count FROM sent_day WHERE sent_day.actived = 1)");

while ($row = mysql_fetch_array($result)) {
$text = 'TG MOBILE GATKOBINEBT ('.$row[cl_car_info].') GADASAXDELIA MIMDINARE DAVALIANEBA '.$row[pay_date].'-SHI . TANXA '.$row[pay_amount].'. AUCILEBLAD MIUTITED XELSHEKRULEBIS  N '.$row[agr_id].' DA GADAXDIS RICXVI. IQONIET GADAXDIS QVITARI. SAQARTVELOS BANKI (S/K ‎205270277) GE12BG0000000523102000 (DAZGVEVIS GADAXDA GIWEVT  06.05.2017-shi).';
$encodedtxt = urlencode($text);
$check 		= file_get_contents('http://msg.ge/bi/sendsms.php?username=calldato1&password=di48fj47sh0&client_id=330&service_id=0330&to=995598777210&text=hello)');
$check = 1;

    if($check){
    	$status = 1;
    	if ($id == '') {
    	    mysql_query("INSERT INTO `sent_sms` 
        					        (`user_id`, `datetime`, `client_id`, `address`, `content`, `status`, `actived`) 
        		              VALUES 
        					        ('$user', NOW(), '$row[cl_id]', '$row[phone]', '$text', '$status', '1')");
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
    	
    }
}
?>