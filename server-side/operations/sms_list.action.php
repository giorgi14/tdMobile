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
		    $filt=" AND sent_list.status = 0";
		}else{
		    $filt="AND sent_list.status = 1";
		}
		 
        $rResult = mysql_query(" SELECT    sent_list.id,
                    					   sent_list.datetime,
                    					   CONCAT(CASE 
                                					  WHEN sent_list.client_id > 0 AND sent_list.guarantor_id = 0 AND sent_list.person_id = 0 AND sent_list.trust_person_id = 0 THEN concat(client.`name`, ' ', client.lastname)
                                					  WHEN sent_list.guarantor_id > 0 THEN CONCAT(client.`name`, ' ', client.lastname, '/თავ.პ./',IFNULL(client_quarantors.`name`,' '))
                                					  WHEN sent_list.person_id > 0 THEN CONCAT(client.`name`, ' ', client.lastname, '/საკ.პ./',IFNULL(client_person.`person`,' '))
                                					  WHEN sent_list.trust_person_id > 0 THEN CONCAT(client.`name`, ' ', client.lastname, '/მინდ.პ./',IFNULL(client_trusted_person.`name`,' '))
                                				  END,'/', IFNULL(client_car.registration_number, ''), '/', IFNULL(client_loan_agreement.agreement_id, '')) AS `name`,
                        					sent_list.`address`,
                        					sent_list.content,
                                            IF(sent_list.`status` = 0, 'გასაგზავნი', 'გაგზავნილი')
                                  FROM      sent_list
                                  LEFT JOIN client ON sent_list.client_id = client.id
                                  LEFT JOIN client_car ON client.id = client_car.client_id
                                  LEFT JOIN client_loan_agreement ON client.id = client_loan_agreement.client_id
                                  LEFT JOIN client_quarantors ON client_quarantors.id = sent_list.guarantor_id
                                  LEFT JOIN client_person ON client_person.id = sent_list.person_id
                                  LEFT JOIN client_trusted_person ON client_trusted_person.id = sent_list.trust_person_id
                                  WHERE     sent_list.actived = 1 $filt");

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
        
	case 'save_sms':
	    $id	       = $_REQUEST['id'];
	    $sms_phone = $_REQUEST['sms_phone'];
	    $sms_text  = $_REQUEST['sms_text'];
	    
	    if ($id == '') {
	        Add($sms_phone,$sms_text);
	    }else{
	        Save($id, $sms_phone, $sms_text);
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

function Add($sms_phone, $sms_text){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("INSERT INTO `sent_list` 
					        (`user_id`, `datetime`, `address`, `content`) 
		              VALUES 
					        ('$user_id', NOW(), '$sms_phone', '$sms_text')");
}

function Save($id, $sms_phone, $sms_text){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("UPDATE `sent_list`
                    SET `user_id`   = '$user_id',
	                    `datetime`  =  NOW(),
	                    `address`   = '$sms_phone',
                        `content`   = '$sms_text'
                  WHERE `id`        = '$id'");
}

function Disable($id){
	mysql_query("UPDATE `sent_list`
					SET `actived` = 0
				 WHERE  `id`      = '$id'");
}


function Get($id){
	$res = mysql_fetch_assoc(mysql_query("SELECT  sent_list.id,
	                                              sent_list.client_id,
                                    			  sent_list.`address`,
	                                              sent_list.`status`,
	                                              sent_list.`content`
                                          FROM    sent_list
										  WHERE   sent_list.`id` = '$id'" ));

	return $res;
}

function GetPage($res = ''){
    
    if ($res[status] == 1){$diss='disabled="disabled"';}else{$diss = '';}
    
    $data = '<div id="dialog-form">
        	    <fieldset>
        	    	<table class="dialog-form-table" style="width: 100%;">
                        <tr>
                            <td style="width: 210px;">
                             <input placeholder="შეიყვანეთ ნომერი" onkeypress="{if (event.which != 8 &amp;&amp; event.which != 0 &amp;&amp; event.which!=46 &amp;&amp; (event.which < 48 || event.which > 57)) {$(\'#errmsg\').html(\'მხოლოდ ციფრი\').show().fadeOut(\'slow\'); return false;}}" type="text" id="sms_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="'.$res[address].'" '.$diss.'>   
                            </td>
                            <td style="width: 335px;">
                               
                            </td>
        					<td style="width: 170px;">
        						
        					</td>
        				</tr>
        			    <tr style="height:5px"></tr>
        				<tr>
                            <td colspan="3">
        					   <textarea maxlength="350" placeholder="შეიყვანეთ ტექსტი" class="idle" id="sms_text" style="resize: vertical;width: 99%;height: 85px;" '.$diss.'>'.$res['content'].'</textarea>
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

?>
