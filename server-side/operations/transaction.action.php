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
	    $page		= GetPage(GetHolidays($id));
        $data		= array('page'	=> $page);

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
		    $val = 'money_transactions.pay_root,
                    money_transactions.pay_percent,
                    money_transactions.diff';
		}else{
		    $val = 'transaction_type.`name`';
		    if ($tab==0) {
		        $where_status = 'AND money_transactions.type_id = 0';
		    }
		    
		}
		
		$rResult = mysql_query("SELECT  money_transactions.id,
                                    	money_transactions.datetime,
                                    	client.`name`,
                                    	money_transactions.pay_amount,
                                        loan_currency.name,
		                                money_transactions.course,
                                        $val
                                FROM  `money_transactions`
                                JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
		                        LEFT JOIN   loan_currency ON loan_currency.id = money_transactions.currency_id
		                        JOIN transaction_type ON transaction_type.id = money_transactions.type_id
                                JOIN   client ON client.id = client_loan_agreement.client_id 
		                        WHERE money_transactions.type_id != 4 $where_status $where");

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
		$id 		 = $_REQUEST['id'];
		$month_fee   = $_REQUEST['month_fee'];
		$root        = $_REQUEST['root'];
		$percent     = $_REQUEST['percent'];
		$penalti_fee = $_REQUEST['penalti_fee'];
		$surplus     = $_REQUEST['surplus'];
		$diff        = $_REQUEST['diff'];
		$type_id     = $_REQUEST['type_id'];
		$currency_id = $_REQUEST['currency_id'];
		$course      = $_REQUEST['course'];
		
		$hidde_id    = $_REQUEST['hidde_id'];
		

		
	    if ($id == '') {
	        Add($hidde_id, $month_fee, $course, $currency_id, $root,  $percent, $penalti_fee, $surplus, $diff, $type_id);
        }
		
		break;
		
	case 'get_shedule':
		$id	     = $_REQUEST['id'];
		$type_id = $_REQUEST['type_id'];
		$agr_id  = $_REQUEST['agr_id'];
		$status  = $_REQUEST['status'];
		
		if ($status == 1) {
		    $filt = "AND client_loan_agreement.client_id = $id";
		}elseif ($status == 2){
		    $filt = "AND client_loan_agreement.id = $agr_id";
		}else{
		    $filt = "AND client_loan_agreement.client_id = $id";
		}
		
// 		$res = mysql_fetch_assoc(mysql_query("SELECT client_loan_schedule.id,
//                                                 	 client_loan_schedule.pay_amount,
//                                                      client_loan_schedule.root,
//                                                 	 client_loan_schedule.percent,
//                                                 	 client_loan_agreement.insurance_fee,
//                                                 	 client_loan_agreement.pledge_fee,
//                                                 	 client_loan_agreement.loan_currency_id,
//                                                 	 client_loan_agreement.id AS agrement_id,
//                                                 	 client.id AS client_id,
//                                                      CASE 
// 			                                             WHEN DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>0 AND DATEDIFF(CURDATE(), client_loan_schedule.pay_date) <= client_loan_agreement.penalty_days 
// 		                                                 THEN   ROUND(((SELECT client_loan_schedule.remaining_root
//             															FROM 			`client_loan_schedule`
//             															LEFT JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
//             															JOIN  		 client ON client.id = client_loan_agreement.client_id
//             															WHERE      client_loan_schedule.actived = 1 AND (client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id OR client_loan_agreement.client_id = client.id) AND client_loan_schedule.`status` = 1
//             															ORDER BY   pay_date DESC
//             															LIMIT 1)*(client_loan_agreement.penalty_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
// 			                                             WHEN DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>client_loan_agreement.penalty_days 
// 		                                                 THEN ROUND((( SELECT client_loan_schedule.remaining_root
//             															FROM 			`client_loan_schedule`
//             															LEFT JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
//             															JOIN  		 client ON client.id = client_loan_agreement.client_id
//             															WHERE      client_loan_schedule.actived = 1 AND (client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id OR client_loan_agreement.client_id = client.id) AND client_loan_schedule.`status` = 1
//             															ORDER BY   pay_date DESC
//             															LIMIT 1)*(client_loan_agreement.penalty_additional_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
// 	                                                   END AS penalty
//                                                  FROM `client_loan_schedule`
//                                                  LEFT JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
//                                                  JOIN  client ON client.id = client_loan_agreement.client_id
//                                                  WHERE client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1
//                                                  ORDER BY pay_date ASC
//                                                  LIMIT 1"));
		
		$res = mysql_fetch_assoc(mysql_query("  SELECT 	    client_loan_schedule.id,
                                        					client_loan_schedule.pay_amount,
                                        					client_loan_schedule.root,
                                        					client_loan_schedule.percent,
                                        					client_loan_agreement.insurance_fee,
                                        					client_loan_agreement.pledge_fee,
                                        					client_loan_agreement.loan_currency_id,
                                        					client_loan_agreement.id AS agrement_id,
		                                                    client_loan_agreement.loan_amount,
                                        					client.id AS client_id,
                                        				    DATEDIFF(CURDATE(), client_loan_schedule.pay_date) AS gadacilebuli,
                                        				   (SELECT     client_loan_schedule.remaining_root
                                    						FROM 		`client_loan_schedule`
                                    						LEFT JOIN  client_loan_agreement AS agr ON agr.id = client_loan_schedule.client_loan_agreement_id
                                    						JOIN  		client ON client.id = agr.client_id
                                    					    WHERE      client_loan_schedule.actived = 1 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND client_loan_schedule.`status` = 1
                                    						ORDER BY   pay_date DESC
                                    						LIMIT 1) AS remaining_root,
                                        					client_loan_agreement.penalty_days,
                                        					client_loan_agreement.penalty_percent,
                                        					client_loan_agreement.penalty_additional_percent
                                                 FROM 	   `client_loan_schedule`
                                                 LEFT JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                 JOIN       client ON client.id = client_loan_agreement.client_id
                                                 WHERE      client_loan_schedule.actived = 1 $filt AND client_loan_schedule.`status` != 1
                                                 ORDER BY   pay_date ASC
                                                 LIMIT 1"));
		
		$res1 = mysql_fetch_assoc(mysql_query("SELECT SUM(pay_amount) AS pay_amount,
                                                	  SUM(pay_root) AS pay_root,
                                                	  SUM(pay_percent) AS pay_percent,
                                                	  SUM(pay_penalty) AS pay_penalty
                                               FROM   money_transactions
                                               WHERE  money_transactions.client_loan_schedule_id = $res[id] AND money_transactions.status in(1, 3)"));
		if ($res[remaining_root]==0) {
		    $remainig_root = $res[loan_amount];
		}else{
		    $remainig_root = $res[remaining_root];
		}
		
		if ($res[gadacilebuli]>0 && $res[gadacilebuli]<=$res[penalty_days]) {
		    $penalty = round(($remainig_root * ($res[penalty_percent]/100))*$res[gadacilebuli],2);
		}elseif ($res[gadacilebuli]>0 && $res[gadacilebuli]>$res[penalty_days]){
		    $penalty = round((($remainig_root * ($res[penalty_percent]/100))*$res[penalty_days])+($remainig_root * ($res[penalty_additional_percent]/100))*($res[gadacilebuli]-$res[penalty_days]),2);
		}
		
		
		if ($type_id == 1 || $type_id == 0) {	
    		$data = array('status' => 1, 'id' => $res[id],'pay_amount' => $res[pay_amount] + $penalty, 'root' => $res[root], 'percent' => $res[percent], 'penalty' => $penalty, 'client_data' => client($res[client_id]), 'agrement_data' => client_loan_number($res[agrement_id]), 'currenc' => currency($res[loan_currency_id]),'pay_amount1' => $res1[pay_amount], 'root1' => $res1[pay_root], 'percent1' => $res1[pay_percent], 'penalty1' => $res1[pay_penalty]);
		}elseif ($type_id == 2){
		    $data = array('status' => 2, 'id' => $res[id],'insurance_fee' => $res[insurance_fee]);
		}elseif ($type_id == 3){
		    $data = array('status' => 3, 'id' => $res[id],'pledge_fee' => $res[pledge_fee]);
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

function Add($hidde_id, $month_fee, $course, $currency_id, $root,  $percent, $penalti_fee, $surplus, $diff, $type_id){
    
	$user_id	= $_SESSION['USERID'];
	
	$res = mysql_fetch_assoc(mysql_query("SELECT  ROUND(SUM(pay_amount),2) AS pay_amount
                                          FROM    money_transactions
                                	      WHERE   money_transactions.client_loan_schedule_id = $hidde_id 
	                                      AND     money_transactions.status = 3 AND actived = 1"));
	
	$res1 = mysql_fetch_assoc(mysql_query("SELECT  client_loan_schedule.pay_amount,
                                            	   CASE
	                                                   WHEN client_loan_agreement.loan_type_id = 1 AND DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>0 THEN ROUND(client_loan_schedule.percent/(DAY(LAST_DAY(CURDATE())))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
                                                	   WHEN client_loan_agreement.loan_type_id = 2 AND DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>0 AND DATEDIFF(CURDATE(), client_loan_schedule.pay_date) < client_loan_agreement.penalty_days THEN ROUND((client_loan_schedule.remaining_root*(client_loan_agreement.penalty_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
                                                	   WHEN client_loan_agreement.loan_type_id = 2 AND DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>client_loan_agreement.penalty_days THEN ROUND((client_loan_schedule.remaining_root*(client_loan_agreement.penalty_additional_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
                                            	   END AS penalty
                                    	   FROM   `client_loan_schedule`
	                                       JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    	   WHERE   client_loan_schedule.id = $hidde_id AND client_loan_schedule.actived = 1"));
	
	$all_pay = ROUND($month_fee + $res[pay_amount],2);
	$all_fee = ROUND($res1[pay_amount] + $res1[penalty],2);
	
	if ($all_fee == $all_pay){
	    if ($penalti_fee>0){
	        mysql_query("INSERT INTO `money_transactions`
	                                (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `pay_root`, `pay_percent`, `pay_penalty`, `diff`, `type_id`, `status`, `actived`)
	                          VALUES
	                                (NOW(), '$user_id', '$hidde_id', curdate(), '', '$course', '$currency_id', '', '$penalti_fee', '$penalti_fee', '', '$type_id', '2', '1');");
	    }
	    
	    mysql_query("UPDATE  `money_transactions`
	                    SET  `actived` = '0'
	                  WHERE  `client_loan_schedule_id`='$hidde_id' AND status = 3");
	    
	    mysql_query("INSERT INTO `money_transactions`
                    	        (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `pay_root`, `pay_percent`, `pay_penalty`, `diff`, `type_id`, `status`, `actived`)
                    	  VALUES
                    	        (NOW(), '$user_id', '$hidde_id', curdate(), '$all_pay', '$course', '$currency_id', '$root', '$percent', '', '$diff', '$type_id', '1','1');");
	    
	    mysql_query("UPDATE  `client_loan_schedule`
            	        SET  `status` = '1'
            	      WHERE  `id`     = '$hidde_id'");
	    
	}elseif ($all_fee < $all_pay){
	    $delta = round($all_pay - $all_fee,2);
	    
	    if ($penalti_fee>0){
	        mysql_query("INSERT INTO `money_transactions`
                    	            (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `pay_root`, `pay_percent`, `pay_penalty`, `diff`, `type_id`, `status`, `actived`)
                    	      VALUES
                    	            (NOW(), '$user_id', '$hidde_id', curdate(), '', '$course', '$currency_id', '', '$penalti_fee', '$penalti_fee', '', '$type_id', '2', '1');");
	    }
	    
	    mysql_query("INSERT INTO `money_transactions`
                    	        (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `pay_root`, `pay_percent`, `pay_penalty`, `diff`, `type_id`, `status`, `actived`)
                    	  VALUES
                    	        (NOW(), '$user_id', '$hidde_id', curdate(), '$all_pay', '$course', '$currency_id', '$root', '$percent', '', '$diff', '$type_id', '1','1');");
	    
	    mysql_query("UPDATE  `client_loan_schedule`
        	            SET  `status` = '1'
        	         WHERE   `id`     = '$hidde_id'");
	    
	    mysql_query("UPDATE  `money_transactions`
	                    SET  `actived` = '0'
	                  WHERE  `client_loan_schedule_id`='$hidde_id' AND status = 3");
	    $hidde_id = $hidde_id+1;
	    mysql_query("INSERT INTO `money_transactions`
                	            (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `pay_root`, `pay_percent`, `pay_penalty`, `diff`, `type_id`, `status`, `actived`)
                	      VALUES
                	            (NOW(), '$user_id', '$hidde_id', curdate(), '$delta', '$course', '$currency_id', '', '', '', '', '$type_id', '3','1');");
	}else{
	    mysql_query("INSERT INTO `money_transactions`
                	            (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `pay_root`, `pay_percent`, `pay_penalty`, `diff`, `type_id`, `status`, `actived`)
                	      VALUES
                	            (NOW(), '$user_id', '$hidde_id', curdate(), '$month_fee', '$course', '$currency_id', '', '', '', '', '$type_id', '3','1');");
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
    $req = mysql_query("SELECT   cl.id,
			                     IF (cl.attachment_id=0, concat(cl.`name`, ' ', cl.lastname), concat(cl.`name`, ' ', cl.lastname, '/დანართი N', client_loan_agreement.attachment_number)) AS `name`
                        FROM     client AS cl
                        JOIN     client_loan_agreement ON cl.id = client_loan_agreement.client_id
                        WHERE    cl.actived=1 AND client_loan_agreement.`status`=1 AND client_loan_agreement.canceled_status=0
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
    $req = mysql_query("SELECT client_loan_agreement.id, 
                        	   IF (client.attachment_id=0, concat(client_loan_agreement.id), concat((SELECT client_loan_agreement.id FROM client_loan_agreement WHERE client_loan_agreement.client_id = client.attachment_id), '/დანართი N', client_loan_agreement.attachment_number)) AS `name`
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
                                    			   money_transactions.pay_root,
                                    			   money_transactions.pay_percent,
	                                               money_transactions.pay_penalty,
	                                               money_transactions.type_id,
	                                               money_transactions.course,
	                                               money_transactions.currency_id,
	                                               money_transactions.client_loan_schedule_id,
	                                               money_transactions.status
                                            FROM  `money_transactions`
                                            JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE  money_transactions.id = $id" ));
    return $res;
}


function GetPage($res = ''){
    
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
    }else{
        $cource = $res[course];
    }
    $res1= mysql_fetch_assoc(mysql_query("SELECT client_loan_schedule.id,
                                                 client_loan_schedule.pay_amount,
                                                 client_loan_schedule.root,
                                                 client_loan_schedule.percent,
                                                 CASE
                                                    WHEN DATEDIFF(money_transactions.pay_datetime, client_loan_schedule.pay_date)>0 AND DATEDIFF(money_transactions.pay_datetime, client_loan_schedule.pay_date) <= client_loan_agreement.penalty_days THEN ROUND((client_loan_agreement.loan_amount*(client_loan_agreement.penalty_percent/100))*(DATEDIFF(money_transactions.pay_datetime, client_loan_schedule.pay_date)),2)
                                                    WHEN DATEDIFF(money_transactions.pay_datetime, client_loan_schedule.pay_date)>client_loan_agreement.penalty_days THEN ROUND((client_loan_agreement.loan_amount*(client_loan_agreement.penalty_additional_percent/100))*(DATEDIFF(money_transactions.pay_datetime, client_loan_schedule.pay_date)),2)
                                                 END AS penalty
                                           FROM `client_loan_schedule`
                                           JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                           WHERE client_loan_schedule.id = $res[client_loan_schedule_id] "));
    
    $res2 = mysql_fetch_assoc(mysql_query("SELECT   SUM(pay_amount) AS pay_amount,
                                                    SUM(pay_root) AS pay_root,
                                                    SUM(pay_percent) AS pay_percent,
                                                    SUM(pay_penalty) AS pay_penalty
                                            FROM    money_transactions
                                            WHERE   money_transactions.id = $res[id]"));
    
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table">
	            <table>
    				<tr>
    	                <td style="width: 30px;"><label calss="label" style="padding-top: 5px;" for="name">ტიპი</label></td>
    					<td style="width: 196px;">
    						<select id="type_id"  calss="label" style="width: 175px;">'.type($res[type_id]).'</select>
    					</td>
    					<td style="width: 70px;"><label calss="label" style="padding-top: 5px;" for="date">მსესხებელი</label></td>
    					<td style="width: 190px;">
    						<select id="client_id" calss="label" style="width: 260px;">'.client($res[client_id]).'</select>
    					</td>
    					<td style="width: 105px;"><label calss="label" style="padding-top: 5px; margin-left: 19px;" for="date">სესხის ნომერი</label></td>
    					<td style="width: 120px;">
    						<select id="client_loan_number" calss="label" style="width: 170px;">'.client_loan_number($res[client_id]).'</select>
    					</td>
    				</tr>
    				<tr style="height:15px;"></tr>
    				<tr>
    	                <td style="width: 30px;"><label calss="label" style="padding-top: 5px;" for="name">ვალუტა</label></td>
    					<td style="width: 196px;">
    						<select id="currency_id"  calss="label" style="width: 175px;">'.currency($res[currency_id]).'</select>
    					</td>
    					<td style="width: 70px;"><label calss="label" style="padding-top: 5px;" for="date">კურსი</label></td>
    					<td style="width: 190px;">
    						<input style="width: 80px;" id="course" class="label" type="text" value="'.$cource.'" '.$disable.'>
    					</td>
    					<td style="width: 70px;"></td>
    					<td style="width: 120px;"></td>
    				</tr>
				</table>
    			<table>
    				<tr style="height:40px;"></tr>
    				<tr>
    					<td style="width: 110px;"><label style="padding-top: 5px;" class="label" for="date">ჩარიცხული თანხა:</label></td>
                	    <td style="width: 130px;">
    						<input style="width: 80px;" id="month_fee" class="label" type="text" value="'.$res['pay_amount'].'" '.$disable.'>
    					</td>
    					<td style="width: 140px;"><label style="padding-top: 5px;" class="label" for="name">სულ გადახდილი თანხა:</label></td>
    					<td style="width: 130px;">
    						<input style="width: 80px;" id="month_fee2" class="label" type="text" value="'.$res2['pay_amount'].'" disabled="disabled">
    					</td>
    					<td style="width: 125px;"><label style="padding-top: 5px;" class="label" for="name">სულ შესატანი თანხა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="month_fee1" class="label" type="text" value="'.$res1['pay_amount'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 110px;"><label class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 130px;">
    						<input style="width: 80px;" id="root" class="label_label" type="text" value="'.$res['pay_root'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 130px;">
    						<input style="width: 80px;" id="root2" class="label_label" type="text" value="'.$res2['pay_root'].'" disabled="disabled">
    					</td>
    					<td style="width: 125px;"><label style="padding-top: 5px;" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="root1" class="label_label" type="text" value="'.$res1['root'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 110px;"><label class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 130px;">
    						<input style="width: 80px;" id="percent" class="label_label" type="text" value="'.$res['pay_percent'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 130px;">
    						<input style="width: 80px;"  class="label_label" id="percent2" type="text" value="'.$res2['pay_percent'].'" disabled="disabled">
    					</td>
    					<td style="width: 125px;"><label style="padding-top: 5px;" class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;"  class="label_label" id="percent1" type="text" value="'.$res1['percent'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 110px;"><label class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 130px;">
    						<input class="label_label" style="width: 80px;" id="penalti_fee" type="text" value="'.$res['pay_penalty'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 130px;">
    						<input style="width: 80px;" id="penalti_fee2" class="label_label" type="text" value="'.$res2['pay_penalty'].'" disabled="disabled">
    					</td>
    					<td style="width: 125px;"><label style="padding-top: 5px;" class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 80px;">
    						<input style="width: 80px;" id="penalti_fee1" class="label_label" type="text" value="'.$res1['penalty'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 110px;"><label class="label_label" for="date">მეტობა</label></td>
    					<td style="width: 130px;">
    						<input class="label_label" style="width: 80px;" id="surplus" type="text" value="'.$res['pay_penalty'].'" '.$disable.'>
    					</td>
    					<td style="width: 120px;"></td>
    					<td style="width: 130px;"></td>
    					<td style="width: 125px;"></td>
    					<td style="width: 80px;"></td>
    				</tr>
				</table>
			</table>
			<!-- ID -->
			<input type="hidden" id="id" value="' . $res['id'] . '" />
			<input type="hidden" id="hidde_id" value="" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
