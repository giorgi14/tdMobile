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
		                               IF(penalty_stoped=1, 'ჯარიმა შეჩერებული', 'ჩვეულებრივი')
                                FROM   client_loan_schedule
                                WHERE  actived = 1 AND client_loan_agreement_id = $agr_id 
                                AND   `status` = 0 AND activ_status = 0");

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
	case 'save_schedule':
		$id 		           = $_REQUEST['id'];
		$schedule_number       = $_REQUEST['schedule_number'];
		$schedule_date         = $_REQUEST['schedule_date'];
		$schedule_amount       = $_REQUEST['schedule_amount'];
		$schedule_root         = $_REQUEST['schedule_root'];
		$schedule_percent      = $_REQUEST['schedule_percent'];
		$schedule_penalty      = $_REQUEST['schedule_penalty'];
		$schedule_other_amount = $_REQUEST['schedule_other_amount'];
		$penalty_stoped        = $_REQUEST['penalty_stoped'];
		

		
		Savedefault($id, $schedule_number, $schedule_date, $schedule_amount, $schedule_root, $schedule_percent, $schedule_penalty, $schedule_other_amount, $penalty_stoped);
			    
		
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
	mysql_query("");
}

function Savedefault($id, $schedule_number, $schedule_date, $schedule_amount, $schedule_root, $schedule_percent, $schedule_penalty, $schedule_other_amount, $penalty_stoped){
    
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


function GetSchedule($id){
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT id,
                                                   number,
                                                   schedule_date, 
                                                   pay_amount,
                                                   root,
                                                   percent,
            		                               penalty,
                                                   other_amount,
	                                               penalty_stoped
                                            FROM   client_loan_schedule
                                            WHERE  id = $id"));
    return $res;
}


function GetPage($res = ''){
    $checked = "";
    if ($res[penalty_stoped] == 1) {
        $checked = "checked";
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
				<tr style="height:10px;"></tr>
			    <tr style="heght:15px">
					<td style="width: 150px;"><label for="name">ანუიტეტი</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_amount" type="text" value="'.$res[pay_amount].'">
					</td>
			    </tr>
				<tr style="height:10px;"></tr>
			    <tr style="heght:15px">
	                <td style="width: 150px;"><label for="name">ძირი</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_root" type="text" value="'.$res[root].'">
					</td>
			    </tr>
				<tr style="height:10px;"></tr>
			    <tr style="heght:15px">
	                <td style="width: 150px;"><label for="name">პროცენტი</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_percent" type="text" value="'.$res[percent].'">
					</td>
			    </tr>
				<tr style="height:10px;"></tr>
				<tr style="heght:15px">
	                <td style="width: 150px;"><label for="name">ჯარიმა</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_penalty" type="text" value="'.$res[penalty].'">
					</td>
			    </tr>
				<tr style="height:10px;"></tr>
			    <tr style="heght:15px">
	                <td style="width: 150px;"><label for="date">დამატებითი თანხა</label></td>
					<td style="width: 100px;">
						<input style="width: 90px;" id="schedule_other_amount" type="text" value="'.$res[other_amount].'">
					</td>
            	</tr>
				<tr style="height:10px;"></tr>
				<tr style="heght:15px">
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
