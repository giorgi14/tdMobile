<?php
require_once('../../../includes/classes/core.php');

// Main Strings
$action  = $_REQUEST['act'];
$error   = '';
$data    = '';
$user_id = $_SESSION['USERID'];

$client_agr_car_mark              = $_REQUEST['client_agr_car_mark'];
$buyer_name                       = $_REQUEST['buyer_name'];
$buyer_pid	                      = $_REQUEST['buyer_pid'];
$b_letter_car_mark                = $_REQUEST['b_letter_car_mark'];
$b_letter_car_id                  = $_REQUEST['b_letter_car_id'];
$b_letter_manufactur_date         = $_REQUEST['b_letter_manufactur_date'];
$b_letter_car_color               = $_REQUEST['b_letter_car_color'];
$b_letter_car_registracion_number = $_REQUEST['b_letter_car_registracion_number'];
$b_letter_car_selling_price       = $_REQUEST['b_letter_car_selling_price'];
$b_letter_amount                  = $_REQUEST['b_letter_amount'];
$b_letter_payment_date            = $_REQUEST['b_letter_payment_date'];
$b_letter_responsible_id          = $_REQUEST['b_letter_responsible_id'];

switch ($action) {
    case 'get_add_page':
        $page = GetPage('');
        $data = array('page' => $page);
        break;
        
    case 'get_edit_page':
        $id = $_REQUEST['id'];
        $page = GetPage(GetBletter($id));
        $data = array('page' => $page);
        break;
        
    case 'get_list':
        $count    = $_REQUEST['count'];
        $hidden   = $_REQUEST['hidden'];

        $rResult = mysql_query("SELECT b_letter.id,
                            		   b_letter.datetime,
                            		   b_letter.`name`,
                                       b_letter.pid,
                                	   b_letter.mark,
                                       b_letter.selling_price,
                                	   b_letter.amount,
                                	   b_letter.payment_date
                                FROM   b_letter
                                WHERE  b_letter.actived = 1");
         
        $data = array("aaData" => array());

        while ($aRow = mysql_fetch_array($rResult)){
            $row = array();
            for ( $i = 0 ; $i < $count ; $i++ ){
                $row[] = $aRow[$i];
            }
            $data['aaData'][] = $row;
        }
        break;
        
    case 'save_b_letter':
        $b_letter_hidde = $_REQUEST['b_letter_hidde'];
        
        if($b_letter_hidde == ''){
            insert($user_id, $client_agr_car_mark, $buyer_name, $buyer_pid, $b_letter_car_mark, $b_letter_car_id, $b_letter_manufactur_date, $b_letter_car_color, $b_letter_car_registracion_number, $b_letter_car_selling_price, $b_letter_amount, $b_letter_payment_date, $b_letter_responsible_id);
            $b_letter_id = mysql_insert_id();
        }else{
            update($b_letter_hidde, $user_id, $client_agr_car_mark, $buyer_name, $buyer_pid, $b_letter_car_mark, $b_letter_car_id, $b_letter_manufactur_date, $b_letter_car_color, $b_letter_car_registracion_number, $b_letter_car_selling_price, $b_letter_amount, $b_letter_payment_date, $b_letter_responsible_id);
            $b_letter_id = $b_letter_hidde;
        }
        
        $data = array('b_letter_id' => $b_letter_id);
        break;
        
    case 'get_client_car_mark':
        $client_id = $_REQUEST['client_id'];
        
        $res_b_letter_car = mysql_fetch_assoc(mysql_query("SELECT client_car.car_marc,
                                                                  client_car.car_id,
                                                        		  client_car.manufacturing_date,
                                                        		  client_car.color,
                                                        	      client_car.registration_number
                                                           FROM   client
                                                           JOIN   client_car ON client.id = client_car.client_id
                                                           WHERE  client.id = '$client_id'"));
        
        $data = array('marc'                => $res_b_letter_car[car_marc],
                      'car_id'              => $res_b_letter_car[car_id],
                      'manufacturing_date'  => $res_b_letter_car[manufacturing_date],
                      'color'               => $res_b_letter_car[color],
                      'registration_number' => $res_b_letter_car[registration_number]);
        break;
        
    default:
        $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function insert($user_id, $client_agr_car_mark, $buyer_name, $buyer_pid, $b_letter_car_mark, $b_letter_car_id, $b_letter_manufactur_date, $b_letter_car_color, $b_letter_car_registracion_number, $b_letter_car_selling_price, $b_letter_amount, $b_letter_payment_date, $b_letter_responsible_id){
    
    mysql_query("INSERT INTO `b_letter` 
					        (`user_id`, `datetime`, `client_id`, `name`, `pid`, `mark`, `car_id`, `manufactur_date`, `car_color`, `car_registracion_number`, `selling_price`, `amount`, `payment_date`, `responsible_id`, `actived`) 
		              VALUES 
					        ('$user_id', NOW(), '$client_agr_car_mark', '$buyer_name', '$buyer_pid', '$b_letter_car_mark', '$b_letter_car_id', '$b_letter_manufactur_date', '$b_letter_car_color', '$b_letter_car_registracion_number', '$b_letter_car_selling_price', '$b_letter_amount', '$b_letter_payment_date', '$b_letter_responsible_id', 1)");
}

function update($b_letter_hidde, $user_id, $client_agr_car_mark, $buyer_name, $buyer_pid, $b_letter_car_mark, $b_letter_car_id, $b_letter_manufactur_date, $b_letter_car_color, $b_letter_car_registracion_number, $b_letter_car_selling_price, $b_letter_amount, $b_letter_payment_date, $b_letter_responsible_id){
   
    mysql_query("UPDATE `b_letter`
                    SET `user_id`                 = '$user_id',
                        `client_id`               = '$client_agr_car_mark',
                        `name`                    = '$buyer_name',
                        `pid`                     = '$buyer_pid',
                        `mark`                    = '$b_letter_car_mark',
                        `car_id`                  = '$b_letter_car_id',
                        `manufactur_date`         = '$b_letter_manufactur_date',
                        `car_color`               = '$b_letter_car_color',
                        `car_registracion_number` = '$b_letter_car_registracion_number',
                        `selling_price`           = '$b_letter_car_selling_price',
                        `amount`                  = '$b_letter_amount',
                        `payment_date`            = '$b_letter_payment_date',
                        `responsible_id`          = '$b_letter_responsible_id'
                  WHERE `id`                      = '$b_letter_hidde'");
}

function get_client_car($id){
    
    $req = mysql_query("SELECT client.id,
                        	   CONCAT(client.`name`, client.lastname, '/N', client_loan_agreement.id, '/', client_car.car_marc) AS `name`
                        FROM   client
                        JOIN   client_car ON client.id = client_car.client_id
                        JOIN   client_loan_agreement ON client_loan_agreement.client_id = client.id 
                        WHERE  client.actived = 1");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function get_b_letter_responsible($id){
    
    $req = mysql_query("SELECT users.id,
                        	   user_info.`name`
                        FROM   users
                        JOIN   user_info ON user_info.user_id = users.id
                        WHERE  users.actived = 1");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function GetBletter($id){
    $res = mysql_fetch_assoc(mysql_query("SELECT  id,
                                                  user_id,
                                            	  client_id,
                                            	 `name`,
                                            	  pid,
                                                  mark,
                                                  car_id,
                                                  manufactur_date,
                                                  car_color,
                                                  car_registracion_number,
                                                  selling_price,
                                                  amount,
                                                  payment_date,
                                                  responsible_id
                                           FROM   b_letter
                                           WHERE  id = '$id'"));
    return $res;
}

function GetPage($res){
    $data  .= '
    	   <div id="dialog-form">
               <fieldset style="float: left;">
                   <input id="b_letter_hidde" type="hidden" value="'.$res['id'].'">
                   <table style="width: 100%;">
                       <tr>
                           <td colspan="2" style="width: 419px;"><label for="client_name">კლიენტი, მარკა, მოდელი</label></td>
                           <td style="width: 220px;"><label for="client_surname">მყიდველი</label></td>
    	                   <td style="width: 220px;"><label for="phone1">პ/ნ</label></td>
                       </tr>
                       <tr>
                           <td colspan="2"><select class="idle" id="client_agr_car_mark" style="width: 419px;">'.get_client_car($res[client_id]).'</select></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="buyer_name" type="text" value="'.$res[name].'"></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="buyer_pid" type="text" value="'.$res[pid].'"></td>
                       </tr>
                       <tr style="height:18px"></tr>
                       <tr>
                           <td style="width: 220px;"><label for="phone2">მარკა, მოდელი</label></td>
                           <td style="width: 220px;"><label for="client_name">საიდენტიფიკაციო</label></td>
                           <td style="width: 220px;"><label for="client_surname">გამოშვების წელი</label></td>
                           <td style="width: 220px;"><label for="phone2">ფერი</label></td>
                       </tr>
                       <tr>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_car_mark" type="text" value="'.$res[mark].'"></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_car_id" type="text" value="'.$res[car_id].'"></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_manufactur_date" type="text" value="'.$res[manufactur_date].'"></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_car_color" type="text" value="'.$res[car_color].'"></td>
                       </tr>
                       <tr style="height:18px"></tr>
                       <tr>
                           <td style="width: 220px;"><label for="client_name">სანომრე ნიშანი</label></td>
                           <td style="width: 220px;"><label for="client_surname">გასაყიდი ფასი</label></td>
                           <td style="width: 220px;"><label for="phone2">ბეს თანხა</label></td>
                           <td style="width: 220px;"><label for="client_name">გადახდის თარიღი</label></td>
                       </tr>
                       <tr>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_car_registracion_number" type="text" value="'.$res[car_registracion_number].'"></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_car_selling_price" type="text" value="'.$res[selling_price].'"></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_amount" type="text" value="'.$res[amount].'"></td>
                           <td style="width: 220px;"><input class="idle" style="width: 200px;" id="b_letter_payment_date" type="text" value="'.$res[payment_date].'"></td>
                       </tr>
                       <tr style="height:18px"></tr>
                       <tr>
                           <td colspan="4" style="width: 220px;"><label for="client_name">ხელმომწერი</label></td>
                       </tr>
                       <tr>
                           <td colspan="4"><select class="idle" id="b_letter_responsible_id" style="width: 419px;">'.get_b_letter_responsible($res[responsible_id]).'</select></td>
                       </tr>
                       <tr style="height:18px"></tr>
                   </table>
               </fieldset>
            </div>';

    return $data;
}
