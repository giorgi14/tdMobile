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
		 
		$rResult = mysql_query("SELECT   `default`.id,
		                                  loan_type.`name` AS loan_type_name,
                    					  agreement_type.`name` AS agreement_type_name,
                    					 `default`.percent,
                    					 `default`.loan_fee,
                    					 `default`.proceed_fee,
                    					 `default`.proceed_percent,
                    					 `default`.rs_message_number,
                    					 `default`.penalty_days,
                    					 `default`.penalty_percent,
                    					 `default`.penalty_additional_percent
                                FROM     `default`
                                LEFT JOIN agreement_type ON agreement_type.id = `default`.agreement_type_id 
                                LEFT JOIN loan_type ON loan_type.id = `default`.loan_type_id 
                                WHERE    `default`.actived = 1 ");

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
	case 'save_default':
		$id 		                = $_REQUEST['id'];
		$loan_agreement_type        = $_REQUEST['loan_agreement_type'];
		$agreement_type_id          = $_REQUEST['agreement_type_id'];
		$month_percent              = $_REQUEST['month_percent'];
		$loan_fee                   = $_REQUEST['loan_fee'];
		$proceed_percent            = $_REQUEST['proceed_percent'];
		$proceed_fee                = $_REQUEST['proceed_fee'];
		$rs_message_number          = $_REQUEST['rs_message_number'];
		$penalty_days               = $_REQUEST['penalty_days'];
		$penalty_percent            = $_REQUEST['penalty_percent'];
		$penalty_additional_percent = $_REQUEST['penalty_additional_percent'];

		$res = mysql_query("SELECT    default.id,
		                              agreement_type.`name` AS agreement_type_name,
			                          loan_type.`name` AS loan_type_name
                		    FROM     `default`
		                    LEFT JOIN agreement_type ON agreement_type.id = `default`.agreement_type_id 
                            LEFT JOIN loan_type ON loan_type.id = `default`.loan_type_id 
                		    WHERE    `default`.actived = 1 
		                    AND      `default`.loan_type_id = '$loan_agreement_type'
                		    AND      `default`.agreement_type_id = '$agreement_type_id'");
		$check=mysql_fetch_array($res);
		if($agreement_type_id != 0 && $loan_agreement_type != 0){
		    if ($id == '') {
                if(mysql_num_rows($res) == 0){
                    Adddefault($loan_agreement_type, $agreement_type_id, $month_percent,$loan_fee, $proceed_fee, $proceed_percent, $rs_message_number, $penalty_days, $penalty_percent, $penalty_additional_percent);
                }else{
                    $error = 'ეს არის უკვე სიაში';
                }
			}else {
			    if (mysql_num_rows($res) == 0 || $check[id] == $id ) {
			        Savedefault($id, $loan_agreement_type, $agreement_type_id, $month_percent, $loan_fee, $proceed_fee, $proceed_percent, $rs_message_number, $penalty_days, $penalty_percent, $penalty_additional_percent);
			    }else{
			        $error = 'ეს არის უკვე სიაში';
			    }
				
			}
		}
		
		break;
	case 'disable':
		$id	= $_REQUEST['id'];
		DisableHolidays($id);

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

function Adddefault($loan_agreement_type, $agreement_type_id, $month_percent,$loan_fee, $proceed_fee, $proceed_percent, $rs_message_number, $penalty_days, $penalty_percent, $penalty_additional_percent){
    
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO `default` 
                            (`user_id`, `datetime`, `loan_type_id`, `agreement_type_id`, `percent`, `loan_fee`, `proceed_fee`, `proceed_percent`, `rs_message_number`, `penalty_days`, `penalty_percent`, `penalty_additional_percent`) 
                      VALUES 
                            ('$user_id', NOW(), '$loan_agreement_type', '$agreement_type_id', '$month_percent', '$loan_fee', '$proceed_fee', '$proceed_percent', '$rs_message_number', '$penalty_days', '$penalty_percent', '$penalty_additional_percent')");
}

function Savedefault($id, $loan_agreement_type, $agreement_type_id, $month_percent, $loan_fee, $proceed_fee, $proceed_percent, $rs_message_number, $penalty_days, $penalty_percent, $penalty_additional_percent){
    
	$user_id	= $_SESSION['USERID'];
	
	mysql_query("UPDATE `default`
                    SET `user_id`                    = '$user_id',
	                    `datetime`                   =  NOW(),
	                    `loan_type_id`               = '$loan_agreement_type',
                        `agreement_type_id`          = '$agreement_type_id',
                        `percent`                    = '$month_percent',
                        `loan_fee`                   = '$loan_fee',
                        `proceed_fee`                = '$proceed_fee',
                        `proceed_percent`            = '$proceed_percent',
                        `rs_message_number`          = '$rs_message_number',
                        `penalty_days`               = '$penalty_days',
                        `penalty_percent`            = '$penalty_percent',
                        `penalty_additional_percent` = '$penalty_additional_percent'
                 WHERE  `id`                         = '$id'");
}

function DisableHolidays($id)
{
	mysql_query("	UPDATE `default`
					SET    `actived` = 0
					WHERE  `id` = $id");
}

function CheckHolidaysExist($name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `holidays`
											WHERE  `name` = '$name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}

function CheckHolidaysDateExist($date)
{
    $res = mysql_fetch_assoc(mysql_query("	SELECT id
                                            FROM   `holidays`
                                            WHERE  DATE(date) = '$date' AND `actived` = 1"));
    if($res['id'] != ''){
        return true;
    }
    return false;
}

function loan_type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   loan_type");

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

function agreement_type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   agreement_type");

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
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT   `default`.id,
                                					 `default`.`agreement_type_id` AS agreement_type_id,
                                					 `default`.`loan_type_id` AS `loan_type_id`,
                                				     `default`.percent,
                                					 `default`.loan_fee,
                                					 `default`.proceed_fee,
                                					 `default`.proceed_percent,
                                					 `default`.rs_message_number,
                                					 `default`.penalty_days,
                                					 `default`.penalty_percent,
                                					 `default`.penalty_additional_percent
                                            FROM     `default`
                                            WHERE    `default`.`id` = $id" ));
    return $res;
}


function GetPage($res = ''){
    
    if ($res[loan_type_id] == 2) {
        $input_hidde = "display:none;";
    }else{
        $input_hidde = "display:block;";
    }
    
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>

	    	<table class="dialog-form-table">
				<tr style="height:15px">
					<td style="width: 190px;"><label for="name">სესხის ტიპი</label></td>
	                <td style="width: 190px;"><label for="date">ხელშეკრულების ტიპი</label></td>
            	    <td style="width: 190px;"><label for="date">ყოველთვიური პროცენტი</label></td>
            	    <td style="width: 190px;"><label for="date">სესხის გაცემის საკომისიო</label></td>
				</tr>
				<tr style="height:15px">
					<td style="width: 190px;">
						<select id="loan_agreement_type" style="width: 175px;">'.loan_type($res[loan_type_id]).'</select>
					</td>
					<td style="width: 190px;">
						<select id="agreement_type_id" style="width: 175px;">'.agreement_type($res[agreement_type_id]).'</select>
					</td>
				    <td style="width: 190px;">
						<input style="width: 170px;" id="month_percent" type="text" value="'.$res[percent].'">
					</td>
					<td style="width: 190px;">
						<input style="width: 170px;" id="loan_fee" type="text" value="'.$res[loan_fee].'">
					</td>
				</tr>
				<tr style="height:18px;"></tr>
				<tr style="heght:15px">
					<td style="width: 190px;"><label style="'.$input_hidde.'" class="label_label" for="name">ხელშკრ. გაგრძ. საფასური</label></td>
	                <td style="width: 190px;"><label style="'.$input_hidde.'" class="label_label" for="date">პროცენტი</label></td>
            	    <td style="width: 190px;"><label for="date">შემოსავ. სამსახ. შეტყობ. N</label></td>
            	    <td style="width: 190px;"><label for="date">ვადაგადაცილებული დღეები</label></td>
				</tr>
				<tr style="height:15px">
					<td style="width: 190px;">
						<input style="width: 170px; '.$input_hidde.'" id="proceed_fee" type="text" value="'.$res[proceed_fee].'">
					</td>
					<td style="width: 190px;">
						<input style="width: 170px; '.$input_hidde.'" id="proceed_percent" type="text" value="'.$res[proceed_percent].'">
					</td>
				    <td style="width: 190px;">
						<input style="width: 170px;" id="rs_message_number" type="text" value="'.$res[rs_message_number].'">
					</td>
					<td style="width: 190px;">
						<input style="width: 170px;" id="penalty_days" type="text" value="'.$res[penalty_days].'">
					</td>
				</tr>
				<tr style="height:18px;"></tr>
				<tr style="height:15px">
					<td style="width: 190px;"><label for="name">ვადაგადაც. პირგასამტეხლო%</label></td>
	                <td colspan="3" style="width: 190px;"><label for="date">ვადაგადაც. პირგასამტეხლო%</label></td>
				</tr>
				<tr style="height:15px">
					<td style="width: 190px;">
						<input style="width: 170px;" id="penalty_percent" type="text" value="'.$res[penalty_percent].'">
					</td>
					<td style="width: 190px;">
						<input style="width: 170px;" id="penalty_additional_percent" type="text" value="'.$res[penalty_additional_percent].'">
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
