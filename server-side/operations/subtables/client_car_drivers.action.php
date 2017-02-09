<?php
require_once('../../../includes/classes/core.php');

// Main Strings
$action  = $_REQUEST['act'];
$error   = '';
$data    = '';
$user_id = $_SESSION['USERID'];


$id		          = $_REQUEST['id'];
$car_driver_hidde = $_REQUEST['car_driver_hidde'];
$local_id         = $_REQUEST['local_id'];
$name	          = $_REQUEST['name'];
$born             = $_REQUEST['born'];
$license_type     = $_REQUEST['license_type'];
$license_born     = $_REQUEST['license_born'];
$position         = $_REQUEST['position'];



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

        $rResult = mysql_query("SELECT client_car_drivers.id,
                                	   client_car_drivers.name,
                                       client_car_drivers.position,
                                	   client_car_drivers.born_date,
                                       client_car_drivers.driving_license_type,
                                       client_car_drivers.driving_license_date
                                FROM   client_car_drivers
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
        mysql_query("UPDATE `client_car_drivers` SET `actived`='0' WHERE `id`='$id'");

        break;
    case 'save_car_drivers':
        
        if($car_driver_hidde == ''){
            insert($user_id, $local_id, $name, $position, $born,$license_type,$license_born);
        }else{
            update($car_driver_hidde, $user_id, $name, $position, $born,$license_type,$license_born);
        }
        break;
    default:
        $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function insert($user_id, $local_id, $name, $position, $born,$license_type,$license_born){
    mysql_query("INSERT INTO `client_car_drivers` 
						(`user_id`, `datetime`, `client_id`, `name`, `position`, `born_date`, `driving_license_type`, `driving_license_date`) 
					VALUES 
					('$user_id', NOW(), '$local_id', '$name', '$position', '$born', '$license_type', '$license_born')");
}

function update($car_driver_hidde, $user_id, $name, $position, $born,$license_type,$license_born){
   mysql_query("UPDATE `client_car_drivers`
                    SET `name`                 = '$name',
                        `position`             = '$position',
                        `born_date`            = '$born',
                        `driving_license_type` = '$license_type',
                        `driving_license_date` = '$license_born'
                 WHERE  `id`                   = '$car_driver_hidde' ");
}

function GetClient($id){
    $res = mysql_fetch_assoc(mysql_query("SELECT id,
                                                 name,
                                                 position,
                                        		 born_date,
                                                 driving_license_type,
                                                 driving_license_date
                                          FROM   client_car_drivers
                                          WHERE  id = $id"));
    return $res;
}


function GetPage($res){
    $data  .= '
    	   <div id="dialog-form">
                <fieldset style="width: 440px;  float: left;">
                   <input id="car_driver_hidde" type="hidden" value="'.$res['id'].'">
                   <table class="dialog-form-table">
            	       <tr>
                           <td style="width: 150px;"><label for="datetime">სახელი, გვარი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_driver_name" type="text" value="'.$res[name].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">თანამდებობა</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_driver_position" type="text" value="'.$res[position].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">დაბადების თარიღი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_driver_born" type="text" value="'.$res[born_date].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">მართვის მოწმობის ტიპი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_driver_license_type" type="text" value="'.$res[driving_license_type].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">გაცემის თარიღი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_driver_license_born" type="text" value="'.$res[driving_license_date].'"></td>
            	       </tr>
                   </table>
            </fieldset>
        </div>';

    return $data;
}
