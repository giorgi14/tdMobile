<?php
require_once('classes/core.php');

$user = $_SESSION['USERID'];

$result = mysql_query("SELECT  DATE_FORMAT(client_loan_schedule.schedule_date, '%d.%m.%Y') AS `pay_date`,
                               client_loan_schedule.id,
                               client_loan_agreement.loan_currency_id,
                               client_loan_schedule.pay_amount AS `pay_amount`,
                        	   client.`name` AS cl_name,
                    		   client.id AS cl_id,
                               client.phone,
                    		   CASE
        						   WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number),'',' დ.'), IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number), '', client_loan_agreement.attachment_number))
        						   WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
                                   WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
                                   WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
            			       END AS agr_id,
                    		   CONCAT(client_car.car_marc,' ',client_car.registration_number) AS cl_car_info,
                               client.sms_sent,
                               client_loan_schedule.penalty AS penalty
                        FROM   client_loan_schedule
                        JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                        JOIN   client ON client.id = client_loan_agreement.client_id
                        JOIN   client_car ON client.id = client_car.client_id
                        WHERE  client_loan_schedule.actived = 1 AND client.actived = 1
                        AND    client_loan_schedule.status = 0
                        AND    client_loan_agreement.canceled_status = 0
                        AND    DATEDIFF(client_loan_schedule.schedule_date,CURDATE()) IN(SELECT sent_day.count FROM sent_day WHERE sent_day.actived = 1)");

while ($row = mysql_fetch_array($result)) {
    
    $avans = mysql_fetch_array(mysql_query("SELECT SUM(money_transactions_detail.pay_amount) AS avansi,
                                                   money_transactions_detail.received_currency_id,
                                                   money_transactions_detail.course
                                            FROM   money_transactions
                                            JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                            WHERE  money_transactions.client_loan_schedule_id = '$row[id]'
                                            AND    money_transactions.actived = 1
                                            AND    money_transactions.`status` = 3"));
    
    $ins = mysql_fetch_array(mysql_query("SELECT   CONCAT(car_insurance_info.ins_payy,' $') AS inc_amount,
                                            	   DATE_FORMAT(car_insurance_info.car_insurance_end, '%d.%m.%Y') AS ins_pay_date
                                          FROM     car_insurance_info 
                                          WHERE    car_insurance_info.client_id = '$row[cl_id]' AND car_insurance_info.actived = 1
                                          ORDER BY car_insurance_info.id DESC
                                          LIMIT 1"));
    
    
   
    $check_avans = 0;
    
    if ($row[loan_currency_id] == 1 && $avans[received_currency_id]==1) {
        
        if ($row[pay_amount] <= $avans[avansi]) {
            
            $check_avans=1;
            
        }
        
    }elseif ($row[loan_currency_id] == 1 && $avans[received_currency_id]==2){
        
        $cl_avans = round($avans[avansi]*$avans[cource],2);
        
        if ($row[pay_amount]<=$cl_avans) {
            
            $check_avans=1;
            
        }
        
    }elseif ($row[loan_currency_id] == 2 && $avans[received_currency_id]==1){
        
        $cl_avans = round($avans[avansi]/$avans[cource],2);
        
        if ($row[pay_amount]<=$cl_avans) {
            
            $check_avans=1;
            
        }
        
    }elseif ($row[loan_currency_id] == 2 && $avans[received_currency_id]==2){
        
        if ($row[pay_amount] <= $avans[avansi]) {
            
            $check_avans=1;
            
        }
        
    }
    
    
    
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
    if ($row[penalty] == '') {
        $penalti = 0;
    }else{
        $penalti = $row[penalty];
    }
    
    if ($row[loan_currency_id] == 1) {
        $tanxa = ($row[pay_amount]+$penalti).' LARI';   
    }elseif ($row[loan_currency_id] == 2){
        $tanxa = ($row[pay_amount]+$penalti).' $'; 
    }
    
    if ($row[pay_date] >= $ins[ins_pay_date]) {
        $gadasaxdeli = $tanxa.'+'.$ins[inc_amount].'. (procenti + dazgveva)';
        $dazgvevis_shexseneba = '';
    }else{
        $gadasaxdeli = $tanxa;
        $dazgvevis_shexseneba = '(DAZGVEVIS GADAXDA GIWEVT  '.$ins[ins_pay_date].'-shi)';
    }
    
    $text = 'TG MOBILE GATKOBINEBT ('.$row[cl_car_info].') GADASAXDELIA MIMDINARE DAVALIANEBA '.$row[pay_date].'-SHI . TANXA '.$gadasaxdeli.'. AUCILEBLAD MIUTITED XELSHEKRULEBIS  N '.$row[agr_id].' DA GADAXDIS RICXVI. IQONIET GADAXDIS QVITARI. SAQARTVELOS BANKI (S/K ‎205270277) GE12BG0000000523102000 '.$dazgvevis_shexseneba.'.';
    $encodedtxt = urlencode($text);
    
    while ($row1 = mysql_fetch_array($result1)) {
        
        $guarantor_id      = 0;
        $client_person_id  = 0;
        $trusted_person_id = 0;
        
        if ($row1[check_client]==1) {
            $guarantor_id = $row1[id]; 
        }elseif ($row1[check_client]==2){
            $client_person_id = $row1[id];
        }elseif ($row1[check_client]==3){
            $trusted_person_id = $row1[id];
        }
        
        mysql_query("INSERT INTO `sent_list`
                                (`user_id`, `datetime`, `client_id`, `guarantor_id`, `person_id`, `trust_person_id`, `address`, `content`, `status`, `actived`)
                          VALUES
                                ('$user', NOW(), '$row[cl_id]', '$guarantor_id', '$client_person_id', '$trusted_person_id', '$row1[phone]', '$text', '0', '1')");
       
    }
    
    if ($row[sms_sent] == 1) {
         mysql_query("INSERT INTO `sent_list`
                    	        (`user_id`, `datetime`, `client_id`, `address`, `content`, `status`, `actived`)
                    	  VALUES
                    	        ('$user', NOW(), '$row[cl_id]', '$row[phone]', '$text', '0', '1')");
        
    }
}

?>