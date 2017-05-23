<?php
require_once('../../../includes/classes/core.php');

// Main Strings
$action  = $_REQUEST['act'];
$error   = '';
$data    = '';
$user_id = $_SESSION['USERID'];


$id		           = $_REQUEST['id'];
$client_pers_hidde = $_REQUEST['client_pers_hidde'];
$local_id          = $_REQUEST['local_id'];
$client_pers	   = $_REQUEST['client_pers'];
$client_pers_phone = $_REQUEST['client_pers_phone'];
$sms_sent_checkbox = $_REQUEST['sms_sent_checkbox'];



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

        $rResult = mysql_query("SELECT client_person.id,
                                	   client_person.person,
                                	   client_person.phone
                                FROM   client_person
                                WHERE  client_id = $local_id AND actived = 1");
         
        $data = array("aaData"	=> array());

        while ( $aRow = mysql_fetch_array( $rResult )){
            $row = array();
            for ( $i = 0 ; $i < $count ; $i++ ){
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
        mysql_query("UPDATE `client_person` SET `actived`='0' WHERE `id`='$id'");

        break;
    case 'save_client_pers':
        if($client_pers_hidde == ''){
            insert($user_id, $local_id, $client_pers, $client_pers_phone, $sms_sent_checkbox);
        }else{
            update($client_pers_hidde, $user_id, $local_id, $client_pers, $client_pers_phone, $sms_sent_checkbox);
        }
        break;
    default:
        $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function insert($user_id, $local_id, $client_pers, $client_pers_phone, $sms_sent_checkbox){
    mysql_query("INSERT INTO `client_person` 
					         (`user_id`, `client_id`, `datetime`, `person`, `phone`, `sms_sent`, `actived`) 
		               VALUES 
					         ('$user_id', '$local_id', NOW(), '$client_pers', '$client_pers_phone', '$sms_sent_checkbox', 1)");
}

function update($client_pers_hidde, $user_id, $local_id, $client_pers, $client_pers_phone, $sms_sent_checkbox){
    mysql_query("UPDATE `client_person`
                	 SET `user_id`  = '$user_id',
                		 `datetime` = NOW(),
                	     `person`   = '$client_pers',
                		 `phone`    = '$client_pers_phone',
                         `sms_sent` = '$sms_sent_checkbox'
                  WHERE  `id`       = '$client_pers_hidde' ");
}

function GetClient($id){
    $res = mysql_fetch_assoc(mysql_query("SELECT id,
                                                 person,
                                        		 phone,
                                                 sms_sent
                                          FROM   client_person
                                          WHERE  id = $id"));
    return $res;
}

function GetPage($res){
    $checked = "";
    if ($res[sms_sent] == 1){
        $checked="checked";
    }
    if ($res[id] == ''){$index = '995';}else{$index = '';}
    $data  .= '
    	   <div id="dialog-form">
                <fieldset style="width: 400px;  float: left;">
                   <input id="client_pers_hidde" type="hidden" value="'.$res['id'].'">
                   <table class="dialog-form-table">
            	       <tr>
                           <td style="width: 110px;"><label for="datetime">საკონტაქტო პირი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="client_pers" type="text" value="'.$res[person].'"></td>
            	       </tr>
                       <tr style="height:20px;"></tr>
                       <tr>
                           <td style="width: 110px;"><label for="pet_num">ტელეფონი</label></td>
                           <td style="width: 275px;"><input placeholder="შეიყვანეთ ნომერი" onKeyDown="if(this.value.length==11) return false;" onkeypress="{if (event.which != 8 &amp;&amp; event.which != 0 &amp;&amp; event.which!=46 &amp;&amp; (event.which < 48 || event.which > 57)) {$(\'#errmsg\').html(\'მხოლოდ ციფრი\').show().fadeOut(\'slow\'); return false;}}" type="text" id="client_pers_phone" class="idle" style="width: 275px;" value="'.$index.''.$res[phone].'"></td>
                       </tr>
                       <tr style="height:20px;"></tr>
                       <tr>
                           <td style="width: 100px;"><label for="pet_num">sms</label></td>
                           <td style="width: 275px;"><input type="checkbox" style="width: 23px; margin-left: 0px;" id="sms_sent_person_checkbox" type="text" value="1" '.$checked.'></td>
            	       </tr>
                   </table>
            </fieldset>
        </div>';

    return $data;
}
