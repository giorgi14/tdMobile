<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
	case 'get_add_page':
	    
	    $page = GetPage();
    	$data = array('page'	=> $page);
	    
		break;
	case 'get_edit_page':
		$id	  = $_REQUEST['id'];
		
	    $page = GetPage(GetHolidays($id));
        $data = array('page'	=> $page);
		
		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		$tab	        = $_REQUEST['tab'];
		$transaction_id	= $_REQUEST['transaction_id'];
		
		$rResult = mysql_query("SELECT      money_transactions_detail.id,
                            				DATE_FORMAT(money_transactions_detail.datetime,'%d/%m/%Y'),
                            				IF(money_transactions_detail.`status` = 1,money_transactions_detail.pay_root + money_transactions_detail.pay_percent,money_transactions_detail.pay_amount),
                            				loan_currency.`name`,
                            				money_transactions_detail.course,
		                                    fact_cource.`name`,
                            				money_transaction_status.`name`
                                FROM       `money_transactions_detail`
                                LEFT JOIN   loan_currency ON loan_currency.id = money_transactions_detail.currency_id
		                        LEFT JOIN   loan_currency AS fact_cource ON fact_cource.id = money_transactions_detail.received_currency_id
                                JOIN        money_transaction_status ON money_transaction_status.id = money_transactions_detail.`status`
                                WHERE       money_transactions_detail.transaction_id = '$transaction_id'");

		$data = array("aaData"	=> array());

		while ($aRow = mysql_fetch_array($rResult)){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++){
				$row[] = $aRow[$i];
			}
			
			$data['aaData'][] = $row;
		}

		break;
	case 'save_transaction':
		$id 		          = $_REQUEST['id'];
		$tr_id 		          = $_REQUEST['tr_id'];
		
		$month_fee            = $_REQUEST['month_fee'];
		$root                 = $_REQUEST['root'];
		$percent              = $_REQUEST['percent'];
		$penalti_fee          = $_REQUEST['penalti_fee'];
		$surplus              = $_REQUEST['surplus'];
		$diff                 = $_REQUEST['diff'];
		$type_id              = $_REQUEST['type_id'];
		$currency_id          = $_REQUEST['currency_id'];
		$received_currency_id = $_REQUEST['received_currency_id'];
		$course               = $_REQUEST['course'];
		$transaction_date     = $_REQUEST['transaction_date'];
		
		$month_fee_trasaction = $_REQUEST['month_fee_trasaction'];
		$extra_fee            = $_REQUEST['extra_fee'];
		
		$hidde_id             = $_REQUEST['hidde_id'];
		$hidde_transaction_id = $_REQUEST['hidde_transaction_id'];
		$hidde_status         = $_REQUEST['hidde_status'];
		$user_id	          = $_SESSION['USERID'];
		
		if ($id == '') {
	        if ($tr_id == '') {
	            mysql_query("INSERT INTO `money_transactions` 
                                        (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`) 
                                  VALUES 
                                        (NOW(), '$user_id', '$hidde_id', '$transaction_date', '$month_fee', '$extra_fee', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '$type_id', '0', '1')");
	            
	            $tr_id = mysql_insert_id();
	        }else{
	            $tr_id = $hidde_transaction_id;
	        }
	        if ($type_id == 2) {
	            Add1($tr_id, $hidde_id, $transaction_date, $month_fee, $course, $currency_id, $received_currency_id, $type_id);
	        }elseif ($type_id == 1){
	           Add($tr_id, $hidde_id, $transaction_date, $month_fee, $course, $currency_id, $received_currency_id, $root,  $percent, $penalti_fee, $surplus, $diff, $type_id);
	        }
	        $data = array('tr_id' => $tr_id);
        }else{
            update($hidde_status, $id, $transaction_date, $month_fee,  $root,  $percent, $penalti_fee, $surplus);
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

function Add($hidde_transaction_id, $hidde_id, $transaction_date, $month_fee, $course, $currency_id, $received_currency_id, $root,  $percent, $penalti_fee, $surplus, $diff, $type_id){
    
	$user_id	= $_SESSION['USERID'];
	$client_id  = $_REQUEST['client_id'];
	$res = mysql_fetch_assoc(mysql_query("SELECT  SUM(money_transactions_detail.pay_amount) AS pay_amount
                                          FROM    money_transactions_detail
                                          JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                          JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                          JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                          WHERE   client_loan_agreement.client_id = '$client_id' 
                                          AND     money_transactions_detail.`status` = 3
                                          AND     money_transactions_detail.actived = 1"));
	
	$res1 = mysql_fetch_assoc(mysql_query("SELECT  client_loan_schedule.pay_amount,
                                            	   client_loan_schedule.penalty AS penalty,
	                                               client_loan_schedule.id
                                    	   FROM   `client_loan_schedule`
	                                       JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    	   WHERE   client_loan_schedule.id = $hidde_id AND client_loan_schedule.actived = 1"));
	$sxvaoba = $month_fee - $res1[pay_amount]; 
	if ($sxvaoba>0 && $sxvaoba<1) {
	    $month_fee = $res1[pay_amount];
	}
	$all_pay = ROUND($month_fee + $res[pay_amount],2);
	$all_fee = ROUND($res1[pay_amount] + $res1[penalty],2);
	
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
	    
	    if ($root>0 && $percent>0) {
	        
	        mysql_query("INSERT INTO `money_transactions_detail` 
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES 
                                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '', '$course', '$currency_id', '$received_currency_id', '$root', '$percent', '$type_id', 1, 1)");
	        
	        mysql_query("UPDATE  `client_loan_schedule`
	                        SET  `status` = '1'
	                      WHERE  `id`     = '$res1[id]'");
	    }
	    
	}elseif ($all_fee < $all_pay){
	    $delta = round($all_pay - $all_fee,2);
	    
	    if ($penalti_fee>0){
	        mysql_query("INSERT INTO `money_transactions_detail`
                    	            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	      VALUES
                    	            (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$penalti_fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 2, 1)");
	    }
	    
	    if($root>0 && $percent>0){
    	    mysql_query("INSERT INTO `money_transactions_detail`
                        	        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	  VALUES
                        	        (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '', '$course', '$currency_id', '$received_currency_id', '$root', '$percent', '$type_id', 1, 1)");
    	    
    	    mysql_query("UPDATE  `client_loan_schedule`
            	            SET  `status` = '1'
            	         WHERE   `id`     = '$res1[id]'");
	    
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
	    if ($delta>1) {
	       mysql_query("INSERT INTO `money_transactions_detail`
                        	       (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	 VALUES
                        	       (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$delta', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 3, 1)");
	    }
	    
	}else{
	    mysql_query("INSERT INTO `money_transactions_detail`
                    	        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	  VALUES
                    	        (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$month_fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', 3, 1)");
	}
}

function Add1($tr_id, $hidde_id, $transaction_date, $month_fee, $course, $currency_id, $received_currency_id, $type_id){

    $user_id	= $_SESSION['USERID'];
    $client_id  = $_REQUEST['client_id'];
    
    
    
    $res1 = mysql_fetch_assoc(mysql_query(" SELECT  SUM(money_transactions_detail.pay_amount) AS pay_amount
                                            FROM    money_transactions_detail
                                            JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                            JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE   client_loan_agreement.client_id = '$client_id'
                                            AND     money_transactions_detail.`status` = 3
                                            AND     money_transactions_detail.actived = 1"));
    
    $res = mysql_fetch_array(mysql_query("SELECT  car_insurance_info.ins_payy
                                          FROM   `car_insurance_info`
                                          WHERE   car_insurance_info.client_id = '$client_id'
                                          AND     car_insurance_info.actived = 1
                                          AND     DATE(car_insurance_info.car_insurance_end) = CURDATE()"));
    
    $sxvaoba = $month_fee - $res[ins_payy];
    if ($sxvaoba>0 && $sxvaoba<1) {
        $month_fee = $res1[pay_amount];
    }
    
    $month_fee = $month_fee + $res1[pay_amount];
    $sxvaoba1 = $month_fee - $res[ins_payy];
    
    if ($month_fee >= $res[ins_payy]) {
        mysql_query("INSERT INTO `money_transactions_detail`
                                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                          VALUES
                                (NOW(), '$user_id', '$tr_id', '$transaction_date', '$res[ins_payy]', '$course', '$currency_id', '$received_currency_id', '', '', '2', '4', 1)");
        
        mysql_query("UPDATE `car_insurance_info`
                        SET `status` = '1'
                     WHERE  `car_insurance_info.client_id` = '$client_id'
                     AND     DATE(car_insurance_info.car_insurance_end) = CURDATE()");
        
        if ($sxvaoba1>1) {
            
            mysql_query("UPDATE  money_transactions_detail
                         JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                         JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                         JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            SET  money_transactions_detail.actived = 0
                         WHERE   client_loan_agreement.client_id = '$client_id'
                         AND     money_transactions_detail.`status` = 3
                         AND     money_transactions_detail.actived = 1");
            
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$tr_id', '$transaction_date', '$sxvaoba1', '$course', '$currency_id', '$received_currency_id', '', '', '2', 3, 1)");
        }
    }else{
        
        mysql_query("UPDATE  money_transactions_detail
                     JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                     JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                     JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                        SET  money_transactions_detail.actived = 0
                     WHERE   client_loan_agreement.client_id = '$client_id'
                     AND     money_transactions_detail.`status` = 3
                     AND     money_transactions_detail.actived = 1");
        
        mysql_query("INSERT INTO `money_transactions_detail`
                                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                          VALUES
                                (NOW(), '$user_id', '$tr_id', '$transaction_date', '$month_fee', '$course', '$currency_id', '$received_currency_id', '', '', '2', 3, 1)");
    }
    
    
}

function update($hidde_status, $id, $transaction_date, $month_fee, $root,  $percent, $penalti_fee, $surplus){
    
    $user_id	= $_SESSION['USERID'];
    
    if ($hidde_status == 1) {
        $req = mysql_query("UPDATE `money_transactions_detail`
                               SET `datetime`     = NOW(),
                                   `user_id`      = '$user_id',
                                   `pay_datetime` = '$transaction_date',
                                   `pay_root`     = '$root',
                                   `pay_percent`  = '$percent'
                            WHERE  `id`           = '$id'");
        
    }elseif ($hidde_status == 2){
        $req = mysql_query("UPDATE `money_transactions_detail`
                               SET `datetime`     = NOW(),
                                   `user_id`      = '$user_id',
                                   `pay_datetime` = '$transaction_date',
                                   `pay_amount`   = '$penalti_fee'
                            WHERE  `id`           = '$id'");
        
    }elseif ($hidde_status == 3){
        $req = mysql_query("UPDATE `money_transactions_detail`
                               SET `datetime`     = NOW(),
                                   `user_id`      = '$user_id',
                                   `pay_datetime` = '$transaction_date',
                                   `pay_amount`   = '$surplus'
                            WHERE  `id`           = '$id'");
        
    }elseif ($hidde_status == 3){
        $req = mysql_query("UPDATE `money_transactions_detail`
                               SET `datetime`     = NOW(),
                                   `user_id`      = '$user_id',
                                   `pay_datetime` = '$transaction_date',
                                   `pay_amount`   = '$month_fee'
                            WHERE  `id`           = '$id'");
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
                        		   WHEN client.attachment_id = 0 AND client.id<302 THEN CONCAT('ს/ხ ',client.exel_agreement_id)
                                   WHEN client.attachment_id = 0 AND client.id>=302 THEN CONCAT('ს/ხ ',client_loan_agreement.id)
            					   WHEN client.attachment_id > 0 AND client.id<302 THEN concat('ს/ხ ',(SELECT cl.exel_agreement_id FROM client AS cl WHERE cl.id = client.attachment_id), '/დანართი N', client_loan_agreement.attachment_number)
            					   WHEN client.attachment_id != 0 AND client.id>=302 THEN concat('ს/ხ ',(SELECT client_loan_agreement.id FROM client_loan_agreement WHERE client_loan_agreement.client_id = client.attachment_id), '/დანართი N', client_loan_agreement.attachment_number)
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
    
	$res = mysql_fetch_assoc(mysql_query(" SELECT   money_transactions_detail.id,
	                                                money_transactions_detail.type_id,
                                    				client_loan_agreement.`client_id`,
                                    				money_transactions_detail.pay_amount,
                                    				money_transactions_detail.pay_root,
                                    				money_transactions_detail.pay_percent,
                                    				money_transactions_detail.type_id,
                                    				money_transactions.client_loan_schedule_id,
                                    				money_transactions_detail.datetime,
	                                                money_transactions.id AS tr_id,
	                                                money_transactions.extra_fee,
	                                                money_transactions.month_fee_trasaction,
                                    				money_transactions_detail.`status`
                                            FROM   `money_transactions_detail`
                                            JOIN  	money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                            JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE   money_transactions_detail.id = $id" ));
    return $res;
}


function GetPage($res = ''){
    $today = date("Y-m-d H:i:s");
    
    $display_none  = '';
    $display_none1 = '';
    $display_none2 = '';
    
    if ($res['status']==1) {
       $display_none1 = 'display:none';
       $display_none2= 'display:none';
    }elseif ($res['status']==2){
        $display_none = 'display:none';
        $display_none2= 'display:none';
    }elseif ($res['status']==3){
        $display_none = 'display:none';
        $display_none1= 'display:none';
    }
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
        $req = mysql_fetch_assoc(mysql_query("SELECT MAX(id),
                                                     cource 
                                              FROM   cur_cource
                                              WHERE  actived = 1 
                                              AND    DATE(datetime) = CURDATE() 
                                              LIMIT  1"));
        $cource = $req[cource];
        $date = $today;
    }else{
        $cource = $res[course];
        $date = $res[datetime];
    }
     if ($res['type_id'] == 1) {
         $res1= mysql_fetch_assoc(mysql_query("SELECT  client_loan_schedule.id,
                                        			   client_loan_schedule.pay_amount,
                                        			   client_loan_schedule.root,
                                        			   client_loan_schedule.percent,
                                                       client_loan_schedule.penalty AS penalty
                                                FROM  `client_loan_schedule`
                                                JOIN   money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                                                JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                WHERE  client_loan_schedule.id = $res[client_loan_schedule_id]
                                                LIMIT  1"));
     }elseif ($res['type_id'] == 2){
         $res1= mysql_fetch_assoc(mysql_query("SELECT  car_insurance_info.ins_payy AS pay_amount
                                               FROM   `car_insurance_info`
                                               WHERE   car_insurance_info.client_id = '$res[client_id]'
                                               AND     car_insurance_info.actived = 1
                                               AND     DATE(car_insurance_info.car_insurance_end) = CURDATE()"));
     }
    
    
    $res2 = mysql_fetch_assoc(mysql_query(" SELECT  SUM(money_transactions_detail.pay_amount) AS pay_amount
                                            FROM    money_transactions_detail
                                            JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                            JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE   client_loan_agreement.client_id = '$res[client_id]' 
                                            AND     money_transactions_detail.`status` = 3
                                            AND     money_transactions_detail.actived = 1"));
   
    $res3 = mysql_fetch_assoc(mysql_query(" SELECT  money_transactions_detail.pay_amount AS pay_amount
                                            FROM    money_transactions_detail
                                            JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                            JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE   client_loan_agreement.client_id = '$res[client_id]'
                                            AND     money_transactions_detail.`status` = 3
                                            AND     money_transactions_detail.balance_transaction_id = '$res[tr_id]'
                                            AND     money_transactions_detail.actived = 0"));
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table">
	            <table>
	                <tr>
    	                <td style="width: 200px;"><label calss="label" style="padding-top: 5px;" for="name">ტიპი</label></td>
    					<td style="width: 280px;"><label calss="label" style="padding-top: 5px;" for="date">მსესხებელი</label></td>
    					<td style="width: 120px;"><label calss="label" style="padding-top: 5px;" for="date">სესხის ნომერი</label></td>
    				</tr>
    				<tr>
    	                <td style="width: 200px;">
    						<select id="type_id"  calss="label" style="width: 175px;">'.type($res[type_id]).'</select>
    					</td>
    					<td style="width: 280px;">
    						<select id="client_id" calss="label" style="width: 260px;">'.client($res[client_id]).'</select>
    					</td>
    					<td style="width: 120px;">
    						<select id="client_loan_number" calss="label" style="width: 175px;">'.client_loan_number($res[client_id]).'</select>
    					</td>
    				</tr>
    				<tr>
    	                <td style="width: 200px;"><label calss="label" style="padding-top: 5px;" for="name">სესხის ვალუტა</label></td>
    					<td style="width: 280px;"><label calss="label" style="padding-top: 5px;" for="date"></label></td>
    					<td style="width: 120px;"><label calss="label" style="padding-top: 5px;" for="date"></label></td>
    				</tr>
    				<tr>
    	                <td style="width: 200px;">
    						<select id="currency_id"  calss="label" style="width: 155px;">'.currency($res[currency_id]).'</select>
    					</td>
    					<td style="width: 280px;">
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
    						<input style="width: 80px;" id="month_fee_trasaction" class="label" type="text" value="'.$res['month_fee_trasaction'].'" disabled="disabled">
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;">
    					</td>
    				    <td style="width: 135px;"></td>
    					<td style="width: 100px;">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="padding-top: 5px;" class="label" for="date">ჩარიცხული თანხა სესხის ვალუტაში:</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee" class="label" type="text" value="'.$res['pay_amount'].'">
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label" for="name">სულ შესატანი თანხა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="month_fee1" class="label" type="text" value="'.$res1['pay_amount'].'" disabled="disabled">
    					</td>
    				    <td style="width: 135px;"><label style="padding-top: 5px;" class="label" for="name">არსებული ბალანსი:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee2" class="label" type="text" value="'.$res2['pay_amount'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 105px; "><label style="'.$display_none.'" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 100px; ">
    						<input style="width: 80px; '.$display_none.'" id="root" class="label_label" type="text" value="'.$res['pay_root'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="root1" class="label_label" type="text" value="'.$res1['root'].'" disabled="disabled">
    					</td>
    				    <td style="width: 135px;"><label style="padding-top: 5px;" class="label" for="name">ჩარიცხვამდე ბალანსი:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="post_balance" class="label" type="text" value="'.$res3['pay_amount'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 105px; "><label style="'.$display_none.'" class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 100px; ">
    						<input style="width: 80px; '.$display_none.'" id="percent" class="label_label" type="text" value="'.$res['pay_percent'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;"  class="label_label" id="percent1" type="text" value="'.$res1['percent'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 105px; "><label style="'.$display_none1.'" class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 100px; ">
    						<input class="label_label" style="width: 80px; '.$display_none1.'" id="penalti_fee" type="text" value="'.$res['pay_penalty'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="penalti_fee1" class="label_label" type="text" value="'.$res1['penalty'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 105px; '.$display_none2.' "><label class="label_label" for="date">მეტობა</label></td>
    					<td style="width: 100px; '.$display_none2.'">
    						<input class="label_label" style="width: 80px; " id="surplus" type="text" value="'.$res['pay_amount'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 120px;"><label class="label_label" for="date">ზედმეტი თანხა</label></td>
    					<td style="width: 100px;"><input class="label_label" style="width: 80px; " id="extra_fee" type="text" value="'.$res['extra_fee'].'" disabled="disabled"></td>
    					<td style="width: 120px; color: #ff0000 "; id="error_mesage"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
				</table>
			</table>
			<!-- ID -->
			<input type="hidden" id="id" value="' . $res['id'] . '" />
			<input type="hidden" id="hidde_status" value="' . $res['status'] . '" />
		</fieldset>
    </div>
    ';
	return $data;
}

?>
