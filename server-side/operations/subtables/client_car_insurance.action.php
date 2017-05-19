<?php
require_once('../../../includes/classes/core.php');

// Main Strings
$action  = $_REQUEST['act'];
$error   = '';
$data    = '';
$user_id = $_SESSION['USERID'];


$id		                     = $_REQUEST['id'];
$car_insurance_hidde         = $_REQUEST['car_insurance_hidde'];
$local_id                    = $_REQUEST['local_id'];
$car_loan_amount	         = $_REQUEST['car_loan_amount'];
$car_real_price              = $_REQUEST['car_real_price'];
$car_ins_registration_number = $_REQUEST['car_ins_registration_number'];
$car_insurance_amount        = $_REQUEST['car_insurance_amount'];
$car_insurance_start         = $_REQUEST['car_insurance_start'];
$car_insurance_end           = $_REQUEST['car_insurance_end'];
$curent_courceee             = $_REQUEST['curent_courceee'];



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
                        			   datetime,
                        			   car_insurance_start,
                        			   car_insurance_end,
                        			   car_insurance_amount
                                FROM   car_insurance_info
                                WHERE  actived = 1 AND client_id = '$local_id'");
         
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
        mysql_query("UPDATE `car_insurance_info` SET `actived`='0' WHERE `id`='$id'");

        break;
    case 'save_insurance_info':
        
        if($car_insurance_hidde == ''){
            insert($user_id, $local_id, $car_loan_amount, $car_real_price, $car_ins_registration_number, $car_insurance_amount, $curent_courceee, $car_insurance_start, $car_insurance_end);
            $insurance_id = mysql_insert_id();
        }else{
            update($car_insurance_hidde, $car_loan_amount, $car_real_price, $car_ins_registration_number, $car_insurance_amount, $curent_courceee, $car_insurance_start, $car_insurance_end);
            $insurance_id = $car_insurance_hidde;
        }
        
        $data = array('insurance_id' => $insurance_id);
        break;
    default:
        $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function insert($user_id, $local_id, $car_loan_amount, $car_real_price, $car_ins_registration_number, $car_insurance_amount, $curent_courceee, $car_insurance_start, $car_insurance_end){
    mysql_query("INSERT INTO `car_insurance_info` 
            				(`user_id`, `datetime`, `client_id`, `car_loan_amount`, `car_real_price`, `car_ins_registration_number`, `car_insurance_amount`, `cource`, `car_insurance_start`, `car_insurance_end`, `actived`) 
            		  VALUES 
            				('$user_id', NOW(), '$local_id', '$car_loan_amount', '$car_real_price', '$car_ins_registration_number', '$car_insurance_amount', '$curent_courceee', '$car_insurance_start', '$car_insurance_end', 1)");
}

function update($car_insurance_hidde, $car_loan_amount, $car_real_price, $car_ins_registration_number, $car_insurance_amount, $curent_courceee, $car_insurance_start, $car_insurance_end){
   mysql_query("UPDATE `car_insurance_info`
                   SET `user_id`                     = '$user_id',
        			   `car_loan_amount`             = '$car_loan_amount',
        			   `car_real_price`              = '$car_real_price',
        			   `car_ins_registration_number` = '$car_ins_registration_number',
        			   `car_insurance_amount`        = '$car_insurance_amount',
                       `cource`                      = '$curent_courceee',
        			   `car_insurance_start`         = '$car_insurance_start',
        			   `car_insurance_end`           = '$car_insurance_end'
                WHERE  `id`                          = '$car_insurance_hidde'");
}

function GetClient($id){
    $res = mysql_fetch_assoc(mysql_query("SELECT id,
                                                 car_loan_amount,
                                                 car_real_price,
                                        		 car_ins_registration_number,
                                                 car_insurance_amount,
                                                 car_insurance_start,
                                                 car_insurance_end,
                                                 cource
                                          FROM   car_insurance_info
                                          WHERE  id = $id"));
    return $res;
}


function GetPage($res){
    $data  .= '
    	   <div id="dialog-form">
                <fieldset style="width: 440px;  float: left;">
                   <input id="car_insurance_hidde" type="hidden" value="'.$res['id'].'">
                   <table class="dialog-form-table">
            	       <tr>
                           <td style="width: 150px;"><label for="datetime">სესხის ოდენობა</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_loan_amount" type="text" value="'.$res[car_loan_amount].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">ავტოტრანსპორტის ღ-ბა</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_real_price" type="text" value="'.$res[car_real_price].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">სარეგისტრაციო ნომერი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_ins_registration_number" type="text" value="'.$res[car_ins_registration_number].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">სადაზღვევო თანხა</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_insurance_amount" type="text" value="'.$res[car_insurance_amount].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">კურსი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="curent_courceee" type="text" value="'.$res[cource].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">დასაწყისი</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_insurance_start" type="text" value="'.$res[car_insurance_start].'"></td>
            	       </tr>
                       <tr style="height:15px;"></tr>
                       <tr>
                           <td style="width: 150px;"><label for="pet_num">დასასრული</label></td>
                           <td style="width: 275px;"><input style="width: 275px;" id="car_insurance_end" type="text" value="'.$res[car_insurance_end].'"></td>
            	       </tr>
                   </table>
            </fieldset>
        </div>';

    return $data;
}
