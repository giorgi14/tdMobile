<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
	case 'get_edit_page':
		$id		= $_REQUEST['id'];
	    $page		= GetPage($id);
        $data		= array('page'	=> $page);

		break;
		
	case 'get_cource':
	    $transaction_date = $_REQUEST['transaction_date'];
	    $cource = mysql_fetch_array(mysql_query("SELECT cource FROM cur_cource WHERE DATE(datetime) = DATE('$transaction_date')"));
	
	    $data	= array('cource' => $cource[cource]);
	    break;
	    
	case 'get_canceled-loan':
	    $client_id        = $_REQUEST['client_id'];
	    $transaction_date = $_REQUEST['transaction_date'];
	     
	    $res = mysql_query("SELECT   client_loan_schedule.id,
                        	         client_loan_agreement.status as st,
                        	         client_loan_schedule.pay_date,
                        	         client_loan_schedule.`status`,
                        	         ROUND(client_loan_schedule.percent,2) AS percent,
                        	         ROUND((client_loan_schedule.root + client_loan_schedule.remaining_root),2) AS remaining_root,
                        	         ROUND(((client_loan_schedule.root + client_loan_schedule.remaining_root)*client_loan_agreement.loan_beforehand_percent)/100, 2) AS sakomisio,
                        	         client_loan_agreement.loan_amount,
                        	         DATEDIFF('$transaction_date', client_loan_schedule.pay_date) AS gadacilebuli,
                        	         client_loan_agreement.penalty_days,
                        	         client_loan_agreement.penalty_percent,
                        	         client_loan_agreement.penalty_additional_percent
                	        FROM     client_loan_schedule
                	        JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                	        WHERE    client_loan_agreement.client_id = '$client_id' AND client_loan_schedule.`status` = 0
                	        ORDER BY client_loan_schedule.id ASC
                	        LIMIT 1");
	
	    $check = mysql_num_rows($res);
	
	    if ($check == 0) {
	        $res = mysql_query("SELECT   client_loan_schedule.id,
                        	             client_loan_agreement.status as st,
                        	             client_loan_schedule.pay_date,
                        	             client_loan_schedule.`status`,
                        	             ROUND(((client_loan_schedule.root + client_loan_schedule.remaining_root)*client_loan_agreement.loan_beforehand_percent)/100,2) AS sakomisio,
                        	             0 AS percent,
                        	             0 AS penalty,
                        	             ROUND(client_loan_schedule.remaining_root,2) AS remaining_root
                	            FROM     client_loan_schedule
                	            JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                	            WHERE    client_loan_agreement.client_id = '$client_id' AND client_loan_schedule.`status` = 1 AND client_loan_schedule.schedule_date <= '$transaction_date'
                	            ORDER BY client_loan_schedule.id DESC
                	            LIMIT 1");
	    }
	
	    $result = mysql_fetch_assoc($res);
	
	    if ($result[remaining_root]==0) {
	        $remainig_root = $result[loan_amount];
	    }else{
	        $remainig_root = $result[remaining_root];
	    }
	
	    if ($result[gadacilebuli]>0 && $result[gadacilebuli]<=$result[penalty_days]) {
	        $penalty = round(($remainig_root * ($result[penalty_percent]/100))*$result[gadacilebuli],2);
	    }elseif ($result[gadacilebuli]>0 && $result[gadacilebuli]>$result[penalty_days] && $result[penalty_additional_percent] > 0){
	        $penalty = round((($remainig_root * ($result[gadacilebuli]/100))*$result[penalty_days])+($remainig_root * ($result[penalty_additional_percent]/100))*($result[gadacilebuli]-$result[penalty_days]),2);
	    }elseif($result[gadacilebuli]>0 && $result[penalty_additional_percent] <= 0){
	        $penalty = round(($remainig_root * ($result[gadacilebuli]/100))*$result[gadacilebuli],2);
	    }
	
	    if($penalty==0){
	        $penalty = $result[penalty];
	    }
	
	    $req = mysql_fetch_assoc(mysql_query("SELECT client_loan_schedule.id,
                                	                 ROUND(DATEDIFF('$transaction_date', '$result[pay_date]')*(client_loan_schedule.percent/DAY(LAST_DAY(client_loan_schedule.pay_date))),2) AS nasargeblebebi
                                	          FROM   client_loan_schedule
                                	          JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                	          WHERE  client_loan_agreement.client_id = $hidde_idd AND client_loan_schedule.`id` = '$result[id]+1'"));
	
	
	    $res1 = mysql_fetch_assoc(mysql_query(" SELECT SUM(pay_amount) AS pay_amount
                                    	        FROM   money_transactions
                                    	        WHERE  money_transactions.client_loan_schedule_id = $res[id] AND money_transactions.status in(3) AND actived = 1"));
	
	    $all_fee = $req[nasargeblebebi]+$result[sakomisio] + $result[percent] + $penalty + $result[remaining_root];
	
	    $data	= array('all_fee' => $all_fee, 'sakomisio' => $result[sakomisio], 'percent' => $result[percent], 'remaining_root' => $result[remaining_root], 'penalty' => $penalty, 'nasargeblebebi' => $req[nasargeblebebi]);
	    break;
	     
	case 'get_shedule':
	    $id	               = $_REQUEST['id'];
	    $type_id           = $_REQUEST['type_id'];
	    $agr_id            = $_REQUEST['agr_id'];
	    $status            = $_REQUEST['status'];
	    $transaction_date  = $_REQUEST['transaction_date'];
	
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
                                            	        AND      client_loan_schedule.schedule_date < '$transaction_date' AND DATEDIFF('$transaction_date',client_loan_schedule.pay_date)>=1
                                            	        AND      client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 0
                                            	        $filt
                                            	        LIMIT 1"));
	
	        $remaining_root = $check_penalty[remaining_root];
	
	        if ($check_penalty[datediff]>0 && $check_penalty[datediff]<=$check_penalty[penalty_days]) {
	            $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[datediff],2);
	        }elseif ($check_penalty[datediff]>0 && $check_penalty[datediff]>$check_penalty[penalty_days] && $check_penalty[penalty_additional_percent] > 0){
	            $penalty = round((($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[penalty_days])+($remaining_root * ($check_penalty[penalty_additional_percent]/100))*($check_penalty[datediff]-$check_penalty[penalty_days]),2);
	        }elseif($check_penalty[datediff]>0 && $check_penalty[penalty_additional_percent] <= 0){
	            $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[datediff],2);
	        }
	
	        if ($penalty != ''){
	            mysql_query("UPDATE `client_loan_schedule`
	                SET `penalty` = '$penalty'
	                WHERE  `id`      = '$check_penalty[schedule_id]'");
	        }
	
	        $res = mysql_fetch_assoc(mysql_query(" SELECT 	  client_loan_schedule.id,
                                            	              client_loan_schedule.pay_amount,
                                            	              client_loan_schedule.root,
                                            	              client_loan_schedule.percent,
                                            	              client_loan_schedule.penalty,
                                            	              client_loan_agreement.pledge_fee,
                                            	              client_loan_agreement.loan_currency_id,
                                            	              client_loan_agreement.id AS agrement_id,
                                            	              client_loan_agreement.loan_amount,
                                            	              client.id AS client_id,
                                            	             (SELECT  car_insurance_info.ins_payy
                                            	              FROM   `car_insurance_info`
                                            	              WHERE   car_insurance_info.client_id = client.id
                                            	              AND     car_insurance_info.actived = 1
                                            	              AND     DATE(car_insurance_info.car_insurance_end) = '$transaction_date') AS insurance_fee
                                    	            FROM 	 `client_loan_schedule`
                                    	            LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    	            JOIN      client ON client.id = client_loan_agreement.client_id
                                    	            WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1
                                    	            ORDER BY  pay_date ASC
                                    	            LIMIT 1"));
	
	        $res1 = mysql_fetch_assoc(mysql_query(" SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                    	            FROM    money_transactions_detail
                                    	            JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                    	            JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                    	            JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
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
	            $data = array('status' => 1, 'id' => $res[id],'pay_amount' => $res[pay_amount] + $penalty, 'root' => $res[root], 'percent' => $res[percent], 'penalty' => $penalty, 'client_data' => client($res[client_id]), 'agrement_data' => client_loan_number($res[agrement_id]), 'currenc' => currency($res[loan_currency_id]),'pay_amount1' => $res1[pay_amount], 'root1' => $res1[pay_root], 'percent1' => $res1[pay_percent], 'penalty1' => $res1[pay_penalty], 'loan_pay_amount' => $loan_pay_amount);
	        }elseif ($type_id == 2){
	            $data = array('status' => 2, 'id' => $res[id],'insurance_fee' => $res[insurance_fee], 'client_data' => client($res[client_id]), 'agrement_data' => client_loan_number($res[agrement_id]), 'loan_pay_amount' => $loan_pay_amount,'pay_amount1' => $res1[pay_amount]);
	        }elseif ($type_id == 3){
	            $data = array('status' => 3, 'id' => $res[id],'pledge_fee' => $res[pledge_fee], 'loan_pay_amount' => $loan_pay_amount,'pay_amount1' => $res1[pay_amount]);
	        }
	
	        break;
	case 'get_list' :
        $count	    = $_REQUEST['count'];
		$hidden	    = $_REQUEST['hidden'];
		$filt_month	= $_REQUEST['filt_month'];
		
		$filt_day	= $_REQUEST['filt_day'];
		$today      = date("Y-m");
		$c_day      = date("d");
		$AND        = '';
		
		if ($filt_day > 0) {
		    $AND = "AND DAY(client_loan_agreement.datetime) ='$filt_day'";
		}
		
		$rResult = mysql_query("SELECT 	MAX(client_loan_schedule.id),
                        				DATE_FORMAT(MAX(client_loan_schedule.schedule_date),'%d/%m/%Y') AS daricxvis_tarigi,
                        				CASE
                        					 WHEN client.id >= (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        					 WHEN client.id < (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client.exel_agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        				END AS `name`,
                        				client_loan_agreement.oris_code,
                        				CASE
    										 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
    										 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
    										 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
    										 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
                        			    END AS agreement_id,
                                        loan_currency.`name`,
                                        (SELECT IF(client_loan_agreement.loan_currency_id = 1,'0.00',sch.percent) 
                                         FROM   client_loan_schedule AS sch
                                         JOIN   client_loan_agreement ON client_loan_agreement.id = sch.client_loan_agreement_id 
                                         WHERE  sch.id = MAX(client_loan_schedule.id)) AS percent_usd,
                                        (SELECT IF(client_loan_agreement.loan_currency_id = 1, sch.percent, ROUND(sch.percent*(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(MAX(client_loan_schedule.schedule_date)) ),2)) 
                                         FROM   client_loan_schedule AS sch
                                         JOIN   client_loan_agreement ON client_loan_agreement.id = sch.client_loan_agreement_id 
                                         WHERE  sch.id = MAX(client_loan_schedule.id)) AS percent_gel,
                                        (SELECT IFNULL(SUM(money_transactions_detail.pay_amount),0.00)
                                         FROM   money_transactions_detail 
                                         JOIN   money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                         WHERE  money_transactions_detail.actived = 1 AND money_transactions_detail.`status` = 3
                                         AND    money_transactions.agreement_id = client_loan_agreement.id) AS extra_fee,
                                        (SELECT IF(shd.`status` = 0,'გადაუხდელი','გადახდილი') FROM client_loan_schedule AS shd WHERE shd.actived = 1 AND shd.id = MAX(client_loan_schedule.id)) AS `status`,
		                                 client.id AS client_id,
		                                 client_loan_agreement.loan_currency_id
                                FROM   	 client_loan_schedule
                                JOIN   	 client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN   	 client ON client.id = client_loan_agreement.client_id
                                JOIN   	 client_car ON client_car.client_id = client.id
                                JOIN     loan_currency ON loan_currency.id = client_loan_agreement.loan_currency_id
                                WHERE  	 client_loan_schedule.actived = 1 AND client_loan_schedule.pay_date <= CURDATE() 
		                        AND      MONTH(client_loan_schedule.schedule_date) = '$filt_month'
		                        AND      YEAR(client_loan_schedule.schedule_date) = YEAR(CURDATE()) $AND
                                GROUP BY client_loan_schedule.client_loan_agreement_id
		                        ORDER BY client_loan_schedule.schedule_date DESC, client_loan_agreement.oris_code ASC");

		$data = array("aaData" => array());

		while ( $aRow = mysql_fetch_array( $rResult ) ){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ ){
			    if ($i == 10) {
			        $row[] = '<button style="width: 100px;" class="show_letter" loan_currency_id="'.$aRow['loan_currency_id'].'" client_id="'.$aRow['client_id'].'">ბარათი</button>';
			    }else{
				    $row[] = $aRow[$i];
			    }
			}
			$data['aaData'][] = $row;
		}
        break;
    case 'save_transaction':
        $id 		          = $_REQUEST['id'];
        $tr_id 		          = $_REQUEST['tr_id'];
        $type_id              = $_REQUEST['type_id'];
        $currency_id          = $_REQUEST['currency_id'];
        $received_currency_id = $_REQUEST['received_currency_id'];
        $course               = $_REQUEST['course'];
        $transaction_date     = $_REQUEST['transaction_date'];
    
        $month_fee            = $_REQUEST['month_fee'];
        $month_fee2           = $_REQUEST['month_fee2'];
        $root                 = $_REQUEST['root'];
        $percent              = $_REQUEST['percent'];
        $penalti_fee          = $_REQUEST['penalti_fee'];
        $surplus              = $_REQUEST['surplus'];
        
        $client_id            = $_REQUEST['client_id'];
        $client_loan_number   = $_REQUEST['client_loan_number'];
        
        
        
    
        $month_fee_trasaction = $_REQUEST['month_fee_trasaction'];
        $extra_fee            = $_REQUEST['extra_fee'];
    
        $user_id	          = $_SESSION['USERID'];
    
        $pay_amount = $month_fee + $month_fee2;
        mysql_query("INSERT INTO `money_transactions`
                                (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                          VALUES
                                (NOW(), '$user_id', '$id', '$client_loan_number', '$client_id', '$transaction_date', '$pay_amount', '$extra_fee', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '$type_id', '1', '1')");
         
        $tr_id = mysql_insert_id();
    
        Add($id, $tr_id, $transaction_date, $pay_amount, $course, $currency_id, $currency_id, $root,  $percent, $penalti_fee, $surplus, $type_id);
    
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
function Add($id, $hidde_transaction_id, $transaction_date, $month_fee, $course, $currency_id, $received_currency_id, $root,  $percent, $penalti_fee, $surplus, $type_id){

    $user_id	 = $_SESSION['USERID'];
    $client_id   = $_REQUEST['client_id'];

    $month_fee1  = $_REQUEST['month_fee1'];
    $payable_Fee = $_REQUEST['payable_Fee'];
    $yield       = $_REQUEST['yield'];

   

    
    $all_pay = $month_fee;
    $all_fee = $month_fee1;

    if ($all_fee == $all_pay){
         
        if ($penalti_fee>0){
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$penalti_fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '2', 1)");
        }
         
        mysql_query(" UPDATE  money_transactions_detail
                      JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                      JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                      JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                      SET     money_transactions_detail.actived = 0,
                              money_transactions_detail.balance_transaction_id = '$hidde_transaction_id'
                      WHERE   client_loan_agreement.client_id = '$client_id'
                      AND     money_transactions_detail.`status` = 3
                      AND     money_transactions_detail.actived = 1");
         
        if ($root>0 || $percent>0) {
             
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '', '$course', '$currency_id', '$received_currency_id', '$root', '$percent', '$type_id', 1, 1)");
             
            mysql_query("UPDATE  `client_loan_schedule`
                            SET  `status` = '1'
                          WHERE  `id`     = '$id'");
        }
         
        if ($surplus>0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 3, 1)");
        }
         
        if ($payable_Fee>0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$payable_Fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 5, 1)");
        }
         
        if ($yield>0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$yield', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 6, 1)");
        }
         
    }elseif ($all_fee < $all_pay){
        if ($penalti_fee>0){
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$penalti_fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 2, 1)");
        }
         
        if($root>0 || $percent>0){
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '', '$course', '$currency_id', '$received_currency_id', '$root', '$percent', '$type_id', 1, 1)");
            	
            mysql_query("UPDATE  `client_loan_schedule`
                            SET  `status` = '1'
                         WHERE   `id`     = '$id'");
             
        }
         
        mysql_query("UPDATE  money_transactions_detail
                     JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                     JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                     JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                     SET     money_transactions_detail.actived = 0,
                             money_transactions_detail.balance_transaction_id = '$hidde_transaction_id'
                     WHERE   client_loan_agreement.client_id = '$client_id'
                     AND     money_transactions_detail.`status` = 3
                     AND     money_transactions_detail.actived = 1");
        
        if ($surplus>0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 3, 1)");
        }
         
        if ($payable_Fee>0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$payable_Fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 5, 1)");
        }

        if ($yield>0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$yield', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 6, 1)");
        }
         
    }else{
        mysql_query("INSERT INTO `money_transactions_detail`
                                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                          VALUES
                                (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$month_fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 3, 1)");
    }
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

function GetPage($id){
    $today = date("Y-m-d H:i:s");
    
    $hidde_out_car = 'display:none;';
    
    
    
    
    $res1= mysql_fetch_assoc(mysql_query("SELECT      client_loan_agreement.id,
                                                      client_loan_agreement.client_id,
                                                      client_loan_agreement.loan_currency_id,
                                                      client_loan_schedule.id,
                                					  client_loan_schedule.pay_amount,
                                					  client_loan_schedule.root,
                                					  client_loan_schedule.percent,
                                                      client_loan_schedule.schedule_date,
                                					  client_loan_schedule.penalty AS penalty
                                           FROM  	 `client_loan_schedule`
                                           LEFT JOIN  money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                                           LEFT JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                           WHERE      client_loan_schedule.id = '$id'
                                           LIMIT 1"));
     
    
    
    $res2 = mysql_fetch_assoc(mysql_query(" SELECT  CASE
                										WHEN money_transactions_detail.currency_id = client_loan_agreement.loan_currency_id THEN money_transactions_detail.pay_amount
                										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 1 THEN money_transactions_detail.pay_amount/money_transactions_detail.course
                										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 2 THEN money_transactions_detail.pay_amount*money_transactions_detail.course
                									END),2),0) AS pay_amount
                                            FROM    money_transactions_detail
                                            JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                            JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE   client_loan_agreement.client_id = '$res1[client_id]' 
                                            AND     money_transactions_detail.`status` = 3
                                            AND     money_transactions_detail.actived  = 1"));
   
    $res3 = mysql_fetch_assoc(mysql_query(" SELECT  money_transactions_detail.pay_amount AS pay_amount
                                            FROM    money_transactions_detail
                                            JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                            JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE   client_loan_agreement.client_id = '$res[client_id]'
                                            AND     money_transactions_detail.`status` = 3
                                            AND     money_transactions_detail.balance_transaction_id = '$res[tr_id]'
                                            AND     money_transactions_detail.actived = 0"));
                                            
    $res4 = mysql_fetch_assoc(mysql_query("SELECT cource FROM cur_cource WHERE DATE(datetime) = '$res1[schedule_date]' AND actived = 1"));
    
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table">
	            <table>
	                <tr>
	                    <td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">დღევანდელი კურსი</label></td>
	                    <td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">სესხის ვალუტა</label></td>
	                    <td style="width: 180px;"><label calss="label" style="padding-top: 5px;" for="name">დარიცხვის თარიღი</label></td>
	                </tr>
    				<tr>
	                    <td style="width: 180px;">
    						<input style="width: 150px;" id="course" class="label" type="text" value="'.$res4[cource].'" disabled="disabled">
    					</td>
    					<td style="width: 180px;">
    						<select id="currency_id"  calss="label" style="width: 155px;" disabled="disabled">'.currency($res1[loan_currency_id]).'</select>
    					</td>
    				    <td style="width: 180px;">
    						<input style="width: 200px;" id="transaction_date" class="label" type="text" value="'.$res1[schedule_date].'" disabled="disabled">
    					</td>
    				</tr>
	                <tr>
    	                <td style="width: 200px;"><label calss="label" style="padding-top: 5px;" for="name">ტიპი</label></td>
    					<td style="width: 280px;"><label calss="label" style="padding-top: 5px;" for="date">მსესხებელი</label></td>
    					<td style="width: 120px;"><label calss="label" style="padding-top: 5px;" for="date">სესხის ნომერი</label></td>
    				</tr>
    				<tr>
    	                <td style="width: 200px;">
    						<select id="type_id"  calss="label" style="width: 175px;" disabled="disabled">'.type(1).'</select>
    					</td>
    					<td style="width: 280px;">
    						<select id="client_id" calss="label" style="width: 260px;" disabled="disabled">'.client($res1[client_id]).'</select>
    					</td>
    					<td style="width: 120px;">
    						<select id="client_loan_number" calss="label" style="width: 175px;" disabled="disabled">'.client_loan_number($res1[client_id]).'</select>
    					</td>
    				</tr>
    				<tr style="display:none">
    	                <td style="width: 200px;"><label calss="label" style="padding-top: 5px;" for="name">სესხის ვალუტა</label></td>
    					<td style="width: 280px;"><label calss="label" style="padding-top: 5px;" for="date">მანქანის გაყვანა</label></td>
    					<td style="width: 120px;"><label calss="label" style="padding-top: 5px;" for="date"></label></td>
    				</tr>
    				<tr style="display:none">
    	                <td style="width: 200px;">
    						<select id="currency_id"  calss="label" style="width: 155px;" disabled="disabled">'.currency($res1[loan_currency_id]).'</select>
    					</td>
    					<td style="width: 280px;">
    						<input class="idle" style="width: 15px;" id="car_out" value="1" type="checkbox">
    					</td>
    					<td style="width: 120px;">
    					</td>
    				</tr>
    			</table>
    			<table>
    				<tr style="height:40px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="padding-top: 5px;" class="label" for="date">ჩარიცხული თანხა:</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee_trasaction" class="label" type="text" value="0" disabled="disabled">
    					</td>
    					<td style="width: 100px;"></td>
    					<td style="width: 80px;">
    					</td>
    				    <td style="width: 135px;"></td>
    					<td style="width: 100px;">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="" class="label" for="date">ჩარიცხული თანხა სესხის ვალუტაში:</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee" class="label" type="text" value="0" disabled="disabled">
    					</td>
    					<td style="width: 100px;"><label style="padding-top: 5px; margin-left: 10px;" class="label" for="name">სულ შესატანი თანხა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="month_fee1" class="label" type="text" value="'.$res1['pay_amount'].'" disabled="disabled">
    					</td>
    				    <td style="width: 135px;"><label style="padding-top: 5px;" class="label" for="name">არსებული ბალანსი:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee2" class="label" type="text" value="'.$res2['pay_amount'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px; "><label style="padding-top: 5px;" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 100px; ">
    						<input style="width: 70px; float:left;" id="root" onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" class="label_label" type="text" value="'.$res['pay_root'].'"><span style="float: right; display: inline; margin-top: 4px;"><button id="delete_root" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 100px;"><label style="padding-top: 5px; margin-left: 10px;" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="root1" class="label_label" type="text" value="'.$res1['root'].'" disabled="disabled">
    					</td>
    				    <td style="width: 135px;"><label style="padding-top: 5px;" class="label" for="name">ჩარიცხვამდე ბალანსი:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="post_balance" class="label" type="text" value="'.$res3['pay_amount'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr >
    					<td style="width: 105px; "><label style="padding-top: 5px;" class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 100px; ">
    						<input style="width: 70px; float:left;" id="percent" class="label_label"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" type="text" value="'.$res['pay_percent'].'"><span style="float: right; display: inline; margin-top: 4px;"><button id="delete_percent" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 100px;"><label style="padding-top: 5px; margin-left: 10px;" class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;"  class="label_label" id="percent1" type="text" value="'.$res1['percent'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr >
    					<td style="width: 105px; "><label style="padding-top: 5px;" class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 100px; ">
    						<input class="label_label" style="width: 70px; float:left;" id="penalti_fee"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" type="text" value="'.$res['pay_penalty'].'"><span style="float: right; display: inline; margin-top: 4px;"><button id="delete_penalty" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 100px;"><label style="padding-top: 5px; margin-left: 10px;" class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="penalti_fee1" class="label_label" type="text" value="'.$res1['penalty'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr class="car_out_class" style="height:10px; '.$hidde_out_car.'"></tr>
    				<tr class="car_out_class" style="'.$hidde_out_car.'">
    					<td style="width: 105px; padding-top: 5px;"><label class="label_label" for="date">საკომისიო:</label></td>
    					<td style="width: 100px;">
    						<input class="label_label" style="width: 70px; float:left;" id="payable_Fee" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_payable_Fee" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px; margin-left: 10px;" class="label_label" for="date">საკომისიო:</label></td>
    					<td style="width: 100px;"><input style="width: 80px;" id="payable_Fee1" class="label_label" type="text" value="'.$res1['penalty'].'" disabled="disabled"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    				<tr class="car_out_class" style="height:10px; '.$hidde_out_car.'"></tr>
    				<tr class="car_out_class" style="'.$hidde_out_car.'">
    					<td style="width: 120px;"><label class="label_label" for="date">დღიური სარგებელი:</label></td>
    					<td style="width: 100px;"><input class="label_label" style="width: 70px; float:left;" id="yield" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_yield" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span></td>
    					<td style="width: 120px;"><label style="margin-left: 10px;" class="label_label" for="date">დღიური სარგებელი:</label></td>
    					<td style="width: 100px;"><input style="width: 80px;" id="yield1" class="label_label" type="text" value="'.$res1['penalty'].'" disabled="disabled"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px; padding-top: 5px;"><label class="label_label" for="date">მეტობა</label></td>
    					<td style="width: 100px;">
    						<input class="label_label" style="width: 70px; float:left;" id="surplus" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_surplus" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 120px;"><label class="label_label" for="date">ზედმეტი თანხა:</label></td>
    					<td style="width: 100px;"><input class="label_label" style="width: 80px; " id="extra_fee" type="text" value="'.$res2['pay_amount'].'" disabled="disabled"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
				</table>
			</table>
			<!-- ID -->
			<input type="hidden" id="cl_sched_id" value="' . $id . '" />
			<input type="hidden" id="tr_id" value="" />
			    
			<input type="hidden" id="hidde_status" value="' . $res['status'] . '" />
			    
		    <input type="hidden" id="hidde_root" value="0" />
	        <input type="hidden" id="hidde_percent" value="0" />
            <input type="hidden" id="hidde_penalty" value="0" />
		    <input type="hidden" id="hidde_payable_Fee" value="0" />
		    <input type="hidden" id="hidde_payable_Fee" value="0" />
            <input type="hidden" id="hidde_yield" value="0" />
			<input type="hidden" id="hidde_surplus" value="0" />
                
		</fieldset>
    </div>
    ';
	return $data;
}

?>
