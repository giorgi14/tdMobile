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
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		$agr_id	= $_REQUEST['agr_id'];
		 
		$rResult = mysql_query("SELECT id,
                                       number,
                                       schedule_date, 
                                       pay_amount,
                                       root,
                                       percent,
		                               penalty,
                                       other_amount,
                                       ROUND(remaining_root+root,2),
		                               CASE
		                                   WHEN penalty_stoped=1 THEN 'ჯარიმა შეჩერებული'
		                                   WHEN status = 0 THEN 'ჩვეულებრივი'
		                                   WHEN status = 2 THEN 'შეთანხმება'
		                               END AS `status`
                                FROM   client_loan_schedule
                                WHERE  actived = 1 AND client_loan_agreement_id = $agr_id 
                                AND   `status` IN(0,2) AND activ_status IN(0,2)");

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
	case 'save_schedule':
		$id 		           = $_REQUEST['id'];
		$filt_agr_id 		   = $_REQUEST['filt_agr_id'];
		$schedule_number       = $_REQUEST['schedule_number'];
		$schedule_date         = $_REQUEST['schedule_date'];
		$schedule_amount       = $_REQUEST['schedule_amount'];
		$schedule_root         = $_REQUEST['schedule_root'];
		$schedule_percent      = $_REQUEST['schedule_percent'];
		$schedule_penalty      = $_REQUEST['schedule_penalty'];
		$schedule_other_amount = $_REQUEST['schedule_other_amount'];
		$penalty_stoped        = $_REQUEST['penalty_stoped'];
		

		if($id==''){
		    Add($filt_agr_id, $schedule_date, $schedule_number, $schedule_other_amount);
		}else{
		    Save($id, $schedule_number, $schedule_date, $schedule_amount, $schedule_root, $schedule_percent, $schedule_penalty, $schedule_other_amount, $penalty_stoped);
		}	    
		
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

function Add($filt_agr_id, $schedule_date, $schedule_number, $schedule_other_amount){
    
	$user_id = $_SESSION['USERID'];
	
	mysql_query("INSERT INTO `client_loan_schedule` 
					        (`user_id`, `datetime`, `client_loan_agreement_id`, `schedule_date`,  `number`,`other_amount`, `activ_status`, `status`) 
		              VALUES 
					        ('$user_id', NOW(), '$filt_agr_id', '$schedule_date', '$schedule_number', '$schedule_other_amount',  '2', '2')");
}

function Save($id, $schedule_number, $schedule_date, $schedule_amount, $schedule_root, $schedule_percent, $schedule_penalty, $schedule_other_amount, $penalty_stoped){
    
	mysql_query("UPDATE  `client_loan_schedule`
                    SET  `number`         = '$schedule_number',
            			 `schedule_date`  = '$schedule_date',
            			 `pay_date`       = '$schedule_date',
            			 `root`           = '$schedule_root',
            			 `percent`        = '$schedule_percent',
            			 `penalty`        = '$schedule_penalty',
            			 `other_amount`   = '$schedule_other_amount',
            			 `pay_amount`     = '$schedule_amount',
	                     `penalty_stoped` = '$penalty_stoped'
                  WHERE  `id`               = '$id'");
}

function Disable($id){

    mysql_query("UPDATE client_loan_schedule
                    SET actived = 0
                 WHERE  id IN($id)
                ");
    return $res;
}

function GetSchedule($id){
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT id,
                                                   number,
                                                   schedule_date, 
                                                   pay_amount,
                                                   root,
                                                   percent,
            		                               penalty,
                                                   other_amount,
	                                               penalty_stoped,
	                                               status
                                            FROM   client_loan_schedule
                                            WHERE  id = $id"));
    return $res;
}


function GetPage($res = ''){
    $checked = "";
    $display = '';
    if ($res[penalty_stoped] == 1) {
        $checked = "checked";
    }
    
    if ($res[id] == '') {
        $display = 'display:none';
    }else{
        if ($res[status] == 2) {
            $display = 'display:none';
        }
    }
    
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table">
				<tr style="heght:15px">
					<td style="width: 150px;"><label for="date">ნომერი</label></td>
	                <td style="width: 100px;">
						<input style="width: 90px;" id="schedule_number" type="text" value="'.$res[number].'">
					</td>
	            </tr>
			    <tr style="height:10px;"></tr>
				<tr style="heght:15px">
            	    <td style="width: 150px;"><label for="date">თარიღი</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_date" type="text" value="'.$res[schedule_date].'">
					</td>
			    </tr>
				<tr style="height:10px; '.$display.'"></tr>
			    <tr style="heght:15px; '.$display.'">
					<td style="width: 150px;"><label for="name">ანუიტეტი</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_amount" type="text" value="'.$res[pay_amount].'">
					</td>
			    </tr>
				<tr style="height:10px; '.$display.'"></tr>
			    <tr style="heght:15px; '.$display.'">
	                <td style="width: 150px;"><label for="name">ძირი</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_root" type="text" value="'.$res[root].'">
					</td>
			    </tr>
				<tr style="height:10px; '.$display.'"></tr>
			    <tr style="heght:15px; '.$display.'">
	                <td style="width: 150px;"><label for="name">პროცენტი</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_percent" type="text" value="'.$res[percent].'">
					</td>
			    </tr>
				<tr style="height:10px; '.$display.'"></tr>
				<tr style="heght:15px; '.$display.'">
	                <td style="width: 150px;"><label for="name">ჯარიმა</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_penalty" type="text" value="'.$res[penalty].'">
					</td>
			    </tr>
				<tr style="height:10px;"></tr>
			    <tr style="heght:15px">
	                <td style="width: 150px;"><label for="date">შეთანხმების თანხა</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_other_amount" type="text" value="'.$res[other_amount].'">
					</td>
            	</tr>
				<tr style="height:10px; '.$display.'"></tr>
				<tr style="heght:15px; '.$display.'">
	                <td style="width: 150px;"><label for="date">ჯარიმის შეჩერება</label></td>
					<td style="width: 100px;">
						<input style="width: 15px;" id="penalty_stoped" type="checkbox" '.$checked.' value="1">
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
