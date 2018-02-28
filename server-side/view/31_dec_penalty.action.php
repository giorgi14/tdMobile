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
		$status	= $_REQUEST['status'];
		
		if ($status == 0) {
		    $rResult = mysql_query("SELECT   GROUP_CONCAT(client_loan_schedule.id) AS `id`,
                            		         IF(DATE_FORMAT(CURDATE(),'%m-%d')!='12-31',CONCAT(DATE_FORMAT(CURDATE(),'%Y')-1,'-12-31'),CURDATE()),
                            		         CASE
                                		         WHEN ISNULL(client.ltd_name) OR client.ltd_name = '' THEN CONCAT(IFNULL(client.`name`,''),' ',IFNULL(client.lastname, ''))
                                		         WHEN NOT ISNULL(client.ltd_name) AND client.ltd_name != '' THEN CONCAT('შპს ',client.ltd_name)
                            		         END AS `client`,
                            		         CASE
                                		         WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number),'',' დ.'), IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number), '', client_loan_agreement.attachment_number))
                                		         WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
                                		         WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
                                		         WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
                            		         END AS agreement_id,
                            		         client_loan_agreement.oris_code,
                            		         SUM(IF(client_loan_schedule.schedule_date<IF(DATE_FORMAT(CURDATE(),'%m-%d')!='12-31',CONCAT(DATE_FORMAT(CURDATE(),'%Y')-1,'-12-31'),CURDATE()),1,0)),
                            		         client_loan_schedule.schedule_date,
                            		         'დასარიცხი' AS `status`,
                    		                 concat('<div><button style=\"width: 100%; padding: 0px;\" class=\"show_letter\" loan_currency_id=\"',client_loan_agreement.loan_currency_id,'\" client_id=\"',client.id,'\">ბარათი</button>', '</div>')
                    		        FROM     client_loan_schedule
                    		        JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                    		        JOIN     client ON client.id = client_loan_agreement.client_id
                    		        WHERE    client_loan_schedule.actived = 1 AND client.actived = 1 AND client_loan_agreement.actived = 1
                    		        AND      DATE(client_loan_schedule.schedule_date) < IF(DATE_FORMAT(CURDATE(),'%m-%d')!='12-31',CONCAT(DATE_FORMAT(CURDATE(),'%Y')-1,'-12-31'),CURDATE())
                    		        AND      client_loan_schedule.`status` = 0 AND client_loan_schedule.activ_status = 0
                    		        AND      client_loan_agreement.canceled_status != 1 AND client_loan_agreement.status = 1 AND client_loan_schedule.31dec_status = 0
                    		        GROUP BY client_loan_agreement.id");
		}else{
		    $rResult = mysql_query("SELECT   GROUP_CONCAT(client_loan_schedule.id) AS `id`,
                        					 IF(DATE_FORMAT(CURDATE(),'%m-%d')!='12-31',CONCAT(DATE_FORMAT(CURDATE(),'%Y')-1,'-12-31'),CURDATE()),
                        					 CASE
                        						 WHEN ISNULL(client.ltd_name) OR client.ltd_name = '' THEN CONCAT(IFNULL(client.`name`,''),' ',IFNULL(client.lastname, ''))
                        						 WHEN NOT ISNULL(client.ltd_name) AND client.ltd_name != '' THEN CONCAT('შპს ',client.ltd_name)
                        					 END AS `client`,
                        					 CASE
                        						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number),'',' დ.'), IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number), '', client_loan_agreement.attachment_number))
                        						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
                        						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
                        						 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
                        					 END AS agreement_id,
                        					 client_loan_agreement.oris_code,
                        					 CONCAT(31_dec_penalty.penalty,' ', IF(client_loan_agreement.loan_currency_id=1,'GEL','$')),
                        				     client_loan_schedule.schedule_date,
                        				     'დარიცხული' AS `status`,
                        				     concat('<div><button style=\"width: 100%; padding: 0px;\" class=\"show_letter\" loan_currency_id=\"',client_loan_agreement.loan_currency_id,'\" client_id=\"',client.id,'\">ბარათი</button>', '</div>')
                                    FROM     31_dec_penalty
                                    JOIN     client_loan_schedule ON client_loan_schedule.id = 31_dec_penalty.sclient_loan_schedule_id
                                    JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN     client ON client.id = client_loan_agreement.client_id 
                                    WHERE    client_loan_schedule.actived = 1
                                    AND      client_loan_schedule.activ_status = 0 
                                    AND      client_loan_agreement.status = 1
                                    GROUP BY 31_dec_penalty.sclient_loan_schedule_id");
		}
		

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
	
	case 'disable':
		$id	    = $_REQUEST['id'];
		$status	= $_REQUEST['status'];
		if ($status == 1) {
		    $check_penalty = mysql_fetch_array(mysql_query("SELECT schedule_id
                                                		    FROM  31_dec_penalty
                                                		    WHERE sclient_loan_schedule_id = $id"));
		    
		    mysql_query("DELETE FROM 31_dec_penalty WHERE sclient_loan_schedule_id = $id");
		    mysql_query("UPDATE client_loan_schedule SET 31dec_status = 0 WHERE id IN($check_penalty[schedule_id])");
		}else{
    		$check_penalty = mysql_fetch_array(mysql_query("SELECT client_loan_schedule.id,
                                                                   client_loan_agreement.penalty_days,
                                                        		   client_loan_agreement.penalty_percent,
                                                        		   client_loan_agreement.grace_period_caunt,
                                                        		   client_loan_agreement.penalty_additional_percent,
                                                                   CONCAT(DATE_FORMAT(client_loan_schedule.schedule_date,'%Y'),'-12-31') AS `date`,
                                                		           MAX(client_loan_schedule.root + client_loan_schedule.remaining_root) AS remaining_root
                                                		    FROM   client_loan_schedule
                                                		    JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                		    JOIN   client ON client.id = client_loan_agreement.client_id
                                                		    WHERE  client_loan_schedule.`status` = 0 AND client_loan_schedule.activ_status = 0
                                                		    AND    client_loan_agreement.canceled_status != 1 AND client_loan_agreement.status = 1
                                                		    AND    client_loan_schedule.id IN($id)
                                                            LIMIT 1"));
    		
    		$res_penalty = mysql_query("SELECT   DATEDIFF(CONCAT(DATE_FORMAT(client_loan_schedule.schedule_date,'%Y'),'-12-31'),client_loan_schedule.schedule_date) AS datediff,
                            		             client_loan_schedule.schedule_date
                            		    FROM     client_loan_schedule
                            		    JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            		    JOIN     client ON client.id = client_loan_agreement.client_id
                            		    WHERE    client_loan_schedule.`status` = 0 AND client_loan_schedule.activ_status = 0
                            		    AND      client_loan_agreement.canceled_status != 1 AND client_loan_agreement.status = 1
                            		    AND      client_loan_schedule.id IN($id)");
    		
    		
    		$remaining_root = $check_penalty[remaining_root];
    		$penalty = 0;
    		
    		$i = 1;
    		
    		while ($row = mysql_fetch_array($res_penalty)) {
    		    
    		    $gadacilebuli_day_count = $row[datediff];
    		    
    		    $check_holliday_day = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS count
                                                    		         FROM   holidays
                                                    		         WHERE  actived = 1
                                                    		         AND    DATE(date)>='$row[schedule_date]'
                                                    		         AND    DATE(date)< '$check_penalty[date]'"));
    		    
    		    $gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];
    		    
    		    if ($gadacilebuli_day_count > 0 && $gadacilebuli_day_count <= $check_penalty[grace_period_caunt]){
    		        $penalty = 0;
    		    }else{
    		        $gadacilebuli_day_count=$gadacilebuli_day_count - $check_penalty[grace_period_caunt];
    		        
    		        
    		        if ($i == 1) {
    		            if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$check_penalty[penalty_days]) {
    		                $penalty1 = round(($remaining_root * ($check_penalty[penalty_percent]/100))*$gadacilebuli_day_count,2);
    		            }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$check_penalty[penalty_days] && $check_penalty[penalty_additional_percent] > 0.00){
    		                $penalty1 = round((($remaining_root * ($check_penalty[penalty_percent]/100))*$check_penalty[penalty_days])+(($remaining_root * ($check_penalty[penalty_additional_percent]/100))*($gadacilebuli_day_count-$check_penalty[penalty_days])),2);
    		            }
    		        }else{
    		            $penalty1 = round(($remaining_root * ($check_penalty[penalty_additional_percent]/100))*($gadacilebuli_day_count),2);
    		        }
    		    }
    		    
    		    $i++;
    		    $penalty += $penalty1;
    		}
    		
    		$user_id = $_SESSION['USERID'];
    		
    		if ($penalty>0) {
    		    
    		    $check = mysql_num_rows(mysql_query("SELECT * FROM 31_dec_penalty WHERE sclient_loan_schedule_id = '$check_penalty[id]' AND actived = 1"));
    		   
    		    if ($check == 0) {
        		    mysql_query("INSERT INTO `31_dec_penalty` 
                                			(`user_id`, `datetime`, `accrual_date`, `sclient_loan_schedule_id`, `schedule_id`, `penalty`) 
                                	  VALUES 
        		                            ('$user_id', NOW(), '$check_penalty[date]', '$check_penalty[id]', '$id', '$penalty')");
    		    }
    		    
    		    mysql_query("UPDATE `client_loan_schedule`
    		                    SET  31dec_status = 1
                             WHERE   id IN($id)");
            }
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
