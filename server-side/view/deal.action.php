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
	    $page		= GetPage(GetSchedule($id));
        $data		= array('page'	=> $page);

		break;
	case 'get_cource':
	    $transaction_date = $_REQUEST['transaction_date'];
	    $cource = mysql_fetch_array(mysql_query("SELECT cource FROM cur_cource WHERE DATE(datetime) = DATE('$transaction_date')"));
	
	    $data	= array('cource' => $cource[cource]);
	    break;
    case 'get_penalty':
        $id	                = $_REQUEST['client_id'];
	    $agr_id             = $_REQUEST['client_loan_number'];
	    $deal_penalty_start = $_REQUEST['deal_penalty_start'];
	    $deal_penalty_end   = $_REQUEST['deal_penalty_end'];
	    $transaction_date   = $_REQUEST['payed_date'];
	    
        $filt = "AND client_loan_agreement.client_id = $id AND client_loan_agreement.id = $agr_id";
	      
	
	
        $check_penalty = mysql_fetch_array(mysql_query("SELECT   client_loan_schedule.penalty_stoped,
                                                	             client_loan_schedule.other_amount,
                                                	             DATEDIFF('$deal_penalty_end','$deal_penalty_start') AS datediff,
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
                                        	                 AND    DATE(date)>='$deal_penalty_start'
                                        	                 AND    DATE(date)< '$deal_penalty_end'"));

        $gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];

        if ($check_penalty[penalty_stoped]==1) {
            $penalty=$check_penalty[penalty];
        }else{
            if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$check_penalty[penalty_days]) {
                $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$gadacilebuli_day_count,2);
            }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$check_penalty[penalty_days] && $check_penalty[penalty_additional_percent] > 0){
                $penalty = round((($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[penalty_days])+($remaining_root * ($check_penalty[penalty_additional_percent]/100))*($gadacilebuli_day_count-$check_penalty[penalty_days]),2);
            }elseif($gadacilebuli_day_count>0 && $check_penalty[penalty_additional_percent] <= 0){
                $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$gadacilebuli_day_count,2);
            }
        }
    
        $data = array('penalty' => $penalty, 'gadacilebuli_day_count' => $gadacilebuli_day_count);
        break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		$agr_id	= $_REQUEST['agr_id'];
		 
		$rResult = mysql_query("SELECT client_loan_schedule_deal.id,
		                               client_loan_schedule_deal.datetime,
                                       CONCAT(client.`name`,' ',client.`lastname`),
                                       CONCAT('სხ ',IFNULL(client_loan_agreement.agreement_id,client_loan_agreement.oris_code)),
                                       client_loan_agreement.oris_code,
                                       client_loan_schedule_deal.deal_amount,
                                       client_loan_schedule_deal.deal_end_date,
                            		    CASE
                            		        WHEN client_loan_schedule_deal.deal_status = 0 THEN 'პირველადი'
                                		    WHEN client_loan_schedule_deal.deal_status = 1 THEN 'მიმდინარე'
                                		    WHEN client_loan_schedule_deal.deal_status = 2 THEN 'დასრულებული'
                            		    END AS `status`
                                FROM   client_loan_schedule_deal
                                JOIN   client_loan_schedule ON client_loan_schedule.id = client_loan_schedule_deal.schedule_id
                                JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN   client ON client.id = client_loan_agreement.client_id
                                WHERE  client_loan_schedule_deal.actived = 1");

		$data = array("aaData"	=> array());

		while ( $aRow = mysql_fetch_array( $rResult ) )
		{
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ )
			{
				/* General output */
				$row[] = $aRow[$i];
				if($i == ($count - 1)){
				    $row[] = '<div class="callapp_checkbox">
                                  <input type="checkbox" id="callapp_checkbox_'.$aRow[$hidden].'" name="check_'.$aRow[$hidden].'" value="'.$aRow[$hidden].'" class="check" />
                                  <label for="callapp_checkbox_'.$aRow[$hidden].'"></label>
                              </div>';
				}
			}
			$data['aaData'][] = $row;
		}

		break;
		
		case 'get_shedule':
		    $id	                  = $_REQUEST['id'];
		    $agr_id               = $_REQUEST['agr_id'];
		    $status               = $_REQUEST['status'];
		    $transaction_date     = $_REQUEST['transaction_date'];
		    $received_currency_id = $_REQUEST['received_currency_id'];
		    
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
                                                    	             DATEDIFF('$transaction_date',client_loan_schedule.schedule_date) AS datediff,
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
                                                	                 AND    DATE(date)< '$transaction_date'"));
	
	            $gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];
	
	            if ($check_penalty[penalty_stoped]==1) {
	                $penalty=$check_penalty[penalty];
	            }else{
	                if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$check_penalty[penalty_days]) {
                        $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$gadacilebuli_day_count,2);
                    }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$check_penalty[penalty_days] && $check_penalty[penalty_additional_percent] > 0){
                        $penalty = round((($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[penalty_days])+($remaining_root * ($check_penalty[penalty_additional_percent]/100))*($gadacilebuli_day_count-$check_penalty[penalty_days]),2);
                    }elseif($gadacilebuli_day_count>0 && $check_penalty[penalty_additional_percent] <= 0){
                        $penalty = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$gadacilebuli_day_count,2);
                    }
	            }
	            
	            $res_sch_id = mysql_fetch_assoc(mysql_query("   SELECT    client_loan_schedule.id
	
                                            	                FROM 	 `client_loan_schedule`
                                            	                LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            	                JOIN      client ON client.id = client_loan_agreement.client_id
                                            	                WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1
                                            	                ORDER BY  pay_date ASC
                                            	                LIMIT 1"));
	
	            mysql_query("UPDATE `client_loan_schedule`
            	                SET `penalty` = '$penalty'
            	             WHERE  `id`      = '$res_sch_id[id]'");
	
	
	            $res = mysql_fetch_assoc(mysql_query("SELECT 	client_loan_schedule.id,
                                            	                client_loan_schedule.pay_amount,
                                            	                client_loan_schedule.root,
                                            	                client_loan_schedule.percent,
                                            	                client_loan_schedule.penalty,
                                            	                client_loan_agreement.pledge_fee,
                                            	                client_loan_agreement.agreement_id AS agree_id,
                                            	                client_loan_agreement.loan_currency_id,
                                            	                client_loan_agreement.id AS agrement_id,
                                            	                client_loan_agreement.loan_amount,
                                            	                client_loan_schedule.schedule_date,
                                            	                client.id AS client_id
                                    	              FROM     `client_loan_schedule`
                                    	              LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    	              JOIN      client ON client.id = client_loan_agreement.client_id
                                    	              WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1
                                    	              ORDER BY  pay_date ASC
                                    	              LIMIT 1"));
	
	            $res1 = mysql_fetch_assoc(mysql_query("SELECT   IFNULL(ROUND(SUM(
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
	            
	            $pay_position = mysql_num_rows(mysql_query("SELECT 	  client_loan_schedule.id
                                        	                FROM     `client_loan_schedule`
                                        	                LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        	                JOIN      client ON client.id = client_loan_agreement.client_id
                                        	                WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.id > $res[id] AND client_loan_schedule.`status` != 1
                                        	                "));
	            
	           $data = array('status' => 1, 'schedule_date'=>$res[schedule_date], 'id' => $res[id],'pay_amount' => $res[root] + $res[percent] + $penalty, 'root' => $res[root], 'percent' => $res[percent], 'penalty' => $penalty, 'client_data' => client($res[client_id]), 'agrement_data' => client_loan_number($res[agrement_id]), 'currenc' => currency($res[loan_currency_id]), 'avans' => $res1['pay_amount']);
	           
		    
		
		    break;
	case 'save_deal':
		$id 		          = $_REQUEST['id'];
		$hidde_schedule_id    = $_REQUEST['hidde_schedule_id'];
		$payed_date           = $_REQUEST['payed_date'];
		$payed_amount         = $_REQUEST['payed_amount'];
		$received_currency_id = $_REQUEST['received_currency_id'];
		$cource               = $_REQUEST['cource'];
		$loan_payed_date      = $_REQUEST['loan_payed_date'];
		$deal_penalty_start   = $_REQUEST['deal_penalty_start'];
		$deal_penalty_end     = $_REQUEST['deal_penalty_end'];
		$penalty_day_count    = $_REQUEST['penalty_day_count'];
		$deal_amount          = $_REQUEST['deal_amount'];
		$deal_end             = $_REQUEST['deal_end'];
		$root1                = $_REQUEST['root1'];
		$pescent1             = $_REQUEST['pescent1'];
		$penalty1             = $_REQUEST['penalty1'];
		$unda_daericxos       = $_REQUEST['unda_daericxos'];
		$deals_penalty        = $_REQUEST['deals_penalty'];
		$user_id              = $_SESSION['USERID'];
		
		if($id==''){
		    mysql_query("INSERT INTO `client_loan_schedule_deal` 
                					(`user_id`, `datetime`, `schedule_id`, `pay_date`, `pay_amount`, `curence_id`, `cource`, `loan_valute_amount`, `penalty_start`, `penalty_end`, `penalty_day_count`, `deal_amount`, `deal_end_date`, `cur_percent`, `cur_root`, `cur_penalty`, `unda_daericxos`, `deal_status`, `actived`) 
                			  VALUES 
                					('$user_id', NOW(), '$hidde_schedule_id', '$payed_date', '$payed_amount', '$received_currency_id', '$cource', '$loan_payed_date', '$deal_penalty_start', '$deal_penalty_end', '$penalty_day_count', '$deal_amount', '$deal_end', '$pescent1', '$root1', '$penalty1', '$unda_daericxos', 0, 1)");
		}else{
		   mysql_query("UPDATE client_loan_schedule_deal 
		                   SET deals_penalty = $deals_penalty
		                WHERE  id = $id");
		}	    
		
		break;
	case 'done_deal':
	    $id 	 = $_REQUEST['id'];
	    $user_id = $_SESSION['USERID'];
	    
	    mysql_query("UPDATE `client_loan_schedule_deal`
                        SET `user_id`     = '$user_id',
                            `deal_status` = '2'
                     WHERE  `id`          = '$id'");
	    
	
	    break;
	case 'disable':
		$id	= $_REQUEST['id'];
		Disable($id);

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

function Disable($id){

    mysql_query("UPDATE client_loan_schedule
                    SET actived = 0
                 WHERE  id IN($id)
                ");
    return $res;
}

function client($id, $tr_det_id){
    $where = '';
    if ($tr_det_id == '') {
        $where = 'AND    client_loan_agreement.canceled_status = 0';
    }
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

function GetSchedule($id){
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT client_loan_schedule_deal.id,
                                                   client_loan_schedule_deal.pay_date,
                                                   client_loan_schedule_deal.deal_amount,
                                                   client_loan_schedule_deal.curence_id,
                                                   client_loan_schedule_deal.cource,
                                                   client_loan_schedule_deal.loan_valute_amount,
                                                   client_loan_agreement.loan_currency_id,
                                                   client_loan_agreement.client_id,
                                                   client_loan_agreement.id AS agr_id,
                                                   client_loan_schedule.root,
                                                   client_loan_schedule.percent,
                                                   client_loan_schedule.penalty,
	                                               client_loan_schedule.root + client_loan_schedule.percent + client_loan_schedule.penalty as all_fee,
                                                   client_loan_schedule_deal.penalty_start,
                                                   client_loan_schedule_deal.penalty_end,
                                                   client_loan_schedule_deal.penalty_day_count,
                                                   client_loan_schedule_deal.deal_amount,
                                                   client_loan_schedule_deal.deal_end_date,
                                                   client_loan_schedule_deal.cur_root,
                                                   client_loan_schedule_deal.cur_percent,
                                                   client_loan_schedule_deal.cur_penalty,
	                                               client_loan_schedule_deal.unda_daericxos,
	                                               client_loan_schedule_deal.deals_penalty
                                            FROM   client_loan_schedule_deal
                                            JOIN   client_loan_schedule ON client_loan_schedule.id = client_loan_schedule_deal.schedule_id
                                            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE  client_loan_schedule_deal.id = $id"));
    return $res;
}


function GetPage($res = ''){
    
    if ($res[id]=='') {
        $dis='disabled="disabled"';
        $dis1='';
    }else{
        $dis='';
        $dis1='disabled="disabled"';
    }
    
	$data = '<div id="dialog-form">
        	    
               <fieldset>
    				<legend>ჩარიცხვის შესახებ</legend>
	                <table class="dialog-form-table">
	                    <tr>
        					<td style="width: 160px;"><label for="date">გადახდის თარიღი</label></td>
	                        <td style="width: 125px;"><label for="date">ჩარიცხული თანხა</label></td>
	                        <td style="width: 125px;"><label for="date">ვალუტა</label></td>
	                        <td style="width: 125px;"><label for="date">კურსი</label></td>
                    	    <td style="width: 125px;"><label for="date">ჩარიცხული თანხა სესხის ვალუტაში</label></td>
                    	    <td style="width: 125px;"><label for="date">სესხის ვალუტა</label></td>
	                    </tr>
	                    <tr>
	                        <td style="width: 160px;">
        						<input style="width: 140px;" id="payed_date" type="text" value="'.$res[pay_date].'" '.$dis1.'>
        					</td>
        	                <td style="width: 125px;">
    						    <input style="width: 110px;" id="payed_amount" type="text" value="'.$res[deal_amount].'" '.$dis1.'>
        					</td>
    						<td style="width: 130px;">
        						<select id="received_currency_id" calss="label" style="width: 120px;" '.$dis1.'>'.currency($res[curence_id]).'</select>
        					</td>
        					<td style="width: 125px;">
        						 <input style="width: 110px;" id="cource" type="text" value="'.$res[cource].'" disabled="disabled">
        					</td>
        					<td style="width: 155px;">
        						<input style="width: 140px;" id="loan_payed_date" type="text" value="'.$res[loan_valute_amount].'" disabled="disabled">
        					</td>	     
        					<td style="width: 130px;">
        						<select id="loan_currency_id" calss="label" style="width: 120px;" disabled="disabled">'.currency($res[loan_currency_id]).'</select>
        					</td>	     
        	            </tr>
        				<tr style="height:10px;"></tr>
        			</table>
        						    
        			<table class="dialog-form-table">
        				<tr>
        					
	                        <td style="width: 470px;"><label for="date">მსესხებელი</label></td>
	                        <td style="width: 250px;"><label for="date">სესხის ნომერი</label></td>
	                    </tr>
	                    <tr>
	                        <td style="width: 470px;">
        						<select id="client_id" calss="label" style="width: 450px;" '.$dis1.'>'.client($res[client_id], $res[id]).'</select>
        					</td>
        					<td style="width: 250px;">
        						<select id="client_loan_number" calss="label" style="width: 250px;" '.$dis1.'>'.client_loan_number($res[agr_id], $res[id]).'</select>
        					</td>
        	            </tr>
        				<tr style="height:10px;"></tr>
        			</table>
    			</fieldset>
    			<fieldset>
        			<legend>მიმდინარე გადასახადი</legend>
        			<table class="dialog-form-table">
        				<tr>
        					<td style="width: 120px;"><label for="date">სულ გადასახდელი თანხა</label></td>
	                        <td style="width: 120px;"><label for="date">ძირი</label></td>
						    <td style="width: 120px;"><label for="date">პროცენტი</label></td>
						    <td style="width: 120px;"><label for="date">ჯარიმა</label></td>
        					<td style="width: 120px;"><label for="date">არსებული ბალანსი</label></td>
	                    </tr>
	                    <tr>
	                        <td style="width: 150px;">
        						<input style="width: 130px;" id="all_fee" type="text" value="'.$res[all_fee].'" disabled="disabled">
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="root" type="text" value="'.$res[root].'" disabled="disabled">
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="pescent" type="text" value="'.$res[percent].'" disabled="disabled">
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="penalty" type="text" value="'.$res[penalty].'" disabled="disabled">
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="surplus" type="text" value="'.$res[''].'" disabled="disabled">
        					</td>
        					<td style="width: 130px;">
        						<label style="width: 130px;" id="daricxvis_tarigi" type="text" '.$dis1.'></label>
        					</td>
        	            </tr>
        				<tr style="height:10px;"></tr>
        			</table>
    			</fieldset>
        		<fieldset>
    				<legend>შეთანხმების პირობები</legend>
        	    	<table class="dialog-form-table">
	                    <tr>
        					<td style="width: 160px;" colspan="2" style="width: 140px;"><label for="date">ჯარიმის პერიოდი</label></td>
	                        <td style="width: 100px;" style="width: 140px;"><label for="date">დღეების რაოდ.</label></td>
	                        <td style="width: 150px;" style="width: 140px;"><label for="date">ჯარიმის თანხა</label></td>
	                        <td style="width: 100px;" style="width: 140px;"><label for="date">შეთანხმ. თანხა</label></td>
	                        <td style="width: 100px;" style="width: 140px;"><label for="date">შეთანხმ. დასრულება</label></td>
	                    </tr>
	                    <tr>
        	                <td style="width: 160px;">
        						<input style="width: 140px;" id="deal_penalty_start" type="text" value="'.$res[penalty_start].'" '.$dis1.'>
        					</td>
        					<td style="width: 160px;">
        						<input style="width: 140px;" id="deal_penalty_end" type="text" value="'.$res[penalty_end].'" '.$dis1.'>
        					</td>
        					<td style="width: 100px;">
        						<input style="width: 80px;" id="penalty_day_count" type="text" value="'.$res[penalty_day_count].'" disabled="disabled">
        					</td>
        				    <td style="width: 150px;">
        						<input style="width: 80px;" id="penalty_amount" type="text" value="'.$res[penalty].'" disabled="disabled">
        					</td>
        					<td style="width: 100px;">
        						<input style="width: 80px;" id="deal_amount" type="text" value="'.$res[deal_amount].'" '.$dis1.'>
        					</td>
        					<td style="width: 160px;">
        						<input style="width: 140px;" id="deal_end" type="text" value="'.$res[deal_end_date].'" '.$dis1.'>
        					</td>
        	            </tr>
        				<tr style="height:20px;"></tr>
        			</table>
    			</fieldset>
    			<fieldset>
    				<legend>შეთანხმების საფუძველზე მიმდინარე გადასახადი</legend>
        			<table class="dialog-form-table">
        				<tr>
        					<td style="width: 150px;"><label for="date">ძირი</label></td>
						    <td style="width: 150px;"><label for="date">პროცენტი</label></td>
						    <td style="width: 150px;"><label for="date">ჯარიმა</label></td>
        					<td style="width: 300px;"><label for="date">ბარათზე დარიცხული ჯარიმა</label></td>
        					<td style="width: 300px;"><label for="date">შეთანხმების დარღვევის თანხა</label></td>
	                    </tr>
	                    <tr>
	                        <td style="width: 150px;">
        						<input style="width: 130px;" id="root1" type="text" value="'.$res[cur_root].'" '.$dis1.'>
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="pescent1" type="text" value="'.$res[cur_percent].'" '.$dis1.'>
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="penalty1" type="text" value="'.$res[cur_penalty].'" '.$dis1.'>
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="unda_daericxos" type="text" value="'.$res[unda_daericxos].'" disabled="disabled">
        					</td>
        					<td style="width: 150px;">
        						<input style="width: 130px;" id="deals_penalty" type="text" value="'.$res[deals_penalty].'" '.$dis.'>
        					</td>
        	            </tr>
        			</table>
    			</fieldset>
    			<!-- ID -->
    			<input type="hidden" id="hidde_id" value="' . $res['id'] . '" />
    			<input type="hidden" id="hidde_schedule_id" value="' . $res['id'] . '" />
    		</div>';
	return $data;
}

?>
