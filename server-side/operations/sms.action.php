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
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		$status	= $_REQUEST['status'];
		
		if ($status == 0) {
		    $filt="";
		}elseif ($status == 1){
		    $filt=" AND status=0";
		}else{
		    $filt="AND status=1";
		}
		 
		$rResult = mysql_query("SELECT    sent_sms.id,
		                                  sent_sms.datetime,
                        				  client.`name`,
                        				  sent_sms.`address`,
                        				  sent_sms.content,
                        				  IF(sent_sms.status = 0, 'გასაგზავნი', 'გაგზავნილი')
                                FROM      sent_sms
                                LEFT JOIN client ON sent_sms.client_id = client.id
                                WHERE     sent_sms.actived = 1 $filt");

		$data = array("aaData" => array());

		while ( $aRow = mysql_fetch_array( $rResult ) ){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ ){
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
		
	case 'get_client_phone':
	    $client_id	= $_REQUEST['client_id'];
		$phone = get_phone($client_id, '');
		$data  = array('page' => $phone);
		break;
		
	case 'save_sms':
	    $id	       = $_REQUEST['id'];
	    $sms_phone = $_REQUEST['sms_phone'];
	    $sms_text  = $_REQUEST['sms_text'];
	    $client_id = $_REQUEST['client_id'];
	    
	    if ($id == '') {
	        Add($client_id,$sms_phone,$sms_text);
	    }else{
	        Save($id, $client_id,$sms_phone, $sms_text);
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

function Add($client_id, $sms_phone, $sms_text){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("INSERT INTO `sent_sms` 
					        (`user_id`, `datetime`, `client_id`, `address`, `content`, `status`, `actived`) 
		              VALUES 
					        ('$user_id', NOW(), '$client_id', '$sms_phone', '$sms_text', '0', '1')");
}

function Save($id, $client_id, $sms_phone, $sms_text){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("UPDATE `sent_sms`
                    SET `user_id`   = '$user_id',
	                    `datetime`  =  NOW(),
	                    `client_id` = '$client_id',
                        `address`   = '$sms_phone',
                        `content`   = '$sms_text'
                  WHERE `id`        = '$id'");
}

function Disable($id){
	mysql_query("UPDATE `sent_sms`
					SET `actived` = 0
				 WHERE  `id`      = '$id'");
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
    $req = mysql_query("SELECT  client.phone,
                                CONCAT(client.`name`,'/',client.phone) AS name
                        FROM    client
                        JOIN    client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE   client.actived = 1 AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1 AND client.id = '$id'
                        UNION ALL
                        SELECT  client_person.phone,
                                CONCAT('საკონტ. პ./', client_person.person,'/', client_person.phone) AS name
                        FROM    client_person
                        JOIN    client ON client.id = client_person.client_id
                        JOIN    client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE   client.actived = 1 AND client_person.actived = 1 AND client.id = '$id' AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1
                        UNION ALL
                        SELECT client_quarantors.phone,
                                CONCAT('თავდ. პ./', client_quarantors.`name`,'/', client_quarantors.phone) AS name
                        FROM    client_quarantors
                        JOIN    client ON client.id = client_quarantors.client_id
                        JOIN    client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE   client.actived = 1 AND client_quarantors.actived = 1 AND client.id = '$id' AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1
                        UNION ALL
                        SELECT  client_trusted_person.phone,
                                CONCAT('მინდობ. პ./', client_trusted_person.`name`,'/',client_trusted_person.phone) AS name
                        FROM    client_trusted_person
                        JOIN    client ON client.id = client_trusted_person.client_id
                        JOIN    client_loan_agreement ON client.id = client_loan_agreement.client_id
                        WHERE   client.actived = 1 AND client_trusted_person.actived = 1 AND client.id = '$id' AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 1");

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

function GetPage($res = ''){
    
    if ($res[status] == 1){$diss='disabled="disabled"';}else{$diss = '';}
    
    $data = '<div id="dialog-form">
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
                            <td style="width: 210px;">
                                <select class="idle" id="client_id" style="width: 205px;" '.$diss.'>'.get_client($res[client_id]).'</select>
                            </td>
                            <td style="width: 335px;">
                                <select class="idle" id="client_phone" style="width: 335px;" '.$diss.'>'.get_phone($res[client_id], $res['address']).'</select>
                            </td>
        					<td style="width: 170px;">
        						<input placeholder="შეიყვანეთ ნომერი" onkeypress="{if (event.which != 8 &amp;&amp; event.which != 0 &amp;&amp; event.which!=46 &amp;&amp; (event.which < 48 || event.which > 57)) {$(\'#errmsg\').html(\'მხოლოდ ციფრი\').show().fadeOut(\'slow\'); return false;}}" type="text" id="sms_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="'.$res[address].'" '.$diss.'>
        					</td>
        				</tr>
        			    <tr style="height:5px"></tr>
        				<tr>
                            <td colspan="3">
        					   <textarea maxlength="150" placeholder="შეიყვანეთ ტექსტი" class="idle" id="sms_text" style="resize: vertical;width: 99%;height: 85px;" '.$diss.'>'.$res['content'].'</textarea>
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
            </div>';
    return $data;
}

function Getphone(){
    $req = mysql_query("SELECT client.id,
                               CONCAT(client.`name`, ' ',client.lastname) AS `client`,
                        	   client.phone
                         FROM  client 
                         JOIN  client_loan_agreement ON client.id = client_loan_agreement.client_id
                         WHERE client.actived = 1 AND client_loan_agreement.`canceled_status` = 0");
    
    while ($row = mysql_fetch_array($req)) {
        $dat.='<tr style="width: 100%; border: solid 1px;">
					<td style="width: 50%; border-right: solid 1px;">'.$row[client].'</td>
					<td style="width: 30%; border-right: solid 1px;">'.$row[phone].'</td>
					<td style="width: 20%;"><button style="width: 65px;" phone="'.$row[phone].'" class="copy_number">არჩევა</button></td>
				</tr>';
    }
    $data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table" style="width: 100%;">
                <tr style="width: 100%; border: solid 1px;">
					<td style="width: 50%; border-right: solid 1px;">კლიენტი</td>
					<td style="width: 30%; border-right: solid 1px;">ნომერი</td>
					<td style="width: 20%;">ქმედება</td>
				</tr>
				'.$dat.'
			</table>
		</fieldset>
    </div>
    ';
    return $data;
}
?>
