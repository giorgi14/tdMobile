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
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		$tab	= $_REQUEST['tab'];
		$where = '';
		if ($tab > 0) {
		    $where="AND money_transactions.type_id=$tab";
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
                                               WHEN client.id >= 302 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                                               WHEN client.id < 302 THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client.exel_agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                                           END AS `name`,
                                    	   money_transactions.pay_amount,
                                           loan_currency.name,
		                                   money_transactions.course,
		                                   $val
                                           IF(money_transactions.`status` = 0,'დაუდასტურებელი','დადასტურებული'),
		                                   user_info.`name`,
		                                   DATE_FORMAT(money_transactions.datetime,'%d/%m/%Y')
                                 FROM     `money_transactions`
                                 LEFT JOIN client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                 LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
		                         LEFT JOIN loan_currency ON loan_currency.id = money_transactions.currency_id
		                         LEFT JOIN transaction_type ON transaction_type.id = money_transactions.type_id
                                 LEFT JOIN client ON client.id = client_loan_agreement.client_id
		                         LEFT JOIN client_car ON client_car.client_id = client.id
		                         LEFT JOIN user_info ON user_info.user_id = money_transactions.user_id
		                         WHERE     money_transactions.type_id != 4 $where_status $where ");

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
                                                        AND      client_loan_schedule.schedule_date < CURDATE() AND DATEDIFF(CURDATE(),client_loan_schedule.pay_date)>=1
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
		
		$res = mysql_fetch_assoc(mysql_query("SELECT 	 client_loan_schedule.id,
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
                                                         AND     DATE(car_insurance_info.car_insurance_end) = CURDATE()) AS insurance_fee
                                               FROM 	`client_loan_schedule`
                                               LEFT JOIN client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                               JOIN      client ON client.id = client_loan_agreement.client_id
                                               WHERE     client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1
                                               ORDER BY  pay_date ASC
                                               LIMIT 1"));
		
		$res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                               FROM    money_transactions_detail
                                               JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                               JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                               JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                               WHERE   client_loan_agreement.client_id = '$res[client_id]' 
                                               AND     money_transactions_detail.`status` = 3
                                               AND     money_transactions_detail.actived = 1"));
		
		$month_fee_trasaction = $_REQUEST['month_fee_trasaction'];
		$receivedd_currency_id = $_REQUEST['received_currency_id'];
		$loan_cource_id       = $res[loan_currency_id];
		$course               = $_REQUEST['course'];
		
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
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT money_transactions.id,
                                    			   client_loan_agreement.`client_id`,
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
	                                               money_transactions.status
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
        </fieldset>
    </div>
    ';
	return $data;
}

?>
