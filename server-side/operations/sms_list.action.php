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
		 
        $rResult = mysql_query(" SELECT     sms_list.id,
                    					    sms_list.datetime,
                        					client.name,
                        					sms_list.`phone`,
                        					sms_list.text,
                                            IF(sms_list.`status` = 0, 'გასაგზავნი', 'გაგზავნილი')
                                  FROM      sms_list
                                  LEFT JOIN client ON sms_list.client_id = client.id
                                  WHERE     sms_list.actived = 1 $filt");

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
	mysql_query("INSERT INTO `sms_list` 
					        (`redactor_user_id`, `datetime`, `phone`, `text`) 
		              VALUES 
					        ('$user_id', NOW(), '$sms_phone', '$sms_text')");
}

function Save($id, $sms_phone, $sms_text){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("UPDATE `sms_list`
                    SET `redactor_user_id` = '$user_id',
	                    `datetime`         =  NOW(),
	                    `client_id`        = '$client_id',
                        `phone`            = '$sms_phone',
                        `text`             = '$sms_text'
                  WHERE `id`               = '$id'");
}

function Disable($id){
	mysql_query("UPDATE `sms_list`
					SET `actived` = 0
				 WHERE  `id`      = '$id'");
}


function Get($id){
	$res = mysql_fetch_assoc(mysql_query("SELECT  sms_list.id,
                                    			  sms_list.`phone`,
	                                              sms_list.`status`,
	                                              sms_list.`text`
                                          FROM    sms_list
										  WHERE   sms_list.`id` = '$id'" ));

	return $res;
}

function GetPage($res = ''){
    
    if ($res[status] == 1){$diss='disabled="disabled"';}else{$diss = '';}
    
    $data = '<div id="dialog-form">
        	    <fieldset>
        	    	<table class="dialog-form-table" style="width: 100%;">
                        <tr>
                            <td style="width: 210px;">
                             <input placeholder="შეიყვანეთ ნომერი" onkeypress="{if (event.which != 8 &amp;&amp; event.which != 0 &amp;&amp; event.which!=46 &amp;&amp; (event.which < 48 || event.which > 57)) {$(\'#errmsg\').html(\'მხოლოდ ციფრი\').show().fadeOut(\'slow\'); return false;}}" type="text" id="sms_phone" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="'.$res[phone].'" '.$diss.'>   
                            </td>
                            <td style="width: 335px;">
                               
                            </td>
        					<td style="width: 170px;">
        						
        					</td>
        				</tr>
        			    <tr style="height:5px"></tr>
        				<tr>
                            <td colspan="3">
        					   <textarea maxlength="150" placeholder="შეიყვანეთ ტექსტი" class="idle" id="sms_text" style="resize: vertical;width: 99%;height: 85px;" '.$diss.'>'.$res['text'].'</textarea>
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
