<?php
require_once('classes/core.php');

$user = $_SESSION['USERID'];

$result = mysql_query("SELECT DATE_FORMAT(client_loan_schedule.pay_date, '%d.%m.%Y') AS `pay_date`,
                               CONCAT(client_loan_schedule.pay_amount,IF(client_loan_agreement.loan_currency_id = 1, ' LARI', ' $')) AS `pay_amount`,
                        	   client.`name` AS cl_name,
                    		   client.id AS cl_id,
                               client.phone,
                    		   client_loan_agreement.id AS agr_id,
                    		   CONCAT(client_car.car_marc,' ',client_car.registration_number) AS cl_car_info,
                               client.sms_sent
                        FROM   client_loan_schedule
                        JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                        JOIN   client ON client.id = client_loan_agreement.client_id
                        JOIN   client_car ON client.id = client_car.client_id
                        WHERE  client_loan_schedule.actived = 1 AND client.actived = 1 
                        AND    client_loan_agreement.canceled_status = 0
                        AND    DATEDIFF(client_loan_schedule.pay_date,CURDATE()) IN(SELECT sent_day.count FROM sent_day WHERE sent_day.actived = 1)");

while ($row = mysql_fetch_array($result)) {
    
    $result1 = mysql_query("SELECT    client_quarantors.id,
                            		  client_quarantors.phone,
                                      '1' AS check_client
                            FROM      client_quarantors 
                            WHERE     client_id = '$row[cl_id]' 
                            AND       client_quarantors.actived = 1 
                            AND       client_quarantors.sms_sent = 1
                            UNION ALL
                            SELECT    client_person.id,
                            		  client_person.phone,
                                      '2' AS check_client
                            FROM      client_person 
                            WHERE     client_id = $row[cl_id] 
                            AND       client_person.actived = 1 
                            AND       client_person.sms_sent = 1
                            UNION ALL
                            SELECT    client_trusted_person.id,
                                      client_trusted_person.`phone`,
                                      '3' AS check_client
                            FROM      client_trusted_person
                            WHERE     client_trusted_person.sent_sms = 1
                            AND       client_trusted_person.client_id = '$row[cl_id]'");
    
    $text = 'TG MOBILE GATKOBINEBT ('.$row[cl_car_info].') GADASAXDELIA MIMDINARE DAVALIANEBA '.$row[pay_date].'-SHI . TANXA '.$row[pay_amount].'. AUCILEBLAD MIUTITED XELSHEKRULEBIS  N '.$row[agr_id].' DA GADAXDIS RICXVI. IQONIET GADAXDIS QVITARI. SAQARTVELOS BANKI (S/K ‎205270277) GE12BG0000000523102000 (DAZGVEVIS GADAXDA GIWEVT  06.05.2017-shi).';
    $encodedtxt = urlencode($text);
    
    if ($row[sms_sent] == 1) {
        $check = file_get_contents('http://msg.ge/bi/sendsms.php?username=calldato1&password=di48fj47sh0&client_id=330&service_id=0330&to='.$row[phone].'&text='.$encodedtxt.')');
    }
    
    while ($row1 = mysql_fetch_array($result1)) {
        $check1 = file_get_contents('http://msg.ge/bi/sendsms.php?username=calldato1&password=di48fj47sh0&client_id=330&service_id=0330&to='.$row1[phone].'&text='.$encodedtxt.')');
        
        if ($row1[check_client]==1) {
            $guarantor_id = $row1[id]; 
        }elseif ($row1[check_client]==2){
            $client_person_id = $row1[id];
        }elseif ($row1[check_client]==3){
            $trusted_person_id = $row1[id];
        }
        
        if($check1){
            mysql_query("INSERT INTO `sent_sms`
                                    (`user_id`, `datetime`, `client_id`, `guarantor_id`, `person_id`, `trust_person_id`, `address`, `content`, `status`, `actived`)
                              VALUES
                                    ('$user', NOW(), '$row[cl_id]', '$guarantor_id', '$client_person_id', '$trusted_person_id', '$row1[phone]', '$text', '1', '1')");
        }else{
            mysql_query("INSERT INTO `sent_sms`
                                    (`user_id`, `datetime`, `client_id`, `guarantor_id`, `person_id`, `trust_person_id`, `address`, `content`, `status`, `actived`)
                              VALUES
                                    ('$user', NOW(), '$row[cl_id]', '$guarantor_id', '$client_person_id', '$trusted_person_id', '$row1[phone]', '$text', '1', '1')");
        }
    }
    
    if ($row[sms_sent] == 1) {
        if($check){
            mysql_query("INSERT INTO `sent_sms` 
        					        (`user_id`, `datetime`, `client_id`, `address`, `content`, `status`, `actived`) 
        		              VALUES 
        					        ('$user', NOW(), '$row[cl_id]', '$row[phone]', '$text', '1', '1')");
        }else{
        	mysql_query("INSERT INTO `sent_sms`
                        	        (`user_id`, `datetime`, `client_id`, `address`, `content`, `status`, `actived`)
                        	  VALUES
                        	        ('$user', NOW(), '$client_id', '$sms_phone', '$text', '0', '1')");
        }
    }
}

?>