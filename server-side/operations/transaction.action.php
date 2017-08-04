<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$id		= $_REQUEST['id'];
	    $page	= GetPage(GetHolidays($id));
        $data	= array('page'	=> $page);

		break;
		
	case 'delete_transaction':
		$id      = $_REQUEST['tr_id'];
		$user_id = $_SESSION['USERID'];
		
	    mysql_query("UPDATE `money_transactions`
	                    SET `user_id`                    = '$user_id',
                            `money_transactions`.actived = '0'
                     WHERE   id                          = '$id'");

		break;
		
	case 'delete_transaction':
	    $id      = $_REQUEST['tr_id'];
	    $user_id = $_SESSION['USERID'];
	
	    mysql_query("UPDATE `money_transactions_detail`
	                    SET `user_id` = '$user_id',
	                         actived  = '0'
	                  WHERE  transaction_id  = '$id'");
	
	    break;
	    
	case 'restore_transaction':
		$id      = $_REQUEST['tr_id'];
		$user_id = $_SESSION['USERID'];
		
	    mysql_query("UPDATE `money_transactions`
	                    SET `user_id`                    = '$user_id',
                            `money_transactions`.actived = '1'
                     WHERE   id                          = '$id'");

		break;
	case 'get_list' :
		$count	 = $_REQUEST['count'];
		$hidden	 = $_REQUEST['hidden'];
		$tab	 = $_REQUEST['tab'];
		
		$where   = '';
		$actived = 1;
		
		if ($tab > 0) {
		    $where="AND money_transactions.type_id=$tab";
		    if ($tab == 4) {
		        $actived = 0;
		        $where="AND money_transactions.type_id=0";
		    }
		    
		}
		 
		if ($tab == 1) {
		    $val = '(SELECT SUM(money_transactions_detail.pay_root) FROM money_transactions_detail WHERE money_transactions_detail.transaction_id = money_transactions.id AND money_transactions_detail.status = 1 AND NOT ISNULL(money_transactions_detail.pay_root)),
				    (SELECT SUM(money_transactions_detail.pay_percent) FROM money_transactions_detail WHERE money_transactions_detail.transaction_id = money_transactions.id AND money_transactions_detail.status = 1 AND NOT ISNULL(money_transactions_detail.pay_root)),
                    IFNULL((SELECT SUM(money_transactions_detail.pay_percent) FROM money_transactions_detail WHERE money_transactions_detail.transaction_id = money_transactions.id AND money_transactions_detail.status = 3 AND NOT ISNULL(money_transactions_detail.pay_amount)),0.00),';
		}else{
		    $val = '';
		    if ($tab==0) {
		        $where_status = 'AND money_transactions.status = 0';
		    }
		    
		}
		
		
		
		$rResult = mysql_query("SELECT     money_transactions.id,
                                    	   DATE_FORMAT(money_transactions.pay_datetime,'%d/%m/%Y'),
		                                   client_loan_agreement.oris_code,
                                    	   CASE
                                               WHEN client.id >= (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                                               WHEN client.id < (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client.exel_agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                                           END AS `name`,
                                    	   money_transactions.pay_amount,
                                           IFNULL(loan_currency.name,ln_currency.name),
		                                   money_transactions.course,
		                                   $val
                                           IF(money_transactions.`status` = 0,'დაუდასტურებელი','დადასტურებული'),
		                                   user_info.`name`,
		                                   DATE_FORMAT(money_transactions.datetime,'%d/%m/%Y')
                                 FROM     `money_transactions`
                                 LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
		                         LEFT JOIN loan_currency ON loan_currency.id = money_transactions.currency_id
		                         LEFT JOIN loan_currency AS ln_currency ON ln_currency.id = money_transactions.received_currency_id
		                         LEFT JOIN transaction_type ON transaction_type.id = money_transactions.type_id
                                 LEFT JOIN client ON client.id = money_transactions.client_id
		                         LEFT JOIN client_car ON client_car.client_id = client.id
		                         LEFT JOIN user_info ON user_info.user_id = money_transactions.user_id
		                         WHERE     money_transactions.actived = '$actived' AND money_transactions.type_id != 4 AND money_transactions.id > 59 $where_status $where 
		                         ORDER BY money_transactions.pay_datetime DESC");

		$data = array("aaData"	=> array());

		while ($aRow = mysql_fetch_array($rResult)){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ )
			{
				/* General output */
				$row[] = $aRow[$i];
				
			}
			$data['aaData'][] = $row;
		}

		break;
	case 'get_list_pledge' :
	    
	    $count	 = $_REQUEST['count'];
	    $hidden	 = $_REQUEST['hidden'];
	    
	    $rResult = mysql_query("SELECT    money_transactions.id,
                        				  DATE_FORMAT(money_transactions.pay_datetime,'%d/%m/%Y'),
                        				  client_loan_agreement.oris_code,
                                          CASE
                    						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' დანართი ', client_loan_agreement.attachment_number, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT(client.ltd_name, 'ს/ხ', client_loan_agreement.oris_code, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    					  END AS new_name,
                        			      money_transactions.pay_amount,
                        				  IFNULL(loan_currency.name,ln_currency.name),
                            		      money_transactions.course,
                            		      IF(money_transactions.`status` = 0,'დაუდასტურებელი','დადასტურებული'),
                            		      user_info.`name`,
                                          DATE_FORMAT(money_transactions.datetime,'%d/%m/%Y'),
	                                      money_transaction_status.`name`
                                FROM     `money_transactions`
	                            JOIN      money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                JOIN      money_transaction_status ON money_transactions_detail.`status` = money_transaction_status.id
                                LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                LEFT JOIN loan_currency ON loan_currency.id = money_transactions.currency_id
                                LEFT JOIN loan_currency AS ln_currency ON ln_currency.id = money_transactions.received_currency_id
                                LEFT JOIN transaction_type ON transaction_type.id = money_transactions.type_id
                                LEFT JOIN client ON client.id = client_loan_agreement.client_id
                                LEFT JOIN client_car ON client_car.client_id = client.id
                                LEFT JOIN user_info ON user_info.user_id = money_transactions.user_id
                                WHERE     money_transactions.actived = '1' AND money_transactions.id > 59  AND money_transactions.type_id=2
                                ORDER BY  money_transactions.pay_datetime DESC");
	
	        $data = array("aaData"	=> array());
	
	        while ($aRow = mysql_fetch_array($rResult)){
	            $row = array();
	            for ( $i = 0 ; $i < $count ; $i++ )
	            {
	                /* General output */
	                $row[] = $aRow[$i];
	
	            }
	            $data['aaData'][] = $row;
	        }
	
	        break;
	    case 'get_list_other' :
    	    $count	 = $_REQUEST['count'];
    	    $hidden	 = $_REQUEST['hidden'];
    	    
    	    $rResult = mysql_query("SELECT    money_transactions.id,
                            				  DATE_FORMAT(money_transactions.pay_datetime,'%d/%m/%Y'),
                            				  client_loan_agreement.oris_code,
                                              CASE
                        						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' დანართი ', client_loan_agreement.attachment_number, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT(client.ltd_name, 'ს/ხ', client_loan_agreement.oris_code, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        					  END AS new_name,
                            			      money_transactions.pay_amount,
                            				  IFNULL(loan_currency.name,ln_currency.name),
                                		      money_transactions.course,
                                		      IF(money_transactions.`status` = 0,'დაუდასტურებელი','დადასტურებული'),
                                		      user_info.`name`,
                                              DATE_FORMAT(money_transactions.datetime,'%d/%m/%Y'),
    	                                      money_transaction_status.`name`
                                    FROM     `money_transactions`
    	                            JOIN      money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    JOIN      money_transaction_status ON money_transactions_detail.`status` = money_transaction_status.id
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    LEFT JOIN loan_currency ON loan_currency.id = money_transactions.currency_id
                                    LEFT JOIN loan_currency AS ln_currency ON ln_currency.id = money_transactions.received_currency_id
                                    LEFT JOIN transaction_type ON transaction_type.id = money_transactions.type_id
                                    LEFT JOIN client ON client.id = client_loan_agreement.client_id
                                    LEFT JOIN client_car ON client_car.client_id = client.id
                                    LEFT JOIN user_info ON user_info.user_id = money_transactions.user_id
                                    WHERE     money_transactions.actived = '1' AND money_transactions.id > 59  AND money_transactions.type_id=3
                                    ORDER BY  money_transactions.pay_datetime DESC");
	
	        $data = array("aaData"	=> array());
	
	        while ($aRow = mysql_fetch_array($rResult)){
	            $row = array();
	            for ( $i = 0 ; $i < $count ; $i++ )
	            {
	                /* General output */
	                $row[] = $aRow[$i];
	
	            }
	            $data['aaData'][] = $row;
	        }
	
	        break;
	case 'save_transaction':
		$tr_id 		          = $_REQUEST['tr_id'];
		$client_amount        = $_REQUEST['client_amount'];
		$received_currency_id = $_REQUEST['received_currency_id'];
		$transaction_date     = $_REQUEST['transaction_date'];
		
		save($tr_id, $client_amount, $transaction_date, $received_currency_id);
        
		break;
		
	case 'get_cource':
	    $transaction_date = $_REQUEST['transaction_date'];
	    $cource = mysql_fetch_array(mysql_query("SELECT cource FROM cur_cource WHERE DATE(datetime) = DATE('$transaction_date')"));
	
	    $data	= array('cource' => $cource[cource]);
	    break;
	    
    case 'check_transaction':
        $tr_id = $_REQUEST['tr_id'];
        $check = 0;
        $check_transaction = mysql_num_rows(mysql_query("SELECT money_transactions_detail.id
                                                         FROM   money_transactions_detail
                                                         JOIN   money_transactions ON money_transactions.id = money_transactions_detail.transaction_id 
                                                         WHERE  money_transactions.id = '$tr_id' AND money_transactions.actived = 1 AND money_transactions_detail.actived = 1"));
        if ($check_transaction>0) {
            $check=1;
        }
        $data	= array('check' => $check);
        break;
        
	case 'get_canceled-loan':
	    $local_id  = $_REQUEST['client_id'];
        $pay_datee = $_REQUEST['transaction_date'];

        
        $check_count = mysql_query("SELECT client_loan_schedule.id,
                                           DATEDIFF('$pay_datee', client_loan_schedule.pay_date) AS gadacilebuli,
                                           client_loan_agreement.penalty_days,
                            			   client_loan_agreement.penalty_percent,
                            			   client_loan_agreement.penalty_additional_percent,
                                           ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2) AS remaining_root
                                    FROM   client_loan_schedule 
                                    JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE  client_loan_agreement_id = '$local_id'
                                    AND    DATE(client_loan_schedule.schedule_date)<='$pay_datee'
                                    AND    client_loan_schedule.`status` = 0
                                    AND    client_loan_schedule.actived = 1");
        $penalty = 0;
        $i       = 0;
        while ($row_all = mysql_fetch_array($check_count)) {
            $remainig_root = $row_all[remaining_root];
            if ($i == 0) {
                if ($row_all[gadacilebuli]>0 && $row_all[gadacilebuli]<=$row_all[penalty_days]) {
                    $penalty1 = round(($remainig_root * ($row_all[penalty_percent]/100))*$row_all[gadacilebuli],2);
                }elseif ($row_all[gadacilebuli]>0 && $row_all[gadacilebuli]>$row_all[penalty_days]){
                    $penalty1 = round(round(($remainig_root * ($row_all[penalty_percent]/100))*$row_all[penalty_days],2)+round(($remainig_root * ($row_all[penalty_additional_percent]/100))*($row_all[gadacilebuli]-$row_all[penalty_days]),2),2);
                }
            }else{
                $penalty1 = round(($remainig_root * ($row_all[penalty_additional_percent]/100))*$row_all[gadacilebuli],2);
            }
            $i++;
            
            $penalty = $penalty+$penalty1;
        }
        
        $resultt = mysql_fetch_array(mysql_query("SELECT MAX(client_loan_schedule.id) AS max_sch_id,
                                                        (SELECT ROUND(clsh.remaining_root+clsh.root,2) FROM client_loan_schedule AS clsh WHERE clsh.id = MIN(client_loan_schedule.id)) AS `remaining_root`,
                                                        (SELECT DATEDIFF('$pay_datee', clsh.pay_date) FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `gadacilebuli`,
                                                        (SELECT ROUND(clsh.percent/30,2) FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `erti_dgis_procenti`,
                                                         SUM(client_loan_schedule.percent) AS percent,
                                                         client_loan_agreement.loan_beforehand_percent,
                                                        (SELECT clsh.remaining_root FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `check_remaining_root`,
                                                         MIN(client_loan_schedule.id) AS min_sch_id
                                                  FROM   client_loan_schedule 
                                                  JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                  WHERE  client_loan_agreement_id = $local_id
                                                  AND    DATE(client_loan_schedule.schedule_date)<='$pay_datee'
                                                  AND    client_loan_schedule.`status` = 0
                                                  AND    client_loan_schedule.actived = 1"));
        
        $rercent        = $resultt[percent];
        $remaining_root = $resultt[remaining_root];
        $sakomisio      = '0.00';
        $nasargeblebi   = '0.00';
        
        if ($resultt[check_remaining_root] > 0){
            $sakomisio    = round($remaining_root * ($resultt[loan_beforehand_percent]/100),2);
            $nasargeblebi = round($resultt[erti_dgis_procenti]*$resultt[gadacilebuli],2);
        }
        
        if (mysql_num_rows($check_count)>1) {
            $res1 = mysql_fetch_assoc(mysql_query(" SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                                    FROM    money_transactions_detail
                                                    JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                    JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                                    JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                    WHERE   client_loan_agreement.client_id = '$local_id'
                                                    AND     money_transactions_detail.`status` = 3
                                                    AND     money_transactions_detail.actived = 1"));
            
            $all_fee = round($remaining_root + $rercent + $penalty + $sakomisio + $nasargeblebi, 2);
            $data	= array('all_fee' => $all_fee, 'sakomisio' => $sakomisio, 'percent' => $rercent, 'pay_amount1' => $res1[pay_amount], 'remaining_root' => $remaining_root, 'penalty' => $penalty, 'nasargeblebebi' => $nasargeblebi);
        }else{
            $res = mysql_query("SELECT   client_loan_schedule.id,
                        				 client_loan_agreement.status AS st,
                        				 client_loan_schedule.pay_date,
                        				 client_loan_schedule.`status`,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN 0
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.percent,2)
                                         END AS percent,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2)
                                         END AS remaining_root,
                                         
                        				 DATEDIFF('$pay_datee', client_loan_schedule.pay_date) AS gadacilebuli,
                                         client_loan_schedule.remaining_root as check_remaining_root,
                                         client_loan_agreement.loan_beforehand_percent,
                        				 client_loan_agreement.penalty_days,
                        				 client_loan_agreement.penalty_percent,
                        				 client_loan_agreement.penalty_additional_percent
                                FROM     client_loan_schedule
                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date <= '$pay_datee'
                                ORDER BY client_loan_schedule.id DESC
                                LIMIT 1");
            
            $res1 = mysql_query("SELECT   client_loan_schedule.percent/30 AS `percent`
                                 FROM     client_loan_schedule
                                 JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                 WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date >= '$pay_datee'
                                 ORDER BY client_loan_schedule.id ASC
                                 LIMIT 1");
            
            $result  = mysql_fetch_assoc($res);
            $result1 = mysql_fetch_assoc($res1);
            
            $remainig_root = $result[remaining_root];
            
            $penalty = 0;
            if ($result[gadacilebuli]>0 && $result[gadacilebuli]<=$result[penalty_days] && $result[status] == 0) {
                $penalty = round(($remainig_root * ($result[penalty_percent]/100))*$result[gadacilebuli],2);
            }elseif ($result[gadacilebuli]>0 && $result[gadacilebuli]>$result[penalty_days] && $result[status] == 0){
                $penalty = round(round(($remainig_root * ($result[penalty_percent]/100))*$result[penalty_days],2)+round(($remainig_root * ($result[penalty_additional_percent]/100))*($result[gadacilebuli]-$result[penalty_days]),2),2);
            }
            
            $sakomisio      = '0.00';
            $nasargeblebi   = '0.00';
            
            if ($result[check_remaining_root] > 0){
                $sakomisio    = round($remaining_root * ($result[loan_beforehand_percent]/100),2);
                $nasargeblebi = round($result1[percent]*$result[gadacilebuli],2);
            }
            
        
            if ($result[st] == 1){
                $res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                                       FROM    money_transactions_detail
                                                       JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                       JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                                       JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                       WHERE   client_loan_agreement.client_id = '$local_id' 
                                                       AND     money_transactions_detail.`status` = 3
                                                       AND     money_transactions_detail.actived = 1"));
            
                $all_fee = round($remainig_root + $result['percent'] + $penalty + $sakomisio + $nasargeblebi, 2);
                
                $data	= array('all_fee' => $all_fee, 'sakomisio' => $sakomisio, 'percent' => $result['percent'], 'remaining_root' => $remainig_root, 'pay_amount1' => $res1[pay_amount], 'penalty' => $penalty, 'nasargeblebebi' => $nasargeblebi);
            
            }else{
                global  $error;
                $error = 'ხელშეკრულება არ არის გააქტიურებული';
            }
        }
        
	    break;
	case 'get_shedule':
		$id	               = $_REQUEST['id'];
		$type_id           = $_REQUEST['type_id'];
		$agr_id            = $_REQUEST['agr_id'];
		$status            = $_REQUEST['status'];
		$transaction_date  = $_REQUEST['transaction_date'];
		if ($type_id==1) {
    		  
    		if ($status == 1) {
    		    $filt = "AND client_loan_agreement.client_id = $id";
    		}elseif ($status == 2){
    		    $filt = "AND client_loan_agreement.id = $agr_id";
    		}else{
    		    $filt = "AND client_loan_agreement.client_id = $id";
    		}
    		
    		
    		$check_penalty = mysql_fetch_array(mysql_query("SELECT   client_loan_schedule.id AS schedule_id,
                                                    				 client_loan_schedule.schedule_date,
                                                    				 client.id AS client_id,
                                                    				 DATEDIFF('$transaction_date',client_loan_schedule.pay_date) AS datediff,
                                                    				 client_loan_agreement.penalty_days,
                                                    				 client_loan_agreement.penalty_percent,
                                                    				 client_loan_agreement.penalty_additional_percent,
                                                    				 client_loan_schedule.root + client_loan_schedule.remaining_root AS remaining_root
                                                                				 
                                                            FROM     client_loan_schedule
                                                            JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                            JOIN     client ON client.id = client_loan_agreement.client_id
                                                            WHERE    client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 0
                                                            AND      client_loan_schedule.schedule_date <= '$transaction_date'
                                                            AND      client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 0 
                                                                     $filt
    		                                                LIMIT 1"));
    		
    		$remaining_root = $check_penalty[remaining_root];
    		$penalty = 0;
    		if ($check_penalty[datediff]>0 && $check_penalty[datediff]<=$check_penalty[penalty_days]) {
    		    $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[datediff],2);
    		}elseif ($check_penalty[datediff]>0 && $check_penalty[datediff]>$check_penalty[penalty_days] && $check_penalty[penalty_additional_percent] > 0){
    		    $penalty = round((($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[penalty_days])+($remaining_root * ($check_penalty[penalty_additional_percent]/100))*($check_penalty[datediff]-$check_penalty[penalty_days]),2);
    		}elseif($check_penalty[datediff]>0 && $check_penalty[penalty_additional_percent] <= 0){
    		    $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[datediff],2);
    		}
    		
    		
		    mysql_query("UPDATE `client_loan_schedule`
        		            SET `penalty` = '$penalty'
        		         WHERE  `id`      = '$check_penalty[schedule_id]'");
    		
    		
    		$res = mysql_fetch_assoc(mysql_query("SELECT 	 client_loan_schedule.id,
    		                                                 client_loan_schedule.pay_amount,
                                            			     client_loan_schedule.root,
                                            				 client_loan_schedule.percent,
                                            				 client_loan_schedule.penalty,
    		                                                 client_loan_agreement.pledge_fee,
                                            			     client_loan_agreement.loan_currency_id,
    		                                                 client_loan_agreement.id AS agrement_id,
    		                                                 client_loan_agreement.loan_amount,
                                            				 client.id AS client_id
                                                   FROM 	`client_loan_schedule`
                                                   LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                   JOIN      client ON client.id = client_loan_agreement.client_id
                                                   WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1 
                                                   ORDER BY  pay_date ASC
                                                   LIMIT 1"));
    		
    		$res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                                   FROM    money_transactions_detail
                                                   JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                   JOIN    client ON client.id = money_transactions.client_id
                                                   JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                                   WHERE   client_loan_agreement.client_id = '$res[client_id]' 
                                                   AND     money_transactions_detail.`status` = 3
                                                   AND     money_transactions_detail.actived = 1"));
    		
    		$month_fee_trasaction  = $_REQUEST['month_fee_trasaction'];
    		$receivedd_currency_id = $_REQUEST['received_currency_id'];
    		$loan_cource_id        = $res[loan_currency_id];
    		$course                = $_REQUEST['course'];
    		
    		if ($receivedd_currency_id == $loan_cource_id) {
    		    $loan_pay_amount = $month_fee_trasaction;
    		}else{
    		    if ($receivedd_currency_id == 1 && $loan_cource_id == 2) {
    		        $loan_pay_amount = round($month_fee_trasaction/$course,2);
    		    }else{
    		        $loan_pay_amount = round($month_fee_trasaction*$course,2);
    		    }
    		}
    		$penalty = $res[penalty];
    
    		if ($type_id == 1 || $type_id == 0) {	
        		$data = array('status' => 1, 'id' => $res[id],'pay_amount' => $res[root] + $res[percent] + $penalty, 'root' => $res[root], 'percent' => $res[percent], 'penalty' => $penalty, 'client_data' => client($res[client_id]), 'agrement_data' => client_loan_number($res[agrement_id]), 'currenc' => currency($res[loan_currency_id]),'pay_amount1' => $res1[pay_amount], 'root1' => $res1[pay_root], 'percent1' => $res1[pay_percent], 'penalty1' => $res1[pay_penalty], 'loan_pay_amount' => $loan_pay_amount);
    		}
    		
		}elseif ($type_id == 2){
		    $receivedd_currency_id = $_REQUEST['received_currency_id'];
		    $res_pledge = mysql_fetch_array(mysql_query("SELECT CASE
                                                                   WHEN money_transactions.received_currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount*money_transactions.course,2)
                                                                   WHEN money_transactions.received_currency_id = 1 THEN money_transactions_detail.pay_amount
                                                                END AS fee_lari,
                                                                CASE
                                                                   WHEN money_transactions.received_currency_id = 2 THEN money_transactions_detail.pay_amount
                                                                   WHEN money_transactions.received_currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount/money_transactions.course,2)
                                                                END AS fee_dolari,
		                                                        money_transactions_detail.id,
		                                                        money_transactions.client_id,
		                                                        money_transactions.agreement_id
                                                         FROM   money_transactions
                                                         JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                         WHERE  (money_transactions.client_id = '$id' OR money_transactions.agreement_id = '$agr_id') 
                                                         AND    money_transactions_detail.`status` = 7 AND money_transactions.type_id = 2
                                                         AND    money_transactions_detail.payed_status = 1 AND money_transactions.actived = 1
		                                                 ORDER BY money_transactions.pay_datetime ASC
                                                         LIMIT 1"));
		    
		    $res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                    		       FROM    money_transactions_detail
                                    		       JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                    		       JOIN    client ON client.id = money_transactions.client_id
                                    		       JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    		       WHERE   client_loan_agreement.client_id = '$id'
                                    		       AND     money_transactions_detail.`status` = 9
                                    		       AND     money_transactions_detail.actived = 1"));
		    
		    $check_client = mysql_fetch_array(mysql_query("SELECT id, 
                                                                  client_id 
                                                           FROM   client_loan_agreement 
                                                           WHERE  client_id = '$id' OR id = '$agr_id'"));
		    
		    $data = array('status' => 2, 'fee_lari' => $res_pledge[fee_lari], 'fee_dolari' => $res_pledge[fee_dolari], 'trasnsaction_detail_id' => $res_pledge[id], 'client_data' => client($check_client[client_id]), 'agrement_data' => client_loan_number($check_client[id]), 'currency_data' => currency($receivedd_currency_id), 'pay_amount1' => $res1[pay_amount],);
		}elseif ($type_id == 3){
		    $check_client = mysql_fetch_array(mysql_query("SELECT id,
                                            		              client_id
                                            		       FROM   client_loan_agreement
                                            		       WHERE  client_id = '$id' OR id = '$agr_id'"));
		    
		    $data = array('status'=>3, 'client_data' => client($check_client[client_id]), 'agrement_data' => client_loan_number($check_client[id]));
		}
		
		break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Category Functions
* ******************************
*/

function save($tr_id, $client_amount, $transaction_date, $received_currency_id){
    
	$user_id	= $_SESSION['USERID'];
	
	mysql_query("UPDATE  `money_transactions`
                    SET  `datetime`             = NOW(),
            			 `user_id`              = '$user_id',
            			 `pay_datetime`         = '$transaction_date',
            			 `pay_amount`           = '$client_amount',
            			 `received_currency_id` = '$received_currency_id',
            			 `status`               = '1'
                  WHERE  `id`                   = '$tr_id'");
	
	mysql_query("UPDATE `money_transactions_detail`
                    SET `pay_datetime`         = '$transaction_date',
	                    `received_currency_id` = '$received_currency_id'
	             WHERE  `transaction_id`       = '$tr_id'");
}

function type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   transaction_type");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function currency($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   loan_currency");

    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function client($id){
    $req = mysql_query("SELECT    cl.id,
                                  CASE
                        			  WHEN cl.attachment_id = 0 AND cl.`name` != '' THEN concat(cl.`name`, ' ', cl.lastname)
                                      WHEN cl.attachment_id = 0 AND cl.`name` = '' THEN cl.ltd_name
                                      WHEN cl.attachment_id != 0 AND cl.`name` = '' THEN cl.ltd_name
                                      WHEN cl.attachment_id != 0 AND cl.`name` != '' THEN concat(cl.`name`, ' ', cl.lastname, '/დანართი N', client_loan_agreement.attachment_number)
                                  END AS `name`
                                  
                        				  
                        FROM      client AS cl
                        JOIN      client_loan_agreement ON cl.id = client_loan_agreement.client_id
                        WHERE     cl.actived=1 AND client_loan_agreement.`status`=1 AND client_loan_agreement.canceled_status=0
                        ORDER BY `name` ASC");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function client_loan_number($id){
    $req = mysql_query("SELECT  client_loan_agreement.id,
                                CASE
                        		   WHEN client.attachment_id = 0 AND client.id<(SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT('ს/ხ ',client.exel_agreement_id)
                                   WHEN client.attachment_id = 0 AND client.id>=(SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT('ს/ხ ',client_loan_agreement.id)
            					   WHEN client.attachment_id > 0 AND client.id<(SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN concat('ს/ხ ',(SELECT cl.exel_agreement_id FROM client AS cl WHERE cl.id = client.attachment_id), '/დანართი N', client_loan_agreement.attachment_number)
            					   WHEN client.attachment_id != 0 AND client.id>=(SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN concat('ს/ხ ',(SELECT client_loan_agreement.id FROM client_loan_agreement WHERE client_loan_agreement.client_id = client.attachment_id), '/დანართი N', client_loan_agreement.attachment_number)
                        	    END AS `name` 
                        			 
                         FROM   client_loan_agreement
                         JOIN   client ON client.id = client_loan_agreement.client_id
                         WHERE  client_loan_agreement.actived = 1 
                         AND    client_loan_agreement.`status` = 1
                         AND    client_loan_agreement.canceled_status = 0 
                         AND    client.actived = 1
                         ORDER BY `name` ASC");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function GetHolidays($id){
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT money_transactions.id,
                                    			   IFNULL(client_loan_agreement.`client_id`,money_transactions.client_id) AS client_id,
                                    			   money_transactions.pay_amount,
	                                               money_transactions.type_id,
	                                               money_transactions.course,
	                                               money_transactions.currency_id,
	                                               money_transactions.client_loan_schedule_id,
	                                               money_transactions.datetime,
	                                               money_transactions.pay_datetime,
	                                               money_transactions.received_currency_id,
	                                               money_transactions.month_fee_trasaction,
	                                               money_transactions.comment,
	                                               money_transactions.status,
	                                               money_transactions.actived
                                            FROM  `money_transactions`
                                            LEFT JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            LEFt JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE  money_transactions.id = $id" ));
    return $res;
}


function GetPage($res = ''){
    $today = date("Y-m-d H:i:s");
    
    if ($res[type_id] > 1) {
        $input_hidde = "display:none;";
    }else{
        $input_hidde = "";
    }
    
    if ($res[status] == 1) {
        $disable = 'disabled="disabled"';
    }else{
        $disable = "";
    }
    
    if ($res['id']=='') {
        $req = mysql_fetch_assoc(mysql_query("SELECT MAX(id)+1 AS `id` 
                                              FROM   money_transactions"));
        
        $hidde_id = $req[id];
        $date     = $today;
    }else{
        $date     = $res[datetime];
        $hidde_id = $res[id];
    }
    
    $data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table">
	            <label>თარიღი: '.$date.'</label>
	            <table>
	                <tr>
	                    <td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">დღევანდელი კურსი</label></td>
	                    <td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">ჩარიცხული თანხა</label></td>
	                    <td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">თანხა სესხის ვალუტაში</label></td>
    					<td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">ჩარიცხულის ვალუტა</label></td>
	                    <td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">ჩარიცხვის თარიღი</label></td>
	                </tr>
    				<tr>
	                    <td style="width: 180px;">
    						<input style="width: 150px;" id="course" class="label" type="text" value="'.$res[course].'" disabled="disabled">
    					</td>
    					<td style="width: 180px;">
    						<input style="width: 150px;" id="client_amount" class="label" type="text" value="'.$res[pay_amount].'" '.$disable.'>
    					</td>
    					<td style="width: 180px;">
    						<input style="width: 150px;" id="client_amount1_1" class="label" type="text" value="'.$res[month_fee_trasaction].'" disabled="disabled">
    					</td>
    					<td style="width: 180px;">
    						<select id="received_currency_id" calss="label" style="width: 155px;">'.currency($res[received_currency_id]).'</select>
    					</td>
    				    <td style="width: 180px;">
    						<input style="width: 200px;" id="transaction_date" class="label" type="text" value="'.$res[pay_datetime].'">
    					</td>
    				</tr>
    				<tr style="height:20px"></tr>
    				<tr>
	                    <td colspan="5" style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">კომენტარი</label></td>
	                </tr>
    				<tr>
	                    <td colspan="5" style="width: 180px;">
    						<textarea class="idle" id="transaction_comment" style="resize: vertical;width: 100%; height: 40px;">'.$res['comment'].'</textarea>
    					</td>
    				</tr>
    				<tr style="height:20px"></tr>
    			</table>
    			<fieldset id="table_person_fieldset">
                        <legend>ჩარიცხულის განაწილება</legend>
                        <div id="button_area">
                        	<button id="add_button_dettail">დამატება</button>
    						<button id="pledge_distribution">დაზღვევის დარიცხვა</button>
    						<button id="delete_detail">განაწილების გაუქმება</button>
                        </div>
                        <table class="display" id="table_transaction_detail" style="width: 100%;">
                            <thead>
                                <tr id="datatable_header">
                                    <th>ID</th>
    						        <th style="width: 20%;">თარიღი</th>
            	                    <th style="width: 16%;">თანხა</th>
                                    <th style="width: 16%;">სესხის ვალუტა</th>
                                    <th style="width: 16%;">მიმდინარე კურსი</th>
    						        <th style="width: 16%;">ჩარიცხულის ვალუტა</th>
    						        <th style="width: 16%;">ტიპი</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr class="search_header">
                                    <th class="colum_hidden">
                                	   <input type="text" name="search_id" value="ფილტრი" class="search_init" />
                                    </th>
                                    <th>
                                    	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                    </th>
            	                    <th>
                                    	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                    </th>
    						        <th>
                                    	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                    </th>
    						        <th>
                                    	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                    </th>
    						        <th>
                                    	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                    </th>
    						        <th>
                                    	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                    </th>
                                </tr>
                            </thead>
    				    </table>
                    </fieldset>
    		</table>
			<!-- ID -->
			<input type="hidden" id="tr_id" value="' . $res['id'] . '" />
			<input type="hidden" id="hidde_id" value="" />
			<input type="hidden" id="hidde_cl_id1" value="'.$res[client_id].'" />
			<input type="hidden" id="hidde_transaction_id" value="'.$hidde_id.'" />
			<input type="hidden" id="hidde_actived" value="'.$res[actived].'" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
