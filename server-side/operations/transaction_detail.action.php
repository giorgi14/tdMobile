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
	case 'get_client_chosen':
	
	    $id	= $_REQUEST['id'];
	
	    $data = array('client_data' => client_car($id));
	    break;
    
	case 'get_loan_number_chosen':
    
        $id	= $_REQUEST['id'];
    
        $data = array('loan_number_data' => pledge_client_loan_number($id));
        break;
    case 'get_pledge_dialog':
        
		$page = '<div id="dialog-form">
                     <fieldset>
                     <legend>ძირითადი ინფორმაცია</legend>
                        <table class="dialog-form-table" style="width: 100%;">
                           <tr>
            	                <td style="width: 330px;"><label calss="label" style="padding-top: 5px;" for="date">მსესხებელი</label></td>
            					<td style="width: 200px;"><label calss="label" style="padding-top: 5px;" for="date">სესხის ნომერი</label></td>
                                <td style="width: 135px;"><label>თანხა</label></td>
            				</tr>
            				<tr>
            	                <td style="width: 330px;">
            						<select id="pledge_client_id" calss="label" style="width: 310px;">'.client_car().'</select>
            					</td>
            					<td style="width: 200px;">
            						<select id="pledge_client_loan_number" calss="label" style="width: 175px;">'.pledge_client_loan_number().'</select>
            					</td>
            					<td style="width: 135px;">
            						<input class="idle" style="width: 135px;" id="client_pledge_amount" type="text" value="">
            				    </td>
            				</tr>
                            <tr>
            	                <td colspan="3" style="width: 330px;"><label calss="label" style="padding-top: 5px;" for="date">კომენტარი</label></td>
            				</tr>
            				<tr>
            	                <td colspan="3">
                                   <textarea class="idle" id="pledge_comment" style="resize: vertical;width: 99%;height: 40px;"></textarea>
                                </td>
            				</tr>
                        </table>
                        <input id="local_sub_id" type="hidden" value="">
                     </fieldset>
                 </div>';
        $data = array('page' => $page);
		
		break;
	case 'get_other_dialog':
        
		$page = '<div id="dialog-form">
                     <fieldset>
                     <legend>ძირითადი ინფორმაცია</legend>
                        <table class="dialog-form-table" style="width: 100%;">
		                    <tr>
            	                <td style="width: 250px;"><label calss="label" style="padding-top: 5px;" for="date">მსესხებელი</label></td>
            					<td style="width: 200px;"><label calss="label" style="padding-top: 5px;" for="date">სესხის ნომერი</label></td>
                                <td style="width: 135px;"><label>თანხა</label></td>
            				</tr>
            				<tr>
            	                <td style="width:250px;">
            						<select id="other_client_id" calss="label" style="width: 250px;">'.client_car().'</select>
            					</td>
            					<td style="width: 200px;">
            						<select id="other_client_loan_number" calss="label" style="width: 200px;">'.pledge_client_loan_number().'</select>
            					</td>
            					<td style="width: 135px;">
            						<input class="idle" style="width: 135px;" id="other_pledge_amount" type="text" value="">
            				    </td>
            				</tr>
            				<tr>
            	                <td colspan="3" style="width: 330px;"><label calss="label" style="padding-top: 5px;" for="date">მსესხებელი</label></td>
            				</tr>
            				<tr>
            	                <td colspan="3">
                                   <textarea class="idle" id="other_comment" style="resize: vertical;width: 100%;height: 40px;"></textarea>
                               </td>
            				</tr>
                        </table>
                     </fieldset>
                 </div>';
        $data = array('page' => $page);
		
		break;
	case 'get_list' :
		$count	        = $_REQUEST['count'];
		$hidden	        = $_REQUEST['hidden'];
		$tab	        = $_REQUEST['tab'];
		$transaction_id	= $_REQUEST['transaction_id'];
		
		$rResult = mysql_query("SELECT      money_transactions_detail.id,
                            				DATE_FORMAT(money_transactions_detail.pay_datetime,'%d/%m/%Y'),
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
		
		$client_id            = $_REQUEST['client_id'];
		$client_loan_number   = $_REQUEST['client_loan_number'];
		
		$month_fee_trasaction = $_REQUEST['month_fee_trasaction'];
		$extra_fee            = $_REQUEST['extra_fee'];
		
		$pledge_or_other_payed     = $_REQUEST['pledge_or_other_payed'];
		$pledge_or_other_surplus   = $_REQUEST['pledge_or_other_surplus'];
		$pledge_or_other_extra_fee = $_REQUEST['pledge_or_other_extra_fee'];
		
		$surplus_type          = $_REQUEST['surplus_type'];
		
		$hidde_id             = $_REQUEST['hidde_id'];
		$hidde_transaction_id = $_REQUEST['hidde_transaction_id'];
		$hidde_status         = $_REQUEST['hidde_status'];
		$user_id	          = $_SESSION['USERID'];
		
		if ($id == '') {
	        if ($tr_id == '') {
	            mysql_query("INSERT INTO `money_transactions` 
                                        (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`) 
                                  VALUES 
                                        (NOW(), '$user_id', '$hidde_id', '$client_loan_number', '$client_id', '$transaction_date', '$month_fee', '$extra_fee', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '$type_id', '0', '1')");
	            
	            $tr_id = mysql_insert_id();
	        }else{
	            mysql_query("UPDATE `money_transactions`
            	                SET `datetime`                = NOW(),
                	                `user_id`                 = '$user_id',
                	                `client_loan_schedule_id` = '$hidde_id',
	                                `agreement_id`            = '$client_loan_number',
	                                `client_id`               = '$client_id',
                	                `pay_datetime`            = '$transaction_date',
                	                `extra_fee`               = '$extra_fee',
                	                `course`                  = '$course',
                	                `currency_id`             = '$currency_id',
                	                `month_fee_trasaction`    = '$month_fee_trasaction',
                	                `type_id`                 = '$type_id'
            	              WHERE `id`                      = '$hidde_transaction_id'");
	            $tr_id = $hidde_transaction_id;
	            
	        }
	        if ($type_id == 2 || $type_id == 3) {
	            Add1($tr_id, $hidde_id, $transaction_date, $pledge_or_other_payed, $pledge_or_other_surplus, $course, $currency_id, $received_currency_id, $type_id, $surplus_type);
	        }elseif ($type_id == 1){
	           Add($tr_id, $hidde_id, $transaction_date, $month_fee, $course, $currency_id, $received_currency_id, $root,  $percent, $penalti_fee, $surplus, $diff, $type_id, $surplus_type);
	           
	        }
	        $data = array('tr_id' => $tr_id);
        }else{
            
            update($hidde_status, $id, $transaction_date, $month_fee,  $root,  $percent, $penalti_fee, $surplus);
            $data = array('tr_id' => $tr_id);
        }
		
		break;
		
	case 'save_pledge_distribution':
	    
	    $tr_id                     = $_REQUEST['tr_id'];
	    $client_amount             = $_REQUEST['client_amount'];
		$client_pledge_amount      = $_REQUEST['client_pledge_amount'];
		$received_currency_id      = $_REQUEST['received_currency_id'];
		$course                    = $_REQUEST['course'];
		$transaction_date          = $_REQUEST['transaction_date'];
		$pledge_client_id          = $_REQUEST['pledge_client_id'];
		$pledge_client_loan_number = $_REQUEST['pledge_client_loan_number'];
		$pledge_comment            = $_REQUEST['pledge_comment'];
		
		$user_id	               = $_SESSION['USERID'];
		
		if ($tr_id == '') {
		    mysql_query("INSERT INTO `money_transactions`
                    		        (`datetime`, `user_id`,  `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `type_id`, `comment`, `status`, `actived`)
                    		  VALUES
                    		        (NOW(), '$user_id', '$pledge_client_loan_number', '$pledge_client_id', '$transaction_date', '$client_amount', '$course', '$currency_id', '$received_currency_id', '2', '$pledge_comment', '0', '1')");
		     
		    $tr_id = mysql_insert_id();
		}else{
		    mysql_query("UPDATE `money_transactions`
        		            SET `agreement_id` = '$pledge_client_loan_number',
                		        `client_id`    = '$pledge_client_id',
                		        `pay_datetime` = '$transaction_date',
                		        `course`       = '$course',
                		        `currency_id`  = '$received_currency_id',
                		        `type_id`      = '2',
                		        `status`       = '1',
                		        `actived`      = '1'
        		          WHERE `id`           = '$tr_id'");
		}
        
                            
        mysql_query("INSERT INTO `money_transactions_detail` 
                                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `payed_status`,  `actived`) 
                          VALUES 
                                (NOW(), '$user_id', '$tr_id', '$transaction_date', '$client_pledge_amount', '$course', '$received_currency_id', '$received_currency_id', '', '', '2', '7', '1', '1')");
        
        $data = array('pledge_client_id' => $pledge_client_id, 'tr_id' => $tr_id);
        break;
    case 'save_other_distribution':
	    
	    $tr_id                     = $_REQUEST['tr_id'];
		$client_pledge_amount      = $_REQUEST['client_other_amount'];
		$received_currency_id      = $_REQUEST['other_cource'];
		$course                    = $_REQUEST['other_cource'];
		$transaction_date          = $_REQUEST['other_date'];
		$pledge_client_id          = $_REQUEST['other_client_id'];
		$pledge_client_loan_number = $_REQUEST['other_client_loan_number'];
		$other_comment             = $_REQUEST['other_comment'];
		
		$user_id	               = $_SESSION['USERID'];
		
		if ($tr_id == '') {
            mysql_query("INSERT INTO `money_transactions` 
                                    (`datetime`, `user_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `type_id`, `comment`, `status`, `actived`) 
                              VALUES 
                                    (NOW(), '$user_id', '$pledge_client_loan_number', '$pledge_client_id', '$transaction_date', '$client_pledge_amount', '$course', '$received_currency_id', '$received_currency_id', '3', '$other_comment', '1', '1')");
            
            $tr_id = mysql_insert_id();
		}else {
		   mysql_query("UPDATE `money_transactions`
            		        SET `agreement_id` = '$pledge_client_loan_number',
		                        `client_id`    = '$pledge_client_id',
                		        `pay_datetime` = '$transaction_date',
                		        `course`       = '$course',
                		        `currency_id`  = '$received_currency_id',
                		        `type_id`      = '3',
                		        `status`       = '1',
                		        `actived`      = '1'
            		      WHERE `id`           = '$tr_id'");
		}
		
        mysql_query("INSERT INTO `money_transactions_detail` 
                                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `payed_status`,  `actived`) 
                          VALUES 
                                (NOW(), '$user_id', '$tr_id', '$transaction_date', '$client_pledge_amount', '$course', '$received_currency_id', '$received_currency_id', '$course', '$received_currency_id', '3', '10', '1', '1')");
        
        $data = array('pledge_client_id' => $pledge_client_id, 'tr_id' => $tr_id);
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

function Add($hidde_transaction_id, $hidde_id, $transaction_date, $month_fee, $course, $currency_id, $received_currency_id, $root,  $percent, $penalti_fee, $surplus, $diff, $type_id, $surplus_type){
    
	$user_id	 = $_SESSION['USERID'];
	$client_id   = $_REQUEST['client_id'];
	
	$month_fee1    = $_REQUEST['month_fee1'];
	$payable_Fee   = $_REQUEST['payable_Fee'];
	$yield         = $_REQUEST['yield'];
	$other_penalty = $_REQUEST['other_penalty'];
	$exception_agr = $_REQUEST['exception_agr'];
	$surplus1      = $_REQUEST['surplus1'];
	
	$client_loan_number = $_REQUEST['client_loan_number'];
	$car_out            = $_REQUEST['car_out'];
	$attachment_client_id = $_REQUEST['attachment_client_id'];
	
	$attachment_agr_id = mysql_fetch_array(mysql_query("SELECT id 
                                                        FROM   client_loan_agreement 
                                                        WHERE  client_id = '$attachment_client_id'
                                                        LIMIT 1"));
	
	$attachment_agreement_id = $attachment_agr_id[id];
	
	$res = mysql_fetch_assoc(mysql_query(" SELECT  IFNULL(ROUND(SUM(CASE
                										WHEN money_transactions_detail.currency_id = client_loan_agreement.loan_currency_id THEN money_transactions_detail.pay_amount
                										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 1 THEN money_transactions_detail.pay_amount/money_transactions_detail.course
                										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 2 THEN money_transactions_detail.pay_amount*money_transactions_detail.course
                								   END),2),0) AS pay_amount
                                           FROM    money_transactions_detail
                                           JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                           JOIN    client ON client.id = money_transactions.client_id
                                           JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                           WHERE   client_loan_agreement.client_id = '$client_id' 
                                           AND     money_transactions_detail.`status` = 3
                                           AND     money_transactions_detail.actived = 1"));
	
	$res1 = mysql_fetch_assoc(mysql_query("SELECT  client_loan_schedule.pay_amount,
                                            	   client_loan_schedule.penalty AS penalty,
	                                               client_loan_schedule.id
                                    	   FROM   `client_loan_schedule`
	                                       JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    	   WHERE   client_loan_schedule.id = $hidde_id AND client_loan_schedule.actived = 1"));
	
    $all_pay = ROUND($month_fee + $res[pay_amount],2);
	$all_fee = $month_fee1;
	if ($exception_agr==1) {
	    if ($penalti_fee>0){
	        mysql_query("INSERT INTO `money_transactions_detail`
                    	            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	      VALUES
                    	            (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$penalti_fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '2', 1)");
	    }
	    
	    if ($root>0 || $percent>0) {
	        mysql_query("INSERT INTO `money_transactions_detail`
                    	            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	      VALUES
                    	            (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '', '$course', '$currency_id', '$received_currency_id', '$root', '$percent', '$type_id', 1, 1)");
	        
	        mysql_query("UPDATE `client_loan_schedule`
            	            SET `exception_status` = 1
            	         WHERE  `id`               = '$res1[id]'");
	    }
	    
	    //მეტობა სესხი
	    if ($surplus_type==1) {
	        if ($attachment_client_id>0) {
	            if ($surplus>0) {
	                mysql_query("INSERT INTO `money_transactions`
                    	                    (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
	                 
	                $tr1_id = mysql_insert_id();
	                 
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
	            }
	        }else{
	            if ($surplus>0) {
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
	            }
	        }
	        //მეტობა ალდაგი
	    }elseif ($surplus_type==2){
	        if ($attachment_client_id>0) {
	            if ($surplus>0) {
	                mysql_query("INSERT INTO `money_transactions`
                    	                    (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '2', '1', '1')");
	    
	                $tr1_id = mysql_insert_id();
	    
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
	            }
	        }else{
	            if ($surplus>0) {
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
	            }
	        }
	         
	        //მეტობა სესხი + ალდაგი
	    }elseif ($surplus_type==3){
	        if ($attachment_client_id>0) {
	            if ($surplus>0 || $surplus1>0) {
	                mysql_query("INSERT INTO `money_transactions`
                    	                    (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
	    
	                $tr1_id = mysql_insert_id();
	            }
	        	    
	            if ($surplus>0) {
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
	            }
	        	    
	            if ($surplus1>0) {
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
	            }
	        }else{
	            if ($surplus>0) {
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
	            }
	             
	            if ($surplus1>0) {
	                mysql_query("INSERT INTO `money_transactions_detail`
                    	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                    	              VALUES
                    	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
	            }
	        }
	    }
	}else{
    	if ($all_fee == $all_pay){
    	    
    	    if ($penalti_fee>0){
                mysql_query("INSERT INTO `money_transactions_detail` 
                        				(`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`) 
                        		  VALUES 
                        			    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$penalti_fee', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '2', 1)");
    	    }
    	    
    	    mysql_query(" UPDATE  money_transactions_detail
                          JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                          JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
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
    	        
    	        if ($other_penalty == 1) {
    	            mysql_query("UPDATE  `client_loan_schedule`
                	                SET  `status`        = '1',
    	                                 `penalty_check` = 1
                	              WHERE  `id`     = '$res1[id]'");
    	        }else{
        	        mysql_query("UPDATE  `client_loan_schedule`
        	                        SET  `status` = '1'
        	                      WHERE  `id`     = '$res1[id]'");
    	        }
    	    }
    	    //მეტობა სესხი
    	    if ($surplus_type==1) {
    	        if ($attachment_client_id>0) {
    	            if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions`
                            	                (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
        	            
        	            $tr1_id = mysql_insert_id();
        	            
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
    	            }
    	        }else{
        	        if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                                        	    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                          VALUES
                                        	    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
        	        }
    	        }
    	    //მეტობა ალდაგი
    	    }elseif ($surplus_type==2){
    	        if ($attachment_client_id>0) {
    	            if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions`
                            	                (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '2', '1', '1')");
        	             
        	            $tr1_id = mysql_insert_id();
        	             
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
    	            }
    	        }else{
        	        if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
        	        }
    	        }
    	        
    	    //მეტობა სესხი + ალდაგი
    	    }elseif ($surplus_type==3){
    	        if ($attachment_client_id>0) {
    	            if ($surplus>0 || $surplus1>0) {
        	            mysql_query("INSERT INTO `money_transactions`
                            	                (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
        	             
        	            $tr1_id = mysql_insert_id();
    	            }
    	            
    	            if ($surplus>0) { 
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
    	            }
    	            
    	            if ($surplus1>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
    	            }
    	            
    	        }else{
        	        if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                                        	    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                          VALUES
                                        	    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
        	        }
        	    
        	        if ($surplus1>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
        	        }
    	        }
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
    	    
    	    $res_car_out = mysql_query("SELECT id 
                                        FROM   client_loan_schedule 
                                        WHERE  id > '$hidde_id' AND client_loan_schedule.client_loan_agreement_id = '$client_loan_number'");
    	    
    	    if ($car_out == 1) {
    	       mysql_query("UPDATE client_loan_agreement
            	               SET canceled_status = 1,
    	                           canseled_date   = '$transaction_date'
            	            WHERE  client_loan_agreement.id = '$client_loan_number' ");
    	       
    	       mysql_query("UPDATE  client_loan_schedule
                                SET activ_status = 1
                             WHERE  client_loan_schedule.client_loan_agreement_id = '$client_loan_number'
                             AND    client_loan_schedule.actived = 1
                             AND    client_loan_schedule.schedule_date > '$transaction_date'");
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
        	    
    	        if ($other_penalty == 1) {
    	            mysql_query("UPDATE  `client_loan_schedule`
                	                SET  `status`        = '1',
    	                                 `penalty_check` = 1
                	              WHERE  `id`     = '$res1[id]'");
    	        }else{
        	        mysql_query("UPDATE  `client_loan_schedule`
        	                        SET  `status` = '1'
        	                      WHERE  `id`     = '$res1[id]'");
    	        }
    	    
    	    }
    	    
    	    mysql_query("UPDATE  money_transactions_detail
                         JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                         JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
    	                 SET     money_transactions_detail.actived = 0,
    	                         money_transactions_detail.balance_transaction_id = '$hidde_transaction_id'
                         WHERE   client_loan_agreement.client_id = '$client_id' 
                         AND     money_transactions_detail.`status` = 3
                         AND     money_transactions_detail.actived = 1");
    	    
    	    
    	    //მეტობა სესხი
    	    if ($surplus_type==1) {
    	        if ($attachment_client_id>0) {
    	            if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions`
                            	                (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
        	             
        	            $tr1_id = mysql_insert_id();
        	             
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
    	            }
    	        }else{
        	        if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                                        	       (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                        	 VALUES
                                        	       (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
        	        }
    	        }
    	    //ალდაგი
    	    }elseif ($surplus_type==2){
    	        if ($attachment_client_id>0) {
    	            if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions`
                            	                (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '2', '1', '1')");
        	        
        	            $tr1_id = mysql_insert_id();
        	        
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
    	            }
    	        }else{
    	            if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
        	        }
    	        }
    	    //მეტობა სესხი + ალდაგი
    	    }elseif ($surplus_type==3){
    	        if ($attachment_client_id>0) {
    	            if ($surplus>0 || $surplus1>0) {
        	            mysql_query("INSERT INTO `money_transactions`
                            	                (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
        	        
        	            $tr1_id = mysql_insert_id();
    	            } 
    	            if ($surplus>0) {
    	                mysql_query("INSERT INTO `money_transactions_detail`
                        	                    (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	              VALUES
                        	                    (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
    	            }
    	             
    	            if ($surplus1>0) {
    	                mysql_query("INSERT INTO `money_transactions_detail`
                        	                    (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	              VALUES
                        	                    (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
    	            }
    	             
    	        }else{
        	        if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                                        	       (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                        	 VALUES
                                        	       (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
        	        }
        	    
        	        if ($surplus1>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
        	        }
    	        }
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
    	    
    	    $res_car_out = mysql_query("SELECT id
                            	        FROM   client_loan_schedule
                            	        WHERE  id > '$hidde_id' AND client_loan_schedule.client_loan_agreement_id = '$client_loan_number'");
    	     
    	    if ($car_out == 1) {
    	        mysql_query("UPDATE client_loan_agreement
            	                SET canceled_status = 1,
    	                            canseled_date   = '$transaction_date'
            	             WHERE  client_loan_agreement.id = '$client_loan_number' ");
    	        
    	        mysql_query("UPDATE client_loan_schedule
            	                SET activ_status = 1
            	             WHERE  client_loan_schedule.client_loan_agreement_id = '$client_loan_number'
            	             AND    client_loan_schedule.actived = 1
            	             AND    client_loan_schedule.schedule_date > '$transaction_date'");
    	    }
    	}else{
    	    
    	    $check_loan_type = mysql_fetch_array(mysql_query("SELECT loan_type_id 
                                                    	      FROM   client_loan_agreement 
                                                    	      WHERE  client_id = '$client_id'"));
    	    
    	    if ($check_loan_type[loan_type_id] == 1) {
    	        $percent              = $_REQUEST['percent'];
    	        $penalti_fee          = $_REQUEST['penalti_fee'];
    	        $percent1             = $_REQUEST['percent1'];
    	        $penalti_fee1         = $_REQUEST['penalti_fee1'];
    	        
    	        if ($percent == '') {$percent = 0;}
    	        if ($penalti_fee == '') {$penalti_fee = 0;}
    	        if ($percent1 == '') {$percent1 = 0;}
    	        if ($penalti_fee1 == '') {$penalti_fee1 = 0;}
    	        
    	        $sul_gadasaxdeli = $percent1+$penalti_fee1;
    	        $sul_gadaxdili = $percent+$penalti_fee;
    	        
    	        if ($sul_gadasaxdeli<=$sul_gadaxdili) {
    	            
    	            mysql_query(" UPDATE  money_transactions_detail
            	                  JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
            	                  JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
            	                  SET     money_transactions_detail.actived = 0,
            	                          money_transactions_detail.balance_transaction_id = '$hidde_transaction_id'
            	                  WHERE   client_loan_agreement.client_id = '$client_id'
            	                  AND     money_transactions_detail.`status` = 3
            	                  AND     money_transactions_detail.actived = 1");
    	             
    	            if ($percent>=$percent1) {
    	                 
    	                mysql_query("INSERT INTO `money_transactions_detail`
                        	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	              VALUES
                        	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '', '$course', '$currency_id', '$received_currency_id', '$root', '$percent', '$type_id', 1, 1)");
    	                 
    	                mysql_query("UPDATE  `client_loan_schedule`
                	                    SET  `status` = '1'
                	                  WHERE  `id`     = '$res1[id]'");
    	            }
    	            if ($attachment_client_id>0) {
    	                //მეტობა
    	                if ($surplus>0 || $surplus1>0) {
    	                    mysql_query("INSERT INTO `money_transactions`
                        	                        (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                        	                  VALUES
                        	                        (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
    	            
    	                    $tr1_id = mysql_insert_id();
    	                    if ($surplus>0) {
        	                    mysql_query("INSERT INTO `money_transactions_detail`
                            	                        (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	                  VALUES
                            	                        (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
    	                    }
    	                    if ($surplus1>0) {
    	                        mysql_query("INSERT INTO `money_transactions_detail`
                        	                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	                      VALUES
                        	                            (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
    	                    }
    	                }
    	            }else{
        	            if ($surplus>0) {
        	                mysql_query("INSERT INTO `money_transactions_detail`
                            	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	              VALUES
                            	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
        	            }
        	             
        	            if ($surplus1>0) {
        	                mysql_query("INSERT INTO `money_transactions_detail`
                            	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	              VALUES
                            	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
        	            }
    	            }
    	        }else{
    	            
    	            mysql_query(" UPDATE  money_transactions_detail
                	                JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                	                JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                	                SET     money_transactions_detail.actived = 0,
                	                money_transactions_detail.balance_transaction_id = '$hidde_transaction_id'
                	                WHERE   client_loan_agreement.client_id = '$client_id'
                	                AND     money_transactions_detail.`status` = 3
                	                AND     money_transactions_detail.actived = 1");
    	            if ($attachment_client_id>0) {
    	                //მეტობა
    	                if ($surplus>0 || $surplus1>0) {
    	                    mysql_query("INSERT INTO `money_transactions`
                        	                        (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                        	                  VALUES
                        	                        (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
    	                     
    	                    $tr1_id = mysql_insert_id();
    	                    if ($surplus>0) {
    	                        mysql_query("INSERT INTO `money_transactions_detail`
                        	                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	                      VALUES
                        	                            (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
    	                    }
    	                    if ($surplus1>0) {
    	                        mysql_query("INSERT INTO `money_transactions_detail`
                        	                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                        	                      VALUES
                        	                            (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
    	                    }
    	                }
    	            }else{
        	            if ($surplus>0) {
        	                mysql_query("INSERT INTO `money_transactions_detail`
                            	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	              VALUES
                            	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
        	            }
        	             
        	            if ($surplus1>0) {
        	                mysql_query("INSERT INTO `money_transactions_detail`
                            	                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	              VALUES
                            	                    (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
        	            }
    	            }
    	            if ($surplus1 <= 0 && $surplus1 <= 0) {
    	                global  $error;
    	                $error = 'გადანწილებული თანხა არა საკმარისი';
    	            }
    	        }
    	        
    	    }else{
    	        
    	       mysql_query(" UPDATE  money_transactions_detail
            	             JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
            	             JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
            	             SET  money_transactions_detail.actived = 0,
            	             money_transactions_detail.balance_transaction_id = '$hidde_transaction_id'
            	             WHERE   client_loan_agreement.client_id = '$client_id'
            	             AND     money_transactions_detail.`status` = 3
            	             AND     money_transactions_detail.actived = 1");
    	       
    	       
    	       if ($attachment_client_id>0) {
    	           //მეტობა
    	           if ($surplus>0 || $surplus1>0) {
    	               mysql_query("INSERT INTO `money_transactions`
    	                   (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
    	                   VALUES
    	                   (NOW(), '$user_id', '', '$attachment_agreement_id', '$attachment_client_id', '$transaction_date', '$surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
    	       
    	               $tr1_id = mysql_insert_id();
    	               if ($surplus>0) {
    	                   mysql_query("INSERT INTO `money_transactions_detail`
    	                       (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
    	                       VALUES
    	                       (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
    	               }
    	               if ($surplus1>0) {
    	                   mysql_query("INSERT INTO `money_transactions_detail`
    	                       (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
    	                       VALUES
    	                       (NOW(), '$user_id', '$tr1_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
    	               }
    	           }
    	       }else{
    	           //მეტობა
        	        if ($surplus>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                                        	       (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                        	 VALUES
                                        	       (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus', '$course', '$currency_id', '$received_currency_id', '', '', '$type_id', '3', 1)");
        	        }
        	    
        	        if ($surplus1>0) {
        	            mysql_query("INSERT INTO `money_transactions_detail`
                            	                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                            	          VALUES
                            	                (NOW(), '$user_id', '$hidde_transaction_id', '$transaction_date', '$surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
        	        }
    	       }
    	    }
    	}
    }
}

function Add1($tr_id, $hidde_id, $transaction_date, $pledge_or_other_payed, $pledge_or_other_surplus, $course, $currency_id, $received_currency_id, $type_id, $surplus_type){

    $user_id	= $_SESSION['USERID'];
    $client_id  = $_REQUEST['client_id'];
    
    $month_fee_gel   = $_REQUEST['month_fee_gel'];
    $month_fee_usd   = $_REQUEST['month_fee_usd'];
    
    $month_payed_gel = $_REQUEST['month_payed_gel'];
    $month_payed_usd = $_REQUEST['month_payed_usd'];
    
    $pledge_or_other_surplus1 = $_REQUEST['pledge_or_other_surplus1'];
    $attachment_client_id = $_REQUEST['attachment_client_id'];
    
    $attachment_agr_id = mysql_fetch_array(mysql_query("SELECT id
                                                        FROM   client_loan_agreement
                                                        WHERE  client_id = '$attachment_client_id'
                                                        LIMIT 1"));
    
    if($type_id == 2){
        if ($pledge_or_other_payed > 0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                          VALUES
                                (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_payed', '$course', '$currency_id', '$received_currency_id', '', '', '2', '8', 1)");
        }
        
        mysql_query(" UPDATE  money_transactions_detail
                      JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                      JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                      SET     money_transactions_detail.actived = 0,
                              money_transactions_detail.balance_transaction_id = '$tr_id'
                      WHERE   client_loan_agreement.client_id = '$client_id'
                      AND     money_transactions_detail.`status` = 9
                      AND     money_transactions_detail.actived = 1");
        
        if (($month_fee_gel<=$month_payed_gel || $month_fee_usd<= $month_payed_usd) && $month_payed_gel>0 && $month_payed_usd>0){
            
            $tr_id1 = mysql_query("SELECT money_transactions_detail.id AS tr_id,
                                          money_transactions_detail.course
                                   FROM   money_transactions
                                   JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                   WHERE  money_transactions_detail.actived = 1 AND money_transactions.actived = 1
                                   AND    money_transactions_detail.`status` = 7 AND money_transactions_detail.payed_status = 1
                                   AND    money_transactions.type_id = 2 AND money_transactions.client_id = '$client_id'
                                   AND    money_transactions.pay_datetime <= '$transaction_date'");
            
            while ($row = mysql_fetch_array($tr_id1)) {
                mysql_query("UPDATE money_transactions_detail
                                SET payed_status           = 2,
                                    balance_transaction_id = '$tr_id'
                             WHERE  id                     = '$row[tr_id]'");
            }
        }
        //მეტობა სესხი
        if ($surplus_type==1) {
            if ($attachment_client_id>0) {
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions`
                                            (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                                       VALUES
                                            (NOW(), '$user_id', '', '$tr_id', '$attachment_client_id', '$transaction_date', '$pledge_or_other_surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
                     
                    $tr1_id = mysql_insert_id();
                     
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                }
            }else{
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                }
            }
        //ალდაგი
        }elseif ($surplus_type==2){
            if ($attachment_client_id>0) {
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions`
                                            (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                                       VALUES
                                            (NOW(), '$user_id', '', '$tr_id', '$attachment_client_id', '$transaction_date', '$pledge_or_other_surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '2', '1', '1')");
                     
                    $tr1_id = mysql_insert_id();
                     
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                }
            }else{
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                }
            }
        //მეტობა სესხი + ალდაგი
        }elseif ($surplus_type==3){
            if ($attachment_client_id>0) {
                
                if ($pledge_or_other_surplus>0 || $pledge_or_other_surplus1>0) {
                    mysql_query("INSERT INTO `money_transactions`
                                            (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '', '$tr_id', '$attachment_client_id', '$transaction_date', '$pledge_or_other_surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
                     
                    $tr1_id = mysql_insert_id();
                }
                
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                }
                
                if ($pledge_or_other_surplus1>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                }
            }else{
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                }
                
                if ($pledge_or_other_surplus1>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                }
            }
        }
        
    }else if($type_id == 3){
        if ($pledge_or_other_payed>0) {
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_payed', '$course', '$currency_id', '$received_currency_id', '', '', '3', '11', 1)");
        }
        //მეტობა სესხი
        if ($surplus_type==1) {
            if ($attachment_client_id>0) {
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions`
                                            (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '', '$tr_id', '$attachment_client_id', '$transaction_date', '$pledge_or_other_surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
                     
                    $tr1_id = mysql_insert_id();
                     
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                }
            }else{
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                }
            }
        //მეტობა ალდაგი
        }elseif ($surplus_type==2){
            if ($attachment_client_id>0) {
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions`
                                            (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '', '$tr_id', '$attachment_client_id', '$transaction_date', '$pledge_or_other_surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '2', '1', '1')");
                     
                    $tr1_id = mysql_insert_id();
                     
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                }
            }else{
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                }
            }
        //მეტობა სესხი + ალდაგი
        }elseif ($surplus_type==3){
            if ($attachment_client_id>0) {
                if ($pledge_or_other_surplus>0 || $pledge_or_other_surplus1>0) {
                    mysql_query("INSERT INTO `money_transactions`
                                            (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '', '$tr_id', '$attachment_client_id', '$transaction_date', '$pledge_or_other_surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
                     
                    $tr1_id = mysql_insert_id();
                    if ($pledge_or_other_surplus>0) {
                        mysql_query("INSERT INTO `money_transactions_detail`
                                                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                          VALUES
                                                (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                    }
                    
                    if ($pledge_or_other_surplus1>0) {
                        mysql_query("INSERT INTO `money_transactions_detail`
                                                (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                          VALUES
                                                (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                    }
                }
            }else{
                if ($pledge_or_other_surplus>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                }
                
                if ($pledge_or_other_surplus1>0) {
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus1', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
                }
            }
        }
    }else{
        if ($attachment_client_id>0) {
            if ($pledge_or_other_surplus>0) {
                mysql_query("INSERT INTO `money_transactions`
                                        (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '', '$tr_id', '$attachment_client_id', '$transaction_date', '$pledge_or_other_surplus', '', '$course', '$currency_id', '$received_currency_id', '$month_fee_trasaction', '1', '1', '1')");
                 
                
                
                    mysql_query("INSERT INTO `money_transactions_detail`
                                            (`datetime`, `user_id`, `transaction_id`, `parent_tr_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                      VALUES
                                            (NOW(), '$user_id', '$tr1_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '1', '3', 1)");
                
            }
        }else{
            if ($pledge_or_other_surplus>0) {
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$transaction_date', '$pledge_or_other_surplus', '$course', '$currency_id', '$received_currency_id', '', '', '2', '9', 1)");
            }
        }
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
        
    }else{
        $req = mysql_query("UPDATE `money_transactions_detail`
                               SET `datetime`     = NOW(),
                                   `user_id`      = '$user_id',
                                   `pay_datetime` = '$transaction_date',
                                   `pay_amount`   = '$surplus'
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

function surplus_type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   surplus_type");

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

function client_car($id){
    $req = mysql_query("SELECT    cl.id AS cl_id,
                                  client_loan_agreement.id,
            					  CASE
            						  WHEN cl.attachment_id = 0 AND cl.`name` != '' THEN concat(client_car.registration_number, '/', client_loan_agreement.oris_code)
            						  WHEN cl.attachment_id = 0 AND cl.`name` = '' THEN CONCAT(client_car.registration_number, '/', client_loan_agreement.oris_code)
            						  WHEN cl.attachment_id != 0 AND cl.`name` = '' THEN CONCAT(client_car.registration_number, '/', client_loan_agreement.oris_code)
            						  WHEN cl.attachment_id != 0 AND cl.`name` != '' THEN concat(client_car.registration_number, '/', client_loan_agreement.oris_code, '/დანართი N', client_loan_agreement.attachment_number)
            					  END AS `name`
                        FROM      client AS cl
                        JOIN      client_loan_agreement ON cl.id = client_loan_agreement.client_id
                        JOIN      client_car ON client_car.client_id = cl.id
                        WHERE     cl.actived=1 AND client_loan_agreement.`status`=1 AND client_loan_agreement.canceled_status=0");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['cl_id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['cl_id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function client($id, $tr_det_id){
    $where = '';
    if ($tr_det_id == '') {
        $where = 'AND    client_loan_agreement.canceled_status = 0';
    }
    $req = mysql_query("SELECT    cl.id,
                                  CASE
                        			  WHEN cl.attachment_id = 0 AND cl.`name` != '' THEN concat(cl.`name`, ' ', cl.lastname)
                                      WHEN cl.attachment_id = 0 AND cl.`name` = '' THEN cl.ltd_name
                                      WHEN cl.attachment_id != 0 AND cl.`name` = '' THEN cl.ltd_name
                                      WHEN cl.attachment_id != 0 AND cl.`name` != '' THEN concat(cl.`name`, ' ', cl.lastname, '/დანართი N', client_loan_agreement.attachment_number)
                                  END AS `name`
                        FROM      client AS cl
                        JOIN      client_loan_agreement ON cl.id = client_loan_agreement.client_id
                        WHERE     cl.actived=1 AND client_loan_agreement.`status`=1 $where
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

function pledge_client_loan_number($id){
    
    $req = mysql_query("SELECT  client_loan_agreement.id as agr_id,
                                client.id,
                                CASE
            						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number='','',' დ.'), IF(client_loan_agreement.attachment_number='', '', client_loan_agreement.attachment_number))
            						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('სხ ',client_loan_agreement.agreement_id, ' დანართი ', client_loan_agreement.attachment_number)
                                     WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('სხ ',client_loan_agreement.agreement_id)
                                     WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('სხ ',client_loan_agreement.oris_code)
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
            $data .= '<option value="' . $res['agr_id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['agr_id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function client_loan_number($id, $tr_det_id){
    $where = '';
    if ($tr_det_id == '') {
        $where = 'AND client_loan_agreement.canceled_status = 0';
    }
    $req = mysql_query("SELECT  client_loan_agreement.id,
                                CASE
            						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number),'',' დ.'), IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number), '', client_loan_agreement.attachment_number))
            						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('სხ ',client_loan_agreement.agreement_id, ' დანართი ', client_loan_agreement.attachment_number)
                                     WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('სხ ',client_loan_agreement.agreement_id)
                                     WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('სხ ',client_loan_agreement.oris_code)
                			    END AS `name` 
                        			 
                         FROM   client_loan_agreement
                         JOIN   client ON client.id = client_loan_agreement.client_id
                         WHERE  client_loan_agreement.actived = 1 
                         AND    client_loan_agreement.`status` = 1
                         $where 
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
	                                                client_loan_agreement.`id` AS agr_id,
                                    				money_transactions_detail.pay_amount,
                                    				money_transactions_detail.pay_root,
                                    				money_transactions_detail.pay_percent,
                                    				money_transactions_detail.type_id,
                                    				money_transactions.client_loan_schedule_id,
                                    				money_transactions_detail.datetime,
	                                                money_transactions.id AS tr_id,
	                                                money_transactions.extra_fee,
	                                                money_transactions.month_fee_trasaction,
	                                                money_transactions.out_cal_status,
                                    				money_transactions_detail.`status`
                                            FROM   `money_transactions_detail`
                                            JOIN  	money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                            JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                            WHERE   money_transactions_detail.id = $id" ));
    return $res;
}


function GetPage($res = ''){
    $today = date("Y-m-d H:i:s");
    
    if ($res[type_id] == 1 || $res[type_id] == '') {
        $loan_table_hidde = "";
        $pledge_table_hidde = "display:none;";
    }else{
        $loan_table_hidde = "display:none;";
        $pledge_table_hidde = "";
    }
    
    if ($res[out_cal_status] == 1) {
        $hidde_out_car = '';
    }else{
        $hidde_out_car = 'display:none;';
    }
    
    if ($res['id']=='') {
        $req = mysql_fetch_assoc(mysql_query("SELECT cource 
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
	            <table id="loan_table1" style="'.$loan_table_hidde.'">
	                <tr>
    	                <td style="width: 240px; padding-top: 4px;">
                	       <label calss="label" style="padding-top: 5px;" for="name">ჯარიმა მეორე პირგასამტეხლოს მიხედვით</label>
                	    </td>
    					<td style="width: 120px;">
                	       <input class="idle" style="width: 15px;" id="other_penalty" value="1" type="checkbox">
                	    </td>
	                    <td style="width: 114px;">
                	       <label calss="label" style="padding-top: 5px;" for="name">დარიცხვის თარიღი:</label>
                	    </td>
    					<td style="width: 120px;">
                	       <label calss="label" id="daricxvis_tarigi" style="padding-top: 5px;" for="name"></label>
                	    </td>
    				</tr>
	           </table>
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
    						<select id="client_id" calss="label" style="width: 260px;">'.client($res[client_id], $res[id]).'</select>
    					</td>
    					<td style="width: 120px;">
    						<select id="client_loan_number" calss="label" style="width: 175px;">'.client_loan_number($res[agr_id], $res[id]).'</select>
    					</td>
    				</tr>
    				<tr>
    	                <td style="width: 200px;"><label calss="label" style="padding-top: 5px;" for="name">სესხის ვალუტა</label></td>
    					<td style="width: 280px;"><label calss="label" style="padding-top: 5px;" for="date">მანქანის გაყვანა</label></td>
    					<td style="width: 120px;"><label calss="label" style="padding-top: 5px;" for="date">მეტობის ტიპი</label></td>
    				</tr>
    				<tr>
    	                <td style="width: 200px;">
    						<select id="currency_id"  calss="label" style="width: 155px;">'.currency($res[currency_id]).'</select>
    					</td>
    					<td style="width: 280px;">
    						<input class="idle" style="width: 15px;" id="car_out" value="1" disabled type="checkbox">
    					</td>
    					<td style="width: 120px;"><select id="surplus_type"  calss="label" style="width: 180px;">'.surplus_type($res[currency_id]).'</select></td>
    				</tr>
    			    <tr>
    	                <td style="width: 200px;"></td>
    					<td style="width: 280px;"><label calss="label" style="padding-top: 5px;" for="name">შეთანხმება</label></td>
    					<td style="width: 120px;"><label calss="label" style="padding-top: 5px;" for="date">დანართი</label></td>
    				</tr>
    				<tr>
    	                <td style="width: 200px;"></td>
    					<td style="width: 280px;"><input class="idle" style="width: 15px;" id="exception_agr" value="1" disabled type="checkbox"></td>
    					<td style="width: 120px;"><select id="attachment_client_id"  calss="label" style="width: 180px;"></select></td>
    				</tr>
    			</table>
    			<table id="loan_table" style="'.$loan_table_hidde.'">
    				<tr style="height:40px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="padding-top: 5px;" class="label" for="date">ჩარიცხული თანხა:</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee_trasaction" class="label" type="text" value="'.$res['month_fee_trasaction'].'" disabled="disabled">
    					</td>
    					<td colspan="2" style="width: 100px;"><label id="info_mesage" style="padding-top: 5px; margin-left: 10px; color: red; font-size: 15px;" for="date"></label></td>
    					
    					</td>
    				    <td style="width: 135px;"></td>
    					<td style="width: 100px;">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="" class="label" for="date">ჩარიცხული თანხა სესხის ვალუტაში:</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee" class="label" type="text" value="'.$res['pay_amount'].'" disabled="disabled">
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
    					<td style="width: 105px;"><label id="" style="padding-top: 5px;" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 100px;">
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
    				<tr>
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
    				<tr>
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
    						<input class="label_label" style="width: 70px; float:left;" id="payable_Fee" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'" '.$disable.'><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_payable_Fee" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
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
    					<td style="width: 105px; padding-top: 5px;"><label id="surplus_label" for="date">მეტობა</label></td>
    					<td style="width: 100px;">
    						<input style="width: 70px; float:left;" id="surplus" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_surplus" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    			    <tr class="surplus" style="height:10px; display:none;"></tr>
    				<tr class="surplus" style="display:none;">
    					<td style="width: 105px; padding-top: 5px;"><label for="date">მეტობა(ალდაგი)</label></td>
    					<td style="width: 100px;">
    						<input style="width: 70px; float:left;" id="surplus1" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_surplus1" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 120px;"><label for="date">ზედმეტი თანხა:</label></td>
    					<td style="width: 100px;"><input style="width: 80px; " id="extra_fee" type="text" value="'.$res['extra_fee'].'" disabled="disabled"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
				</table>
    			<table id="pledge_table" style="'.$pledge_table_hidde.'">
    			    <tr style="height:40px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="padding-top: 5px;" for="date">ჩარიცხული თანხა</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="pledge_or_other_mont_fee" type="text" value="'.$res['month_fee_trasaction'].'" disabled="disabled">
    					</td>
    					<td style="width: 100px;"></td>
    					<td style="width: 80px;"></td>
    				    <td style="width: 135px;"></td>
    					<td style="width: 100px;"></td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="padding-top: 5px;" for="date">ჩარიცხული თანხა (GEL):</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="month_payed_gel" type="text" value="'.$res['month_fee_trasaction'].'" disabled="disabled">
    					</td>
    					<td style="width: 100px;">დარიცხული თანხა (GEL)</td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="month_fee_gel" class="label" type="text" value="'.$res['pay_amount'].'" disabled="disabled">
    					</td>
    				    <td style="width: 135px;">
    						<label style="padding-top: 5px;" for="name">არსებული ბალანსი (GEL):</label> 
    				    </td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="pledge_or_other_balance_gel" class="label" type="text" value="'.$res2['pay_amount'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label style="" class="label" for="date">ჩარიცხული თანხა (USD):</label></td>
                	    <td style="width: 100px;">
    						<input style="width: 80px;" id="month_payed_usd" class="label" type="text" value="'.$res['pay_amount'].'" disabled="disabled">
    					</td>
    					<td style="width: 100px;"><label style="padding-top: 5px;" class="label" for="name">დარიცხული თანხა (USD)</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="month_fee_usd" class="label" type="text" value="'.$res1['pay_amount'].'" disabled="disabled">
    					</td>
    				    <td style="width: 135px;"><label style="padding-top: 5px;" class="label" for="name">არსებული ბალანსი (USD):</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="pledge_or_other_balance_usd" type="text" value="'.$res2['pay_amount'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px;"><label id="" style="padding-top: 5px;" for="date">თანხა:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 70px; float:left;" id="pledge_or_other_payed" onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" type="text" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px;"><button id="delete_amount" style="width:20px; padding: 0 0 2px 0; color: #fb0000;">x</button></span>
    					</td>
    					<td style="width: 100px;"></td>
    					<td style="width: 80px;"></td>
    				    <td style="width: 135px;"></td>
    					<td style="width: 100px;"></td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 105px; padding-top: 5px;"><label id="pledge_or_other_surplus1_label" for="date">მეტობა</label></td>
    					<td style="width: 100px;">
    						<input style="width: 70px; float:left;" id="pledge_or_other_surplus" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_pledge_surplus" style="width:20px; padding: 0 0 2px 0; color: #fb0000;">x</button></span>
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    				<tr class="pledge_or_other_surplus1" style="height:10px; dislpay:none;"></tr>
    				<tr class="pledge_or_other_surplus1" style="display:none">
    					<td style="width: 105px; padding-top: 5px;"><label for="date">მეტობა(ალდაგი)</label></td>
    					<td style="width: 100px;">
    						<input style="width: 70px; float:left;" id="pledge_or_other_surplus1" type="text"  onkeydown="if(event.which == 8 || event.keyCode == 46) return false;" value="'.$res['pay_amount'].'"><span style="float: right; display: inline; margin-top: 4px; "><button id="delete_pledge_surplus1" class="label_label" style="width:20px; padding: 0 0 2px 0; color: #fb0000; '.$display_none1.'">x</button></span>
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr>
    					<td style="width: 120px;"><label for="date">ზედმეტი თანხა:</label></td>
    					<td style="width: 100px;"><input style="width: 80px; " id="pledge_or_other_extra_fee" type="text" value="'.$res['extra_fee'].'" disabled="disabled"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 100px;"></td>
    					<td style="width: 120px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
				</table>
			</table>
			<!-- ID -->
			<input type="hidden" id="idd" value="' . $res['id'] . '" />
			<input type="hidden" id="hidde_status" value="' . $res['status'] . '" />
			    
			<input type="hidden" id="hidde_root" value="0" />
	        <input type="hidden" id="hidde_percent" value="0" />
            <input type="hidden" id="hidde_penalty" value="0" />
		    <input type="hidden" id="hidde_payable_Fee" value="0" />
		    <input type="hidden" id="hidde_payable_Fee" value="0" />
            <input type="hidden" id="hidde_yield" value="0" />
			<input type="hidden" id="hidde_surplus" value="0" />
			<input type="hidden" id="hidde_surplus1" value="0" />
			    
			<input type="hidden" id="hidde_pledge_amount" value="0" />
			<input type="hidden" id="hidde_pledge_surplus" value="0" />
			<input type="hidden" id="hidde_pledge_surplus1" value="0" />
			    
        </fieldset>
    </div>
    ';
	return $data;
}

?>
