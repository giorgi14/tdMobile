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
		
    case 'delete_detail':
    
        $tr_id	= $_REQUEST['tr_id'];
        
        $res = mysql_fetch_array(mysql_query("SELECT money_transactions.client_loan_schedule_id
                                              FROM   money_transactions 
                                              WHERE  id = '$tr_id'"));
        
        mysql_query("UPDATE money_transactions_detail
                        SET payed_status           = 1,
                            balance_transaction_id = 0
                     WHERE  balance_transaction_id = '$tr_id' AND status IN(7)");
        
        mysql_query("UPDATE money_transactions_detail
                        SET actived  = 1
                     WHERE  balance_transaction_id = '$tr_id' AND status IN(3,9)");
        
        mysql_query("UPDATE  client_loan_schedule
                        SET `status` = 0
                     WHERE   id      = '$res[client_loan_schedule_id]'");
        
        mysql_query("UPDATE  money_transactions
                        SET `client_loan_schedule_id` = null,
                            `status`                  = 0,
                            `type_id`                 = 0
                      WHERE  id                       = '$tr_id'");
        
        mysql_query("DELETE FROM money_transactions_detail
                     WHERE  money_transactions_detail.transaction_id = '$tr_id'");
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
                    						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' დანართი ', client_loan_agreement.attachment_number, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT(client.ltd_name, 'ს/ხ', client_loan_agreement.oris_code, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                    					   END AS new_name,
                                    	   money_transactions.pay_amount,
                                           IFNULL(loan_currency.name,ln_currency.name),
		                                   money_transactions.course,
		                                   $val
                                           IF(money_transactions.`status` = 0,'დაუდასტურებელი',IF(money_transactions.`reg_ltd`=1,'გადაფორმება შპს-ზე', 'დადასტურებული')),
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
		                         ORDER BY  money_transactions.pay_datetime DESC, money_transactions.datetime DESC");

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
	                            GROUP BY  money_transactions.id
                                ORDER BY  money_transactions.pay_datetime DESC, money_transactions.datetime DESC");
	
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
		
		
		$check_gansawileba = mysql_query("  SELECT COUNT(*) AS count
                                		    FROM  `money_transactions_detail`
                                		    WHERE  transaction_id = '$tr_id'
                                		    AND    money_transactions_detail.actived = 1
                                            UNION ALL
                                            SELECT COUNT(*) AS count
                                		    FROM  `money_transactions_detail`
                                		    WHERE  parent_tr_id = '$tr_id'
                                		    AND    money_transactions_detail.actived = 1");
		
		if (mysql_num_rows($check_gansawileba)>0) {
		    save($tr_id, $client_amount, $transaction_date, $received_currency_id);
		}else{
		    global $error;
		    $error = 'ჩარიცხული თანხა არაა გადანაწილებული!';
		}
		
        
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
	    $local_id      = $_REQUEST['client_id'];
        $pay_datee     = $_REQUEST['transaction_date'];
        $exception_agr = $_REQUEST['exception_agr'];
        $other_penalty = $_REQUEST['other_penalty'];
        
        $check_count = mysql_query("SELECT client_loan_schedule.id,
                                           client_loan_schedule.penalty,
                                           client_loan_schedule.penalty_stoped,
                                           client_loan_schedule.other_amount,
                                           DATEDIFF('$pay_datee', client_loan_schedule.pay_date) AS gadacilebuli,
                                           client_loan_agreement.penalty_days,
                            			   client_loan_agreement.penalty_percent,
                            			   client_loan_agreement.penalty_additional_percent,
                                           client_loan_schedule.pay_date,
                                           ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2) AS remaining_root
                                    FROM   client_loan_schedule 
                                    JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE  client_loan_agreement.client_id = '$local_id'
                                    AND    DATE(client_loan_schedule.schedule_date)<='$pay_datee'
                                    AND    client_loan_schedule.`status` = 0
                                    AND    client_loan_schedule.actived = 1");
        
        $resultt = mysql_fetch_array(mysql_query("SELECT MAX(client_loan_schedule.id) AS max_sch_id,
                                                        (SELECT ROUND(clsh.remaining_root+clsh.root,2) FROM client_loan_schedule AS clsh WHERE clsh.id = MIN(client_loan_schedule.id)) AS `remaining_root`,
                                                        (SELECT DATEDIFF('$pay_datee', clsh.pay_date) FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `gadacilebuli`,
                                                        (SELECT ROUND(clsh.percent/30,2) FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `erti_dgis_procenti`,
                                                        SUM(client_loan_schedule.percent) AS percent,
                                                        SUM(client_loan_schedule.root) AS schedule_root,
                                                        client_loan_agreement.loan_beforehand_percent,
                                                        (SELECT clsh.remaining_root FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `check_remaining_root`,
                                                        MIN(client_loan_schedule.id) AS min_sch_id
                                                FROM   client_loan_schedule
                                                JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                WHERE  client_loan_agreement.client_id = $local_id
                                                AND    DATE(client_loan_schedule.schedule_date)<='$pay_datee'
                                                AND    client_loan_schedule.`status` = 0
                                                AND    client_loan_schedule.actived = 1"));
        
        $res1 = mysql_query("SELECT   client_loan_schedule.percent/30 AS `percent`
                             FROM     client_loan_schedule
                             JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                             WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date >= '$pay_datee'
                             ORDER BY client_loan_schedule.id ASC
                             LIMIT 1");
        
        $result1 = mysql_fetch_assoc($res1);
        $penalty = 0;
        $i       = 0;
        $other_amount = 0;
        
        $remainig_root = $resultt[remaining_root];
        $gadacilebuli_day_count = $gadacilebuli_day_count;
        while ($row_all = mysql_fetch_array($check_count)) {
            
            
            $gadacilebuli_day_count = $row_all[gadacilebuli];
            
            $check_holliday_day = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS count
                                                                 FROM   holidays
                                                                 WHERE  actived = 1
                                                                 AND    DATE(date)>='$row_all[pay_date]'
                                                                 AND    DATE(date)<= '$pay_datee'"));
            
            $gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];
            
            if ($row_all[penalty_stoped] == 1) {
                $penalty1 = $row_all[penalty];
            }else{
                if ($other_penalty == 1) {
                    $penalty1 = round(($remainig_root * ($row_all[penalty_additional_percent]/100))*$gadacilebuli_day_count,2);
                }else{
                    if ($i == 0) {
                        if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$row_all[penalty_days]) {
                            $penalty1 = round(($remainig_root * ($row_all[penalty_percent]/100))*$gadacilebuli_day_count,2);
                        }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$row_all[penalty_days]){
                            $penalty1 = round(round(($remainig_root * ($row_all[penalty_percent]/100))*$row_all[penalty_days],2)+round(($remainig_root * ($row_all[penalty_additional_percent]/100))*($gadacilebuli_day_count-$row_all[penalty_days]),2),2);
                        }
                    }else{
                        $penalty1 = round(($remainig_root * ($row_all[penalty_additional_percent]/100))*$gadacilebuli_day_count,2);
                    }
                }
            }
            $i++;
            $other_amount = $other_amount + $row_all[other_amount];
            $penalty = $penalty+$penalty1;
        }
        
        
        
        $rercent        = $resultt[percent];
        
        $remaining_root = $resultt[remaining_root];
        
        $sakomisio      = '0.00';
        $nasargeblebi   = '0.00';
        
        if ($resultt[check_remaining_root] > 0){
            $sakomisio    = round($remaining_root * ($resultt[loan_beforehand_percent]/100),2);
            if ($resultt[gadacilebuli]>=0) {
                $nasargeblebi = round($result1[percent]*$resultt[gadacilebuli],2);
            }
            
        }
        
        if (mysql_num_rows($check_count)>1) {
            $res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(ROUND(SUM(CASE
                                        										WHEN money_transactions_detail.currency_id = client_loan_agreement.loan_currency_id THEN money_transactions_detail.pay_amount
                                        										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 1 THEN money_transactions_detail.pay_amount/money_transactions_detail.course
                                        										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 2 THEN money_transactions_detail.pay_amount*money_transactions_detail.course
                                        									END),2),0) AS pay_amount
                                                    FROM    money_transactions_detail
                                                    JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                    JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_idWHERE   client_loan_agreement.client_id = '$local_id'
                                                    AND     money_transactions_detail.`status` = 3
                                                    AND     money_transactions_detail.actived = 1"));
            if ($exception_agr==1) {
                $remaining_root = $resultt[schedule_root];
                $sakomisio     = 0;
                $nasargeblebi  = 0;
            }
            $all_fee = round($remaining_root + $rercent + $penalty + $sakomisio + $nasargeblebi + $other_amount, 2);
            $data	= array('all_fee' => $all_fee, 'sakomisio' => $sakomisio, 'percent' => $rercent, 'pay_amount1' => $res1[pay_amount], 'remaining_root' => $remaining_root, 'penalty' => $penalty, 'nasargeblebebi' => $nasargeblebi, 'other_amount' => $other_amount);
        }else{
            $res = mysql_query("SELECT   client_loan_schedule.id,
                        				 client_loan_agreement.status AS st,
                        				 client_loan_schedule.pay_date,
                                         client_loan_schedule.penalty,
                                         client_loan_schedule.penalty_stoped,
                                         client_loan_schedule.other_amount,
                        				 client_loan_schedule.`status`,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN 0
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.root,2)
                                         END AS schedule_root,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN 0
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.percent,2)
                                         END AS percent,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2)
                                         END AS remaining_root,
                                         0 AS nasargeblebi_dgeebi,
                        				 DATEDIFF('$pay_datee', client_loan_schedule.pay_date) AS gadacilebuli,
                                         client_loan_schedule.remaining_root as check_remaining_root,
                                         client_loan_agreement.loan_beforehand_percent,
                        				 client_loan_agreement.penalty_days,
                        				 client_loan_agreement.penalty_percent,
                                         client_loan_agreement.loan_currency_id,
                        				 client_loan_agreement.penalty_additional_percent
                                FROM     client_loan_schedule
                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date <= '$pay_datee'
                                ORDER BY client_loan_schedule.id DESC
                                LIMIT 1");
            
            if (mysql_num_rows($res)==0) {
                $res = mysql_query("SELECT   client_loan_schedule.id,
                            				 client_loan_agreement.status AS st,
                            				 client_loan_schedule.pay_date,
                                             client_loan_schedule.penalty,
                                             client_loan_schedule.penalty_stoped,
                                             client_loan_schedule.other_amount,
                            				 client_loan_schedule.`status`,
                                             CASE
                                                 WHEN client_loan_schedule.`status` = 1 THEN 0
                            					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.percent,2)
                                             END AS nasargeblebi_dgeebi,
                                             0 AS percent,
                                             CASE
                                                 WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root
                            					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2)
                                             END AS remaining_root,
                                             CASE
                                                 WHEN client_loan_schedule.`status` = 1 THEN 0
                            					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.root,2)
                                             END AS schedule_root,
                            				 DATEDIFF('$pay_datee', client_loan_schedule.pay_date) AS gadacilebuli,
                                             client_loan_schedule.remaining_root as check_remaining_root,
                                             client_loan_agreement.loan_beforehand_percent,
                            				 client_loan_agreement.penalty_days,
                            				 client_loan_agreement.penalty_percent,
                                             client_loan_agreement.loan_currency_id,
                            				 client_loan_agreement.penalty_additional_percent
                                    FROM     client_loan_schedule
                                    JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date > '$pay_datee'
                                    ORDER BY client_loan_schedule.id ASC
                                    LIMIT 1");
            }
            
            $res1 = mysql_query("SELECT   client_loan_schedule.percent/30 AS `percent`
                                 FROM     client_loan_schedule
                                 JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                 WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date >= '$pay_datee'
                                 ORDER BY client_loan_schedule.id ASC
                                 LIMIT 1");
            
            $result  = mysql_fetch_assoc($res);
            $result1 = mysql_fetch_assoc($res1);
            
            $remainig_root = $result[remaining_root];
            $gadacilebuli_day_count = $result[gadacilebuli];
            $check_holliday_day = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS count 
                                                                 FROM   holidays
                                                                 WHERE  actived = 1 
                                                                 AND    DATE(date)>='$result[pay_date]' AND DATE(date) <= '$pay_datee'"));
            
            $gadacilebuli_day_count = $gadacilebuli_day_count-$check_holliday_day[count];
            $other_amount = $result[other_amount];
            $penalty = 0;
            
            if ($result[penalty_stoped] == 1) {
                $penalty = $result[penalty];
            }else{
                if ($other_penalty == 1) {
                    $penalty = round(($remainig_root * ($result[penalty_additional_percent]/100))*$gadacilebuli_day_count,2);
                }else{
                    if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$result[penalty_days] && $result[status] == 0) {
                        $penalty = round(($remainig_root * ($result[penalty_percent]/100))*$gadacilebuli_day_count,2);
                    }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$result[penalty_days] && $result[status] == 0){
                        $penalty = round(round(($remainig_root * ($result[penalty_percent]/100))*$result[penalty_days],2)+round(($remainig_root * ($result[penalty_additional_percent]/100))*($gadacilebuli_day_count-$result[penalty_days]),2),2);
                    }
                }
            }
            $sakomisio      = '0.00';
            $nasargeblebi   = $result[nasargeblebi_dgeebi];
            
            if ($result[check_remaining_root] > 0){
                $sakomisio    = round($result[remaining_root] * ($result[loan_beforehand_percent]/100),2);
                if ($result[gadacilebuli]>=0) {
                    $nasargeblebi = round($result1[percent]*$result[gadacilebuli],2);
                }
                
            }
         
            if ($result[st] == 1){
                $res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(ROUND(SUM(CASE
                                            										WHEN money_transactions_detail.currency_id = client_loan_agreement.loan_currency_id THEN money_transactions_detail.pay_amount
                                            										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 1 THEN money_transactions_detail.pay_amount/money_transactions_detail.course
                                            										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 2 THEN money_transactions_detail.pay_amount*money_transactions_detail.course
                                            									END),2),0) AS pay_amount
                                                       FROM    money_transactions_detail
                                                       JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                       JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                                       WHERE   client_loan_agreement.client_id = '$local_id' 
                                                       AND     money_transactions_detail.`status` = 3
                                                       AND     money_transactions_detail.actived = 1"));
            
                if ($exception_agr==1) {
                    $remainig_root = $result[schedule_root];
                    $sakomisio     = 0;
                    $nasargeblebi  = 0;
                }
                $all_fee = round($remainig_root + $result['percent'] + $penalty + $sakomisio + $nasargeblebi + $other_amount, 2);
                
                $data	= array('all_fee' => $all_fee, 'sakomisio' => $sakomisio, 'percent' => $result['percent'], 'remaining_root' => $remainig_root, 'pay_amount1' => $res1[pay_amount], 'penalty' => $penalty, 'nasargeblebebi' => $nasargeblebi, 'other_amount' => $other_amount);
            
            }else{
                global  $error;
                $error = 'ხელშეკრულება არ არის გააქტიურებული';
            }
        }
        
	    break;
	case 'get_shedule':
		$id	                  = $_REQUEST['id'];
		$type_id              = $_REQUEST['type_id'];
		$agr_id               = $_REQUEST['agr_id'];
		$status               = $_REQUEST['status'];
		$transaction_date     = $_REQUEST['transaction_date'];
		$received_currency_id = $_REQUEST['received_currency_id'];
		$check_loan_penalty   = $_REQUEST['check_loan_penalty'];
		$exception_agr        = $_REQUEST['exception_agr'];
		
		if ($type_id==1) {
    		if ($status == 1) {
    		    $filt = "AND client_loan_agreement.client_id = $id";
    		}elseif ($status == 2){
    		    $filt = "AND client_loan_agreement.id = $agr_id";
    		}else{
    		    $filt = "AND client_loan_agreement.client_id = $id";
    		}
    		
    		
    		$check_penalty = mysql_fetch_array(mysql_query("SELECT   client_loan_schedule.id AS schedule_id,
    		                                                         client_loan_schedule.penalty,
    		                                                         client_loan_schedule.penalty_stoped,
    		                                                         client_loan_schedule.other_amount,
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
    		
    		$gadacilebuli_day_count = $check_penalty[datediff];
    		
    		
    		$check_holliday_day = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS count
                                                    		     FROM   holidays
                                                    		     WHERE  actived = 1
                                                    		     AND    DATE(date)>='$check_penalty[schedule_date]'
                                                    		     AND    DATE(date)<= '$transaction_date'"));
    		
    		$gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];
    		
    		if ($check_penalty[penalty_stoped]==1) {
    		    $penalty=$check_penalty[penalty];
    		}else{
        		if ($check_loan_penalty == 1) {
        		    $RemainigRoot = mysql_fetch_array(mysql_query("SELECT   client_loan_schedule.remaining_root+client_loan_schedule.root AS remaining_root
                                                                   FROM     client_loan_schedule
                                                                   JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                                   WHERE    client_loan_agreement.client_id = '$id' 
                                                                   AND 		client_loan_schedule.schedule_date < '$transaction_date' 
                                                                   AND      client_loan_schedule.`status` = 1 
                                                                   AND      client_loan_schedule.penalty_check=0
                                                                   ORDER BY client_loan_schedule.id DESC
                                                                   LIMIT 1"));
        		    $penalty = round(($RemainigRoot[remaining_root] * ($check_penalty[penalty_additional_percent]/100))*$gadacilebuli_day_count,2);
        		}else{
        		    if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$check_penalty[penalty_days]) {
        		        $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$gadacilebuli_day_count,2);
        		    }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$check_penalty[penalty_days] && $check_penalty[penalty_additional_percent] > 0){
        		        $penalty = round((($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[penalty_days])+($remaining_root * ($check_penalty[penalty_additional_percent]/100))*($gadacilebuli_day_count-$check_penalty[penalty_days]),2);
        		    }elseif($gadacilebuli_day_count>0 && $check_penalty[penalty_additional_percent] <= 0){
        		        $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$gadacilebuli_day_count,2);
        		    }  
        		}
    		}
    		$res_sch_id = mysql_fetch_assoc(mysql_query("SELECT    client_loan_schedule.id
                                                		    
                                            		     FROM 	  `client_loan_schedule`
                                            		     LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            		     JOIN      client ON client.id = client_loan_agreement.client_id
                                            		     WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1
                                            		     ORDER BY  pay_date ASC
                                            		     LIMIT 1"));
    		
    		mysql_query("UPDATE `client_loan_schedule`
        		            SET `penalty` = '$penalty'
        		         WHERE  `id`      = '$res_sch_id[id]'");
    		
    		
    		$res = mysql_fetch_assoc(mysql_query("SELECT 	 client_loan_schedule.id,
    		                                                 client_loan_schedule.pay_amount,
                                            			     client_loan_schedule.root,
                                            				 client_loan_schedule.percent,
    		                                                 client_loan_schedule.other_amount,
                                            				 client_loan_schedule.penalty,
    		                                                 client_loan_agreement.pledge_fee,
    		                                                 client_loan_agreement.agreement_id AS agree_id,
                                            			     client_loan_agreement.loan_currency_id,
    		                                                 client_loan_agreement.id AS agrement_id,
    		                                                 client_loan_agreement.loan_amount,
    		                                                 client_loan_schedule.schedule_date,
                                            				 client.id AS client_id
                                                   FROM 	`client_loan_schedule`
                                                   LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                   JOIN      client ON client.id = client_loan_agreement.client_id
                                                   WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1 
                                                   ORDER BY  pay_date ASC
                                                   LIMIT 1"));
    		
    		$res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(ROUND(SUM(
                                        									CASE
                                        										WHEN money_transactions_detail.currency_id = client_loan_agreement.loan_currency_id THEN money_transactions_detail.pay_amount
                                        										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 1 THEN money_transactions_detail.pay_amount/money_transactions_detail.course
                                        										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 2 THEN money_transactions_detail.pay_amount*money_transactions_detail.course
                                        									END),2),0) AS pay_amount
                                                   FROM    money_transactions_detail
                                                   JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                   JOIN    client ON client.id = money_transactions.client_id
                                                   JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                                   WHERE   client_loan_agreement.client_id = '$res[client_id]' 
                                                   AND     money_transactions_detail.`status` = 3
                                                   AND     money_transactions_detail.actived = 1"));
    		
    		$month_fee_trasaction  = $_REQUEST['month_fee_trasaction'];
    		$receivedd_currency_id = $_REQUEST['received_currency_id'];
    		$loan_cource_id        = $res['loan_currency_id'];
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
    		$other_amount = $res[other_amount];
    
    		$pay_position = mysql_num_rows(mysql_query("SELECT 	  client_loan_schedule.id
                                                        FROM     `client_loan_schedule`
                                                        LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                        JOIN      client ON client.id = client_loan_agreement.client_id
                                                        WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.id > $res[id] AND client_loan_schedule.`status` != 1 
                                                        "));
    		$info_message = '';
    		if ($pay_position == 0) {
    		    $info_message = 'ბოლო გადახდა!';
    		}
    		
    		if ($type_id == 1 || $type_id == 0) {	
        		$data = array('status' => 1, 'schedule_date'=>$res[schedule_date], 'id' => $res[id],'pay_amount' => $res[root] + $res[percent] + $penalty+$other_amount, 'root' => $res[root], 'percent' => $res[percent], 'penalty' => $penalty, 'client_data' => client($res[client_id]), 'client_attachment_data' => client_attachment($res[agree_id], $res['client_id']), 'agrement_data' => client_loan_number($res[agrement_id]), 'currenc' => currency($res[loan_currency_id]),'pay_amount1' => $res1[pay_amount], 'root1' => $res1[pay_root], 'percent1' => $res1[pay_percent], 'penalty1' => $res1[pay_penalty], 'loan_pay_amount' => $loan_pay_amount, 'info_message' => $info_message, 'other_amount' => $other_amount);
    		}
    	}elseif ($type_id == 2){
		    $receivedd_currency_id = $_REQUEST['received_currency_id'];
		    $res_pledge = mysql_fetch_assoc(mysql_query("SELECT SUM(CASE
                                                                   WHEN money_transactions_detail.received_currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2)
                                                                   WHEN money_transactions_detail.received_currency_id = 1 THEN money_transactions_detail.pay_amount
                                                                END) AS fee_lari,
                                                                SUM(CASE
                                                                   WHEN money_transactions_detail.received_currency_id = 2 THEN money_transactions_detail.pay_amount
                                                                   WHEN money_transactions_detail.received_currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2)
                                                                END) AS fee_dolari,
		                                                        money_transactions_detail.id,
		                                                        money_transactions.client_id,
		                                                        money_transactions.agreement_id
                                                         FROM   money_transactions
                                                         JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                         WHERE  (money_transactions.client_id = '$id' OR money_transactions.agreement_id = '$agr_id') 
                                                         AND    money_transactions_detail.`status` = 7 AND money_transactions.type_id = 2
                                                         AND    money_transactions_detail.actived = 1 AND money_transactions.actived = 1
		                                                 ORDER BY money_transactions.pay_datetime ASC
                                                         LIMIT 1"));
		    
		    $res_pledge1 = mysql_fetch_assoc(mysql_query("SELECT SUM(CASE
                                                    		        WHEN money_transactions_detail.received_currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2)
                                                    		        WHEN money_transactions_detail.received_currency_id = 1 THEN money_transactions_detail.pay_amount
                                                    		     END) AS fee_lari,
                                                		        SUM(CASE
                                                    		        WHEN money_transactions_detail.received_currency_id = 2 THEN money_transactions_detail.pay_amount
                                                    		        WHEN money_transactions_detail.received_currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2)
                                                		        END) AS fee_dolari,
                                                		        money_transactions_detail.id,
                                                		        money_transactions.client_id,
                                                		        money_transactions.agreement_id
                                        		        FROM   money_transactions
                                        		        JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                        		        WHERE  (money_transactions.client_id = '$id' OR money_transactions.agreement_id = '$agr_id')
                                        		        AND    money_transactions_detail.`status` = 8 AND money_transactions.type_id = 2
                                        		        AND    money_transactions_detail.actived = 1 AND money_transactions.actived = 1
                                        		        ORDER BY money_transactions.pay_datetime ASC
                                        		        LIMIT 1"));
		    $pledge_lari = $res_pledge[fee_lari] - $res_pledge1[fee_lari];
		    $pledge_gel = $res_pledge[fee_dolari] - $res_pledge1[fee_dolari];
		    $res1 = mysql_fetch_assoc(mysql_query("SELECT  CASE
                                            					WHEN money_transactions.currency_id = 2 THEN ROUND(IFNULL(SUM(money_transactions_detail.pay_amount),0),2)
                                            					WHEN money_transactions.currency_id = 1 THEN ROUND(IFNULL(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),0),2)
                                            				END  AS pay_amount_usd,
                                                            CASE
                                            					WHEN money_transactions.currency_id = 2 THEN ROUND(IFNULL(SUM(money_transactions_detail.pay_amount*money_transactions_detail.course),0),2)
                                            					WHEN money_transactions.currency_id = 1 THEN ROUND(IFNULL(SUM(money_transactions_detail.pay_amount),0),2)
                                            				END  AS pay_amount_gel
                                    		       FROM    money_transactions_detail
                                    		       JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                    		       JOIN    client ON client.id = money_transactions.client_id
                                    		       JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    		       WHERE  (client_loan_agreement.client_id = '$id' OR money_transactions.agreement_id = '$agr_id')
                                    		       AND     money_transactions_detail.`status` = 9
                                    		       AND     money_transactions_detail.actived = 1"));
		    
		    
		    $check_client = mysql_fetch_array(mysql_query(" SELECT client_loan_agreement.id, 
                                                    			   client_loan_agreement.client_id, 
                                                    			   client_loan_agreement.agreement_id
                                                            FROM   client_loan_agreement 
                                                            JOIN   client ON client.id = client_loan_agreement.client_id
                                                            WHERE  client.id = '$id' OR client_loan_agreement.id = '$agr_id';"));
		    
		    $data = array('status' => 2, 'fee_lari' => $pledge_lari, 'fee_dolari' => $pledge_gel, 'trasnsaction_detail_id' => $res_pledge[id], 'client_data' => client($check_client[client_id]), 'agrement_data' => client_loan_number($check_client[id]), 'client_attachment_data' => client_attachment($check_client[agreement_id], $check_client[client_id]), 'currency_data' => currency($receivedd_currency_id), 'pay_amount1' => $res1[pay_amount_gel], 'pay_amount2' => $res1[pay_amount_usd]);
		}elseif ($type_id == 3){
		    
		    $res_other = mysql_fetch_assoc(mysql_query("SELECT ROUND(SUM(money_transactions_detail.pay_amount),2) AS pay_amount
                                        		        FROM   money_transactions_detail
                                        		        JOIN   money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                        		        WHERE  money_transactions.client_id = '$id'
                                        		        AND    money_transactions.actived = 1
                                        		        AND    money_transactions_detail.actived = 1
                                        		        AND    money_transactions_detail.`status` = 10"));
		    
		    $res_other1 = mysql_fetch_assoc(mysql_query("SELECT ROUND(SUM(money_transactions_detail.pay_amount),2) AS pay_amount
                                        		         FROM   money_transactions_detail
                                        		         JOIN   money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                        		         WHERE  money_transactions.client_id = '$id'
                                        		         AND    money_transactions.actived = 1
                                        		         AND    money_transactions_detail.actived = 1
                                        		         AND    money_transactions_detail.`status` = 11"));
		    
		    
		    $other_pay = round($res_other[pay_amount] - $res_other1[pay_amount],2);
		    
		    if ($other_pay<=1) {
		        $other_pay = 0;
		    }
		    
		    $check_client = mysql_fetch_array(mysql_query("SELECT client_loan_agreement.id, 
                                                    			   client_loan_agreement.client_id, 
                                                    			   client_loan_agreement.agreement_id
                                                            FROM   client_loan_agreement 
                                                            JOIN   client ON client.id = client_loan_agreement.client_id
                                                            WHERE  client.id = '$id' OR client_loan_agreement.id = '$agr_id';"));
		    
		    $data = array('status'=>3, 'client_data' => client($check_client[client_id]), 'client_attachment_data' => client_attachment($check_client[agreement_id], $check_client[client_id]), 'agrement_data' => client_loan_number($check_client[id]), 'other_pay' => $other_pay);
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
                        			  WHEN cl.attachment_id = 0 AND cl.`name` != '' THEN concat(cl.`name`, ' ', cl.lastname, '/', client_loan_agreement.oris_code, '/', client_car.registration_number)
                                      WHEN cl.attachment_id = 0 AND cl.`name` = '' THEN concat(cl.ltd_name, '/', client_loan_agreement.oris_code, '/', client_car.registration_number)
                                      WHEN cl.attachment_id != 0 AND cl.`name` = '' THEN concat(cl.ltd_name, '/', client_loan_agreement.oris_code, '/', client_car.registration_number)
                                      WHEN cl.attachment_id != 0 AND cl.`name` != '' THEN concat(cl.`name`, ' ', cl.lastname, '/დანართი N', client_loan_agreement.attachment_number, '/', client_loan_agreement.oris_code, '/', client_car.registration_number)
                                  END AS `name`
                        FROM      client AS cl
                        JOIN      client_loan_agreement ON cl.id = client_loan_agreement.client_id
                        JOIN      client_car ON cl.id = client_car.client_id
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

function client_attachment($id, $cl_id){
    $req = mysql_query("SELECT client.id,
                               CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number != '', CONCAT(' დანართი ', client_loan_agreement.attachment_number), '')) AS `name`  
                        FROM   client_loan_agreement
                        JOIN   client ON client.id = client_loan_agreement.client_id
                        WHERE  client_loan_agreement.agreement_id = '$id'
                        AND    client_loan_agreement.client_id != '$cl_id'
                        AND    client_loan_agreement.actived = 1 
                        AND    client.actived = 1 
                        AND    client_loan_agreement.canceled_status = 0");

    $data .= '<option value="0" selected="selected">----</option>';
    
    while( $res = mysql_fetch_assoc($req)){
        $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
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
                                    			   money_transactions.client_id AS client_id,
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
                                            LEFT JOIN   client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
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
    						<button id="add_other">სხვა ხარჯის დარიცხვა</button>
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
			<input type="hidden" id="hidde_statusss" value="'.$res[status].'" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
