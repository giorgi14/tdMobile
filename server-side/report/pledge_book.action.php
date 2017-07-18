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
	    $page		= GetPage(Get($id));
        $data		= array('page'	=> $page);

		break;
	case 'get_list' :
        $count	    = $_REQUEST['count'];
		$hidden	    = $_REQUEST['hidden'];
		$filt_month	= $_REQUEST['filt_month'];
		
		$filt_day	= $_REQUEST['filt_day'];
		$today      = date("Y-m");
		$c_day      = date("d");
		$AND        = '';
		
		if ($filt_day > 0) {
		    $AND = "AND DAY(client_loan_agreement.datetime) ='$filt_day'";
		}
		
		$rResult = mysql_query("SELECT 	MAX(car_insurance_info.id),
                        				DATE_FORMAT(MAX(car_insurance_info.car_insurance_end),'%d/%m/%Y') AS daricxvis_tarigi,
                        				CASE
                        					 WHEN client.id >= (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        					 WHEN client.id < (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client.exel_agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        				END AS `name`,
                        				client_loan_agreement.oris_code,
                        				CASE
											 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
											 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
											 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
											 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
                        			    END AS agreement_number,
                        				(SELECT car_insurance_info.ins_payy 
                        				 FROM   car_insurance_info AS ins_info
                        				 WHERE  ins_info.id = MAX(car_insurance_info.id)) AS percent_usd
                                FROM   	 client
                                JOIN   	 client_loan_agreement ON client.id = client_loan_agreement.client_id
                                JOIN   	 client_car ON client_car.client_id = client.id
                                JOIN     car_insurance_info ON car_insurance_info.client_id = client.id
                                WHERE  	 MONTH(car_insurance_info.car_insurance_end) = '$filt_month'
                                AND      YEAR(car_insurance_info.car_insurance_end) = YEAR(CURDATE()) $AND
                                GROUP BY client.id");

		$data = array("aaData" => array());

		while ( $aRow = mysql_fetch_array( $rResult ) ){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ ){
				$row[] = $aRow[$i];
			}
			$data['aaData'][] = $row;
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

function Get($id){
	$res = mysql_fetch_assoc(mysql_query("SELECT  sent_sms.id,
	                                              sent_sms.client_id,
                                    			  sent_sms.`address`,
	                                              sent_sms.`status`,
	                                              sent_sms.`content`
                                          FROM    sent_sms
										  WHERE   sent_sms.`id` = '$id'" ));

	return $res;
}

function get_client($id){
    
    $req = mysql_query("SELECT client.id,
                               client.`name`
                        FROM   client 
                        JOIN   client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE  client.actived = 1 AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1");
    $data .= '<option value="0" selected="selected">აირჩიე კლიენტი</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        }else{
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function get_phone($id, $phone){
    $req = mysql_query("SELECT client.phone,
                               CONCAT(client.`name`,'/',client.phone) AS name
                        FROM   client 
                        JOIN   client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE  client.actived = 1 AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1 AND client.id = '$id'
                        UNION ALL
                        SELECT client_person.phone,
                               CONCAT('საკონტ. პ./', client_person.person,'/', client_person.phone) AS name
                        FROM   client_person
                        JOIN   client ON client.id = client_person.client_id 
                        JOIN   client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE  client.actived = 1 AND client_person.actived = 1 AND client.id = '$id' AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1
                        UNION ALL
                        SELECT client_quarantors.phone,
                               CONCAT('თავდ. პ./', client_quarantors.`name`,'/', client_quarantors.phone) AS name
                        FROM   client_quarantors
                        JOIN   client ON client.id = client_quarantors.client_id 
                        JOIN   client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE  client.actived = 1 AND client_quarantors.actived = 1 AND client.id = '$id' AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1
                        UNION ALL
                        SELECT client_trusted_person.phone,
                               CONCAT('მინდობ. პ./', client_trusted_person.`name`,'/',client_trusted_person.phone) AS name
                        FROM   client_trusted_person
                        JOIN   client ON client.id = client_trusted_person.client_id 
                        JOIN client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE  client.actived = 1 AND client_trusted_person.actived = 1 AND client.id = '$id' AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1");

    $data .= '<option value="0" selected="selected">აირჩიე ნომერი</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['phone'] == $phone){
            $data .= '<option value="' . $res['phone'] . '" selected="selected">' . $res['name'] . '</option>';
        }else{
            $data .= '<option value="' . $res['phone'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function GetPage($res = ''){
    
    $data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table" style="width: 100%;">
                <tr>
                    <td>
					</td>
                    <td>
					</td>
                    <td>
					   <label style="color:red;" id="errmsg"></label>
					</td>
                </tr>
                <tr>
                    <td style="width: 210px;"><select class="idle" id="client_id" style="width: 205px;" disabled="disabled">'.get_client($res[client_id]).'</select></td>
                    <td style="width: 335px;"><select class="idle" id="client_phone" style="width: 335px;" disabled="disabled">'.get_phone($res[client_id], $res['address']).'</select></td>
					<td style="width: 170px;">
						<input placeholder="შეიყვანეთ ნომერი" onkeypress="{if (event.which != 8 &amp;&amp; event.which != 0 &amp;&amp; event.which!=46 &amp;&amp; (event.which < 48 || event.which > 57)) {$(\'#errmsg\').html(\'მხოლოდ ციფრი\').show().fadeOut(\'slow\'); return false;}}" type="text" id="sms_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="'.$res[address].'" disabled="disabled">
					</td>
				</tr>
			    <tr style="height:5px"></tr>
				<tr>
                    <td colspan="3">
					   <textarea maxlength="150" placeholder="შეიყვანეთ ტექსტი" class="idle" id="sms_text" style="resize: vertical;width: 99%;height: 85px;" disabled="disabled">'.$res['content'].'</textarea>
					</td>
                </tr>
				<tr>
                    <td colspan="3">
					   <label id="simbol_caunt"></label>
					</td>
                </tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="id" value="' . $res['id'] . '" />
			<input type="hidden" id="h_status" value="' . $res['status'] . '" />
        </fieldset>
    </div>
    ';
    return $data;
}

?>
