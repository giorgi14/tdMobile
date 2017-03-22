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
		 
		$rResult = mysql_query("SELECT  sent_sms.id,
                        				sent_sms.`address`,
		                                sent_sms.content,
		                                IF(sent_sms.status = 0, 'გასაგზავნი', 'გაგზავნილი')
                                FROM    sent_sms
                                WHERE   sent_sms.actived = 1");

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
		
	case 'get_client_number':
		$phone = Getphone();
		$data  = array('page' => $phone);
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

function Add($sms_phone,$sms_text){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("INSERT INTO `sent_sms` 
					        (`user_id`, `address`, `content`, `status`, `actived`) 
		              VALUES 
					        ('$user_id', '$sms_phone', '$sms_text', '0', '1')");
}

function Save($id, $sms_phone, $sms_text){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("UPDATE `sent_sms`
                    SET `user_id` = '$user_id',
                        `address` = '$sms_phone',
                        `content` = '$sms_text'
                 WHERE  `id`      = '$id'");
}

function Disable($id){
	mysql_query("UPDATE `sent_sms`
					SET `actived` = 0
				 WHERE  `id`      = '$id'");
}

function Get($id){
	$res = mysql_fetch_assoc(mysql_query("SELECT  sent_sms.id,
                                    			  sent_sms.`address`,
	                                              sent_sms.`content`
                                          FROM    sent_sms
										  WHERE   sent_sms.`id` = '$id'" ));

	return $res;
}

function GetPage($res = ''){
    
    $data = '
	<div id="dialog-form">
	    <fieldset>
	    	<table class="dialog-form-table" style="width: 100%;">
                <tr>
                    <td colspan="3">
					   <label style="color:red;" id="errmsg"></label>
					</td>
                </tr>
				<tr>
					<td style="width: 170px;">
						<input placeholder="შეიყვანეთ ნომერი" onkeypress="{if (event.which != 8 &amp;&amp; event.which != 0 &amp;&amp; event.which!=46 &amp;&amp; (event.which < 48 || event.which > 57)) {$(\'#errmsg\').html(\'მხოლოდ ციფრი\').show().fadeOut(\'slow\'); return false;}}" type="text" id="sms_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="'.$res[address].'">
					</td>
				    <td>
						<button id="get_number">ნომრები</button>
					</td>
                    <td>
						<button style="display:none;" id="get_shablons">შაბლონები</button>
					</td>
				</tr>
			    <tr style="height:5px"></tr>
				<tr>
                    <td colspan="3">
					   <textarea maxlength="150" placeholder="შეიყვანეთ ტექსტი" class="idle" id="sms_text" style="resize: vertical;width: 99%;height: 70px;">'.$res['content'].'</textarea>
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
        </fieldset>
    </div>
    ';
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
