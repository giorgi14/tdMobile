<?php
require_once('../../../includes/classes/core.php');

// Main Strings
$action  = $_REQUEST['act'];
$error   = '';
$data    = '';
$user_id = $_SESSION['USERID'];


$id		            = $_REQUEST['id'];
$guarantor_hidde    = $_REQUEST['guarantor_hidde'];

$local_id           = $_REQUEST['local_id'];
$guarantor_name	    = $_REQUEST['guarantor_name'];
$guarantor_pid      = $_REQUEST['guarantor_pid'];
$guarantor_address  = $_REQUEST['guarantor_address'];
$guarantor_mail     = $_REQUEST['guarantor_mail'];
$guarantor_phone    = $_REQUEST['guarantor_phone'];
$sms_sent_checkbox  = $_REQUEST['sms_sent_checkbox'];



switch ($action) {
    case 'get_add_page':
        $page = GetPage('');
        $data = array('page'	=> $page);

        break;
    case 'get_edit_page':
        $page = GetPage(GetClient($id));
        $data = array('page'	=> $page);

        break;
    case 'get_list':
        $count    = $_REQUEST['count'];
        $hidden   = $_REQUEST['hidden'];
        $local_id = $_REQUEST['local_id'];

        $rResult = mysql_query("SELECT id,
                                	   name,
                                       pid,
                                	   address,
                                       email,
                                       phone
                                FROM   client_quarantors
                                WHERE  client_id = $local_id AND actived = 1");
         
        $data = array("aaData"	=> array());

        while ( $aRow = mysql_fetch_array( $rResult )){
            $row = array();
            for ( $i = 0 ; $i < $count ; $i++ ){
                $row[] = $aRow[$i];
                if($i == ($count - 1)){
                    $row[] = '<div class="callapp_checkbox">
                                  <input type="checkbox" id="callapp_checkbox1_'.$aRow[$hidden].'" name="check_'.$aRow[$hidden].'" value="'.$aRow[$hidden].'" class="check" />
                                  <label for="callapp_checkbox1_'.$aRow[$hidden].'"></label>
                              </div>';
                }
            }
            $data['aaData'][] = $row;
        }

        break;
    case 'disable':
        mysql_query("UPDATE `client_quarantors` SET `actived`='0' WHERE `id`='$id'");

        break;
    case 'save_guarantor':
        if($guarantor_hidde == ''){
            insert($user_id, $local_id, $guarantor_name, $guarantor_pid, $guarantor_address,$guarantor_mail,$guarantor_phone, $sms_sent_checkbox);
        }else{
            update($guarantor_hidde, $user_id, $guarantor_name, $guarantor_pid, $guarantor_address,$guarantor_mail,$guarantor_phone, $sms_sent_checkbox);
        }
        
        break;
    default:
        $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function insert($user_id, $local_id, $guarantor_name, $guarantor_pid, $guarantor_address,$guarantor_mail,$guarantor_phone, $sms_sent_checkbox){
    mysql_query("INSERT INTO `client_quarantors` 
                            (`user_id`, `datetime`, `client_id`, `name`, `pid`, `address`, `email`, `phone`, `sms_sent`, `actived`) 
                      VALUES 
                            ('$user_id', NOW(), '$local_id', '$guarantor_name', '$guarantor_pid', '$guarantor_address', '$guarantor_mail', '$guarantor_phone', '$sms_sent_checkbox', 1)");
}

function update($car_driver_hidde, $user_id, $guarantor_name, $guarantor_pid, $guarantor_address,$guarantor_mail,$guarantor_phone, $sms_sent_checkbox){
   mysql_query("UPDATE `client_quarantors`
            	   SET `user_id`   = '$user_id',
            		   `datetime`  =  NOW(),
            		   `name`      = '$guarantor_name',
            		   `pid`       = '$guarantor_pid',
            		   `address`   = '$guarantor_address',
            		   `email`     = '$guarantor_mail',
            		   `phone`     = '$guarantor_phone',
                       `sms_sent`  = '$sms_sent_checkbox'
                WHERE  `id`        = '$car_driver_hidde'");
}

function GetClient($id){
    $res = mysql_fetch_assoc(mysql_query("SELECT id,
                                                 name,
                                                 pid,
                                        		 address,
                                                 email,
                                                 phone,
                                                 sms_sent
                                          FROM   client_quarantors
                                          WHERE  id = $id"));
    return $res;
}


function GetPage($res){
    if($res[sms_sent] == 1){$checked = "checked";}else{$checked = "";}
    
    $data  .= '
    	   <div id="dialog-form">
                <fieldset style="width: 390px;  float: left;">
                   <input id="guarantor_hidde" type="hidden" value="'.$res['id'].'">
                   <table class="dialog-form-table">
            	       <tr>
                           <td style="width: 100px;"><label for="datetime">სახელი, გვარი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="guarantor_name" type="text" value="'.$res[name].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 100px;"><label for="pet_num">პირადი ნომერი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="guarantor_pid" type="text" value="'.$res[pid].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 100px;"><label for="pet_num">მისამართი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="guarantor_address" type="text" value="'.$res[address].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 100px;"><label for="pet_num">ელ-ფოსტა</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="guarantor_mail" type="text" value="'.$res[email].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 100px;"><label for="pet_num">ტელეფონი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="guarantor_phone" type="text" value="'.$res[phone].'"></td>
            	       </tr>
                       <tr>
                           <td style="width: 100px;"><label for="pet_num">sms</label></td>
                           <td style="width: 275px;"><input type="checkbox" style="width: 23px; margin-left: 0px;" id="sms_sent_checkbox" type="text" value="1" '.$checked.'></td>
            	       </tr>
                   </table>
            </fieldset>
        </div>';

    return $data;
}
