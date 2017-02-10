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
		 
		$rResult = mysql_query("SELECT  money_transactions.id,
                                    	money_transactions.datetime,
                                    	client.`name`,
                                    	client_loan_schedule.pay_amount,
                                        money_transactions.course,
                                        loan_currency.name,
                                		money_transactions.pay_root,
                                        money_transactions.pay_percent,
                                        money_transactions.diff
                                FROM  `money_transactions`
                                JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
		                        LEFT JOIN   loan_currency ON loan_currency.id = money_transactions.currency_id
                                JOIN   client ON client.id = client_loan_agreement.client_id $where");

		$data = array("aaData"	=> array());

		while ( $aRow = mysql_fetch_array( $rResult ) )
		{
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
		$diff        = $_REQUEST['diff'];
		$type_id     = $_REQUEST['type_id'];
		$currency_id = $_REQUEST['currency_id'];
		$course      = $_REQUEST['course'];
		
		$hidde_id    = $_REQUEST['hidde_id'];
		

		
	    if ($id == '') {
            Add($hidde_id, $month_fee, $course, $currency_id, $root,  $percent, $penalti_fee, $diff, $type_id);
        }else{
            Save($id,$month_fee, $course, $currency_id, $root,  $percent, $penalti_fee, $diff);
        }
		
		break;
		
	case 'get_shedule':
		$id	     = $_REQUEST['id'];
		$type_id = $_REQUEST['type_id'];
		
		
    		$res = mysql_fetch_assoc(mysql_query("SELECT client_loan_schedule.id,
    		                                             client_loan_schedule.pay_amount,
                                                	     client_loan_schedule.root,
                                                		 client_loan_schedule.percent,
                                            		     client_loan_agreement.insurance_fee,
                                            		     client_loan_agreement.pledge_fee,
                                            			 CASE 
                                            				  WHEN DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>0 AND DATEDIFF(CURDATE(), client_loan_schedule.pay_date) < client_loan_agreement.penalty_days THEN ROUND((client_loan_agreement.loan_amount*(client_loan_agreement.penalty_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
                                            				  WHEN DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>client_loan_agreement.penalty_days THEN ROUND((client_loan_agreement.loan_amount*(client_loan_agreement.penalty_additional_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
                                            			 END AS penalty
                                                   FROM `client_loan_schedule`
                                                   JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                   JOIN  client ON client.id = client_loan_agreement.client_id
                                                   WHERE client.id = $id AND client_loan_schedule.actived = 1 AND client_loan_schedule.`status` != 1
                                                   ORDER BY pay_date ASC
                                                   LIMIT 1"));
    	if ($type_id == 1 || $type_id == 0) {	
    		$data = array('status' => 1, 'id' => $res[id],'pay_amount' => $res[pay_amount], 'root' => $res[root], 'percent' => $res[percent], 'penalty' => $res[penalty]);
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

function Add($hidde_id, $month_fee, $course, $currency_id, $root,  $percent, $penalti_fee, $diff, $type_id){
    
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO `money_transactions` 
                            (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `pay_root`, `pay_percent`, `pay_penalty`, `diff`, `type_id`, `status`, `actived`) 
		              VALUES 
						      (NOW(), '$user_id', '$hidde_id', curdate(), '$month_fee', '$course', '$currency_id', '$root', '$percent', '$penalti_fee', '$diff', '$type_id', '1','1');");
}

function Save($id,$month_fee, $course, $currency_id, $root,  $percent, $penalti_fee, $diff){
    
	$user_id	= $_SESSION['USERID'];
	
	mysql_query("UPDATE `tdmobile`.`money_transactions`
                    SET `datetime` = NOW(),
                    	 `user_id` = '$user_id',
                    	 `pay_amount` = '$month_fee',
	                     `course`     = '$course',
	                     `currency_id`= '$currency_id',
                    	 `pay_root` = '$root',
                    	 `pay_percent` = '$percent',
                    	 `pay_penalty` = '$penalti_fee',
                    	 `diff` = '$diff',
                    	 `status` = '1'
                   WHERE `id` = '$id'");
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
    $req = mysql_query("SELECT id,
                               concat(client.`name`,' ',lastname) AS `name`
                        FROM   client");

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
    
    $res1= mysql_fetch_assoc(mysql_query("SELECT client_loan_schedule.id,
                                                 client_loan_schedule.pay_amount,
                                                 client_loan_schedule.root,
                                                 client_loan_schedule.percent,
                                                 CASE
                                                    WHEN DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>0 AND DATEDIFF(CURDATE(), client_loan_schedule.pay_date) < client_loan_agreement.penalty_days THEN ROUND((client_loan_agreement.loan_amount*(client_loan_agreement.penalty_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
                                                    WHEN DATEDIFF(CURDATE(), client_loan_schedule.pay_date)>client_loan_agreement.penalty_days THEN ROUND((client_loan_agreement.loan_amount*(client_loan_agreement.penalty_additional_percent/100))*(DATEDIFF(CURDATE(), client_loan_schedule.pay_date)),2)
                                                 END AS penalty
                                           FROM `client_loan_schedule`
                                           JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                           WHERE client_loan_schedule.id = $res[client_loan_schedule_id] "));
    
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
    						<select id="client_id" calss="label" style="width: 200px;">'.client($res[client_id]).'</select>
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
    						<input style="width: 80px;" id="course" class="label" type="text" value="'.$res['course'].' '.$disable.'">
    					</td>
    				</tr>
				</table>
    			<table>
    				<tr style="height:40px;"></tr>
    				<tr>
    					<td style="width: 110px;"><label style="padding-top: 5px;" class="label" for="date">ჩარიცხული თანხა:</label></td>
                	    <td style="width: 180px;">
    						<input style="width: 80px;" id="month_fee" class="label" type="text" value="'.$res['pay_amount'].' '.$disable.'">
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label" for="name">სულ შესატანი თანხა:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="month_fee1" class="label" type="text" value="'.$res1['pay_amount'].'" disabled="disabled">
    					</td>
    	            </tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 110px;"><label class="label_label" for="date">ძირი:</label></td>
    					<td style="width: 180px;">
    						<input style="width: 80px;" id="root" class="label_label" type="text" value="'.$res['pay_root'].' '.$disable.'">
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">ძირი თანხა:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="root1" class="label_label" type="text" value="'.$res1['root'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 110px;"><label class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 180px;">
    						<input style="width: 80px;" id="percent" class="label_label" type="text" value="'.$res['pay_percent'].' '.$disable.'">
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">პროცენტი:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;"  class="label_label" id="percent1" type="text" value="'.$res1['percent'].'" disabled="disabled">
    					</td>
    				</tr>
    				<tr style="height:10px;"></tr>
    				<tr style="'.$input_hidde.'">
    					<td style="width: 110px;"><label class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 180px;">
    						<input class="label_label" style="width: 80px;" id="penalti_fee" type="text" value="'.$res['pay_penalty'].' '.$disable.'">
    					</td>
    					<td style="width: 120px;"><label style="padding-top: 5px;" class="label_label" for="date">ჯარიმა:</label></td>
    					<td style="width: 100px;">
    						<input style="width: 80px;" id="penalti_fee1" class="label_label" type="text" value="'.$res1['penalty'].'" disabled="disabled">
    					</td>
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
