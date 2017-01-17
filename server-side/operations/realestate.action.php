<?php
require_once('../../includes/classes/core.php');
 
// Main Strings
$action  = $_REQUEST['act'];
$error   = '';
$data    = '';
$user_id = $_SESSION['USERID'];

$id        = $_REQUEST['id'];
$local_id  = $_REQUEST['local_id'];
$id_hidden = $_REQUEST['id_hidden'];

//ცლიენტის მონაცემები//
$name         = $_REQUEST['name'];
$surname      = $_REQUEST['surname'];
$born_date    = $_REQUEST['born_date'];
$tin          = $_REQUEST['tin'];
$tin_number   = $_REQUEST['tin_number'];
$tin_date     = $_REQUEST['tin_date'];
$comment      = $_REQUEST['comment'];
$mail         = $_REQUEST['mail'];
$phone        = $_REQUEST['phone'];
$fact_address = $_REQUEST['fact_address'];
$jur_address  = $_REQUEST['jur_address'];

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
        $count  = $_REQUEST['count'];
		$hidden = $_REQUEST['hidden'];

	  	$rResult = mysql_query("SELECT client.id,
                        			   client.id,
                        			   client.datetme,
                        			   CONCAT(client.`name`,' ',client.lastname),
                        			   client.pid,
                        			   client.phone,
                        			   '',
                        			   ''
                                FROM  `client`
                                WHERE  client.actived = 1");
	  
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
        mysql_query("UPDATE `client` SET `actived`='0' WHERE `id`='$id'");
    
        break;
    case 'save_client':
        if($id_hidden == ''){
            insert($local_id, $user_id, $name, $surname, $born_date, $tin, $tin_number, $tin_date,$mail, $phone, $fact_address, $jur_address, $comment);
        }else{
            update($id_hidden, $user_id, $name, $born_date, $surname, $tin, $tin_number, $tin_date, $mail, $phone, $fact_address, $jur_address, $comment);
        }
        break;
    case 'get_local_id':
        
        $table_name = $_REQUEST['table_name'];
        
        $res = mysql_fetch_assoc(mysql_query("SELECT `value` FROM increment WHERE `table` = '$table_name'"));
        
        mysql_query("UPDATE `increment` SET `value` = $res[value]+1 WHERE `table` = '$table_name'");
        
        $data = array('local_id' => $res[value]);
        
        break;
    case 'upload_document':
    
        $local_id = $_REQUEST['local_id'];
        
         $file_tbale = mysql_query("SELECT  file.`name`,
                            				file.`rand_name`,
                            				file.`date`,
                            				file.`id`
                                    FROM   `client_documents`
                                    JOIN    file ON file.id = client_documents.file_id
                                    WHERE   client_documents.`client_id` = '$local_id' AND file.`actived` = 1");
        $str_file_documents = '';
        while ($file_res_document = mysql_fetch_assoc($file_tbale)){
            $str_file_documents .= '
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_document[date].'</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_document[name].'</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:28%; float:left;" onclick="download_file(\''.$file_res_document[rand_name].'\')">ჩამოტვირთვა</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:8%; float:left;" onclick="delete_file(\''.$file_res_document[id].'\',\'client_documents\')">-</div>';
        } 
        
        $data = array('documets' => $str_file_documents);
        
        break;
    case 'upload_papers':
    
        $local_id = $_REQUEST['local_id'];
    
        $file_tbale = mysql_query("SELECT  file.`name`,
                                            file.`rand_name`,
                                            file.`date`,
                                            file.`id`
                                    FROM   `client_papers`
                                    JOIN    file ON file.id = client_papers.file_id
                                    WHERE   client_papers.`client_id` = '$local_id' AND file.`actived` = 1");
        $str_file_documents = '';
        while ($file_res_document = mysql_fetch_assoc($file_tbale)){
            $str_file_documents .= '
                                <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_document[date].'</div>
                                <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_document[name].'</div>
                                <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:28%; float:left;" onclick="download_file(\''.$file_res_document[rand_name].'\')">ჩამოტვირთვა</div>
                                <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:8%; float:left;" onclick="delete_file(\''.$file_res_document[id].'\',\'client_papers\')">-</div>';
        }
    
        $data = array('papers' => $str_file_documents);
    
        break;
   default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function insert($local_id, $user_id, $name, $surname, $born_date, $tin, $tin_number, $tin_date,$mail, $phone, $fact_address, $jur_address, $comment){
    
    mysql_query("INSERT INTO `client` 
                            (`id`, `user_id`, `datetme`, `name`, `lastname`, `born_date`, `pid`, `pid_number`, `pid_date`, `email`, `phone`, `actual_address`, `juridical_address`, `comment`, `actived`) 
                      VALUES 
                            ('$local_id', '$user_id', NOW(), '$name', '$surname', '$born_date', '$tin', '$tin_number', '$tin_date', '$mail', '$phone', '$fact_address', '$jur_address', '$comment', 1);");
    
    $client_id = mysql_insert_id();
    
    //მინდობილი პირის მონაცემები//
    $client_trust_name         = $_REQUEST['client_trust_name'];
    $client_trust_surname      = $_REQUEST['client_trust_surname'];
    $client_trust_tin          = $_REQUEST['client_trust_tin'];
    $client_trust_phone        = $_REQUEST['client_trust_phone'];
    $client_trust_mail         = $_REQUEST['client_trust_mail'];
    $client_trust_fact_address = $_REQUEST['client_trust_fact_address'];
    $client_trust_jur_address  = $_REQUEST['client_trust_jur_address'];
    
    if ($client_trust_name != '' || $client_trust_surname != '' || $client_trust_tin != '' || $client_trust_phone != '' || $client_trust_mail != '' || $client_trust_fact_address != '' || $client_trust_jur_address != '') {
        mysql_query("INSERT INTO `client_trusted_person` 
                                (`user_id`, `datetime`, `client_id`, `name`, `lastname`, `pid`, `phone`, `email`, `actual_address`, `juridical_address`, `actived`) 
                          VALUES 
                                ('$user_id', NOW(), '$client_id', '$client_trust_name', '$client_trust_surname', '$client_trust_tin', '$client_trust_phone', '$client_trust_mail', '$client_trust_fact_address', '$client_trust_jur_address', 1)");
    }
    
    //მანქანის მონაცემები//
    $car_model               = $_REQUEST['car_model'];
    $car_born                = $_REQUEST['car_born'];
    $car_color               = $_REQUEST['car_color'];
    $car_type                = $_REQUEST['car_type'];
    $car_engine              = $_REQUEST['car_engine'];
    $car_registration_number = $_REQUEST['car_registration_number'];
    $car_owner               = $_REQUEST['car_owner'];
    $car_ident               = $_REQUEST['car_ident'];
    $car_ertificate          = $_REQUEST['car_ertificate'];
    
    
    if ($car_model != '' || $car_born != '' || $car_color != '' || $car_type != 0 || $car_engine != '' || $car_registration_number != '' || $car_ident != '' || $car_ertificate != '') {
        mysql_query("INSERT INTO `client_car` 
                                (`user_id`, `datetime`, `client_id`, `model`, `manufacturing_date`, `color`, `type_id`, `engine_size`, `registration_number`, `owner`, `car_id`, `certificate_id`, `actived`) 
                          VALUES 
                                ('$user_id', NOW(), '$client_id', '$car_model', '$car_born', '$car_color', '$car_type', '$car_engine', '$car_registration_number', '$car_owner', '$car_ident', '$car_ertificate', 1)");
    }
    
}

function update($id_hidden, $user_id, $name, $born_date, $surname, $tin, $tin_number, $tin_date, $mail, $phone, $fact_address, $jur_address, $comment){
    
    mysql_query("UPDATE `client`
            	    SET `user_id`           = '$user_id',
            		    `name`              = '$name',
            		    `lastname`          = '$surname',
                        `born_date`         = '$born_date',
            		    `pid`               = '$tin',
                        `pid_number`        = '$tin_number',
                        `pid_date`          = '$tin_date',
            		    `email`             = '$mail',
            		    `phone`             = '$phone',
            		    `actual_address`    = '$fact_address',
            		    `juridical_address` = '$jur_address',
            		    `comment`           = '$comment'
                 WHERE  `id`                = '$id_hidden'");
    
    //მინდობილი პირის მონაცემები//
    $client_trust_name         = $_REQUEST['client_trust_name'];
    $client_trust_surname      = $_REQUEST['client_trust_surname'];
    $client_trust_tin          = $_REQUEST['client_trust_tin'];
    $client_trust_phone        = $_REQUEST['client_trust_phone'];
    $client_trust_mail         = $_REQUEST['client_trust_mail'];
    $client_trust_fact_address = $_REQUEST['client_trust_fact_address'];
    $client_trust_jur_address  = $_REQUEST['client_trust_jur_address'];
    
    $res = mysql_query("SELECT client_id FROM  `client_trusted_person` WHERE  client_trusted_person.client_id = '$id_hidden'");
    
    if (mysql_num_rows($res) == 0) {
        if ($client_trust_name != '' || $client_trust_surname != '' || $client_trust_tin != '' || $client_trust_phone != '' || $client_trust_mail != '' || $client_trust_fact_address != '' || $client_trust_jur_address != '') {
            mysql_query("INSERT INTO `client_trusted_person`
                                    (`user_id`, `datetime`, `client_id`, `name`, `lastname`, `pid`, `phone`, `email`, `actual_address`, `juridical_address`, `actived`)
                              VALUES
                                    ('$user_id', NOW(), '$id_hidden', '$client_trust_name', '$client_trust_surname', '$client_trust_tin', '$client_trust_phone', '$client_trust_mail', '$client_trust_fact_address', '$client_trust_jur_address', 1)");
        }
   }else{
        mysql_query("UPDATE `client_trusted_person`
                    	SET `user_id`           = '$user_id',
                    	    `datetime`          = NOW(),
            			    `name`              = '$client_trust_name',
            			    `lastname`          = '$client_trust_surname',
            			    `pid`               = '$client_trust_tin',
            			    `phone`             = '$client_trust_phone',
            			    `email`             = '$client_trust_mail',
            			    `actual_address`    = '$client_trust_fact_address',
            			    `juridical_address` = '$client_trust_jur_address'
                     WHERE  `client_id`         = '$id_hidden'");
    }
    
    $res_car = mysql_query("SELECT client_id FROM  `client_car` WHERE  client_car.client_id = '$id_hidden'");
    //მანქანის მონაცემები//
    $car_model               = $_REQUEST['car_model'];
    $car_born                = $_REQUEST['car_born'];
    $car_color               = $_REQUEST['car_color'];
    $car_type                = $_REQUEST['car_type'];
    $car_engine              = $_REQUEST['car_engine'];
    $car_registration_number = $_REQUEST['car_registration_number'];
    $car_owner               = $_REQUEST['car_owner'];
    $car_ident               = $_REQUEST['car_ident'];
    $car_ertificate          = $_REQUEST['car_ertificate'];
    
    if (mysql_num_rows($res_car) == 0) {
        if ($car_model != '' || $car_born != '' || $car_color != '' || $car_type != 0 || $car_engine != '' || $car_registration_number != '' || $car_ident != '' || $car_ertificate != '') {
            mysql_query("INSERT INTO `client_car` 
                                    (`user_id`, `datetime`, `client_id`, `model`, `manufacturing_date`, `color`, `type_id`, `engine_size`, `registration_number`, `owner`, `car_id`, `certificate_id`, `actived`) 
                              VALUES 
                                    ('$user_id', NOW(), '$id_hidden', '$car_model', '$car_born', '$car_color', '$car_type', '$car_engine', '$car_registration_number', '$car_owner', '$car_ident', '$car_ertificate', 1)");
        }
    }else{
        mysql_query("UPDATE `client_car`
                	    SET `user_id`             = '$user_id',
                			`model`               = '$car_model',
                			`manufacturing_date`  = '$car_born',
                			`color`               = '$car_color',
                			`type_id`             = '$car_type',
                			`engine_size`         = '$car_engine',
                			`registration_number` = '$car_registration_number',
                			`owner`               = '$car_owner',
                			`car_id`              = '$car_ident',
                			`certificate_id`      = '$car_ertificate'
                     WHERE  `client_id`           = '$id_hidden'");
    }
}

function car_type($id){
    $req = mysql_query("SELECT id, 
                              `name` 
                        FROM   car_type 
                        WHERE  actived = 1");

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

function GetClient($id){
    $res = mysql_fetch_assoc(mysql_query("SELECT client.id,
                                    			 client.id,
                                    			 client.`datetme`,
                                    			 client.`name`,
                                    			 client.`lastname`,
                                                 client.`born_date`,
                                    			 client.`pid`,
                                                 client.`pid_number`,
                                                 client.`pid_date`,
                                    			 client.`phone`,
                                    			 client.`comment`,
                                    			 client.`email`,
                                    			 client.`actual_address`,
                                    			 client.`juridical_address`,
                                                 client_trusted_person.id as trust_person_id,
                                    			 client_trusted_person.`name` AS client_trusted_name,
                                    			 client_trusted_person.lastname AS client_trusted_lastname,
                                    			 client_trusted_person.pid AS client_trusted_pid,
                                    			 client_trusted_person.phone AS client_trusted_phone,
                                    			 client_trusted_person.email AS client_trusted_email,
                                    			 client_trusted_person.actual_address AS client_trusted_actual_address,
                                    			 client_trusted_person.juridical_address AS client_trusted_juridical_address,
                                                 client_car.model AS client_car_model,
                                                 client_car.manufacturing_date AS client_car_manufacturing_date,
                                                 client_car.type_id AS client_car_type_id,
                                                 client_car.engine_size AS client_car_engine_size,
                                                 client_car.registration_number AS client_car_registration_number,
                                                 client_car.`owner` AS client_car_owner,
                                                 client_car.`car_id` AS client_car_car_id,
                                                 client_car.`certificate_id` AS client_car_certificate_id,
                                                 client_car.`color` AS client_car_color,
                                    			 '',
                                    			 ''
                                            FROM  `client`
                                            LEFT JOIN client_trusted_person ON client_trusted_person.client_id = client.id
                                            LEFT JOIN client_car ON client_car.client_id = client.id
                                            WHERE  client.id = $id"));
	return $res;
}

function GetPage($res){
    
    if ($res[id] == '') {
        $checked = "";
        $table_hidde ="display:none;";
    }else if ($res[trust_person_id] == '') {
        $checked = "";
        $table_hidde = "display:none;";
    }else {
        $checked = "checked";
        $table_hidde = "display:block;";
    }
    
    $data  .= '
    	   <div id="dialog-form">
                <fieldset style="width: 145px;  float: left;">
                   <input id="id_hidden" type="hidden" value="'.$res['id'].'">
                   <input id="local_id" type="hidden" value="'.$res['id'].'">
                   <legend>ინფორმაცია</legend>
                   <table class="dialog-form-table">
            	       <tr style="height:0px;">
                           <td style="width: 150px;"><label for="datetime">თარიღი</label></td>
            	       </tr>
                       <tr>
                           <td><input disabled style="width: 137px;" id="datetime" type="text" value="'.$res[datetme].'"></td>
                       </tr>
                       <tr style="height:0px;">
                           <td><label style=" margin-top:10px" for="pet_num">სახელი</label></td>
            	       </tr>
                       <tr style="height:0px;">
                           <td><input style="width: 137px;" id="name" type="text" value="'.$res[name].'"></td>
            	       </tr>  
                       <tr style="height:0px;">
                           <td><label style=" margin-top:10px" for="recive_num">გვარი</label></td>
                       </tr>
                       <tr style="height:0px;">
                           <td><input style="width: 137px;" id="surname" type="text" value="'.$res[lastname].'"></td>
                       </tr>
                       <tr style="height:0px;">
                           <td><label style=" margin-top:10px" for="recive_num">დაბადების თარიღი</label></td>
                       </tr>
                       <tr style="height:0px;">
                           <td><input style="width: 137px;" id="born_date" type="text" value="'.$res[born_date].'"></td>
                       </tr> 
                       <tr style="height:0px;">
                           <td><label style=" margin-top:10px" for="pretens_num">პირადი ნომერი</td>
                       </tr>  
                       <tr style="height:0px;">
                           <td><input style="width: 137px;" id="tin" type="text" value="'.$res[pid].'"></td>
                       </tr>
                       <tr style="height:0px;">
                           <td><label style=" margin-top:10px" for="pretens_num">პირ. მოწმ. ნომერი</td>
                       </tr>  
                       <tr style="height:0px;">
                           <td><input style="width: 137px;" id="tin_number" type="text" value="'.$res[pid_number].'"></td>
                       </tr>
                       <tr style="height:0px;">
                           <td><label style=" margin-top:10px" for="pretens_num">პირ. გაცემის თარიღი</td>
                       </tr>  
                       <tr style="height:0px;">
                           <td><input style="width: 137px;" id="tin_date" type="text" value="'.$res[pid_date].'"></td>
                       </tr>
                       <tr style="height:18px;"></tr>               
                       <tr style="height:0px;">
                           <td><label for="incomming_id">მიმდინარე დავალიანება</label></td>
                       </tr>
                       <tr style="height:0px;">
                           <td><input disabled style="width: 137px; height: 50px; color: #000; font-size: 25px; text-align: center;" id="curent_debt" type="text" value="1785"></td>
                       </tr>
                       <tr style="height:18px;"></tr> 
                       <tr style="height:0px;">
                           <td><label for="comment">დამატებითი ინფორმაცია</label></td>
                       </tr>
                       <tr style="height:0px;">
                           <td colspan=1><textarea id="comment" style="resize: vertical;width: 137px;height: 70px;">'.$res['comment'].'</textarea></td>
                       </tr>
                   </table>
                </fieldset>
                <div id="side_menu" style="float: left;height: 608px; width: 80px; margin-left: 10px; background: #272727; color: #FFF;margin-top: 6px;">
                    <spam class="info" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'info\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/info.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">კლიენტი</div></spam>
                    <spam class="agreement" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'agreement\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/agreement.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">ხელშეკრუ<br>ლება</div></spam>
                    <spam class="auto_mobile" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'auto_mobile\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons" alt="24 ICON" height="24" width="24"><div style="text-align: center;">მანქანა</div></spam>
                    <spam class="pledge" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'pledge\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons" alt="24 ICON" height="24" width="24"><div style="text-align: center;">გირავნობა</div></spam>
                    <spam class="papers" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'papers\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/file.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">საბუთები</div></spam>
                    <spam class="documents" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'documents\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/file.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">დოკუმენ<br>ტები</div></spam>
                </div>
    	       <div style="width:905px; float:left; margin-left:10px;" id="right_side">
                    <fieldset style="display:none;" id="info">
                        <legend>ძირითადი ინფორმაცია</legend>
                        <span class="hide_said_menu">x</span>
                        <table style="width: 100%;">
                           <tr>
                               <td style="width: 215px;"><label for="client_name">მეილი</label></td>
                               <td style="width: 215px;"><label for="client_surname">ტელეფონი</label></td>
        	                   <td style="width: 215px;"><label for="phone1">ფაქტობრივი მისამართი</label></td>
                               <td style="width: 215px;"><label for="phone2">იურიდიული მისამართი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 215px;"><input style="width: 195px;" id="mail" type="text" value="'.$res['email'].'"></td>
                               <td style="width: 215px;"><input style="width: 195px;" id="phone" type="text" value="'.$res[phone].'"></td>
                               <td style="width: 215px;"><input style="width: 195px;" id="fact_address" type="text" value="'.$res[actual_address].'"></td>
                               <td style="width: 215px;"><input style="width: 195px;" id="jur_address" type="text" value="'.$res[juridical_address].'"></td>
                           </tr>
                           <tr style="height:30px;"></tr>
                        </table>
                        <legend style="height:25px;">საკონტაქტო პირი</legend>
                            <div id="button_area">
                            	<button id="add_button_pers">დამატება</button>
                            	<button id="delete_button_pers">წაშლა</button>
                            </div>
                            <table class="display" id="table_person" style="width: 100%;">
                                <thead>
                                    <tr id="datatable_header">
                                        <th>ID</th>
                	                    <th style="width: 50%;">საკონტაქტო პირი</th>
                                        <th style="width: 50%;">ტელეფონი</th>
                                        <th style="width: 25px;">#</th>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr class="search_header">
                                        <th class="colum_hidden">
                                    	   <input type="text" name="search_id" value="ფილტრი" class="search_init" />
                                        </th>
                                        <th>
                                        	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                        </th>
                	                    <th>
                                        	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                                        </th>
                                        <th style="border-right: 1px solid #A3D0E4;">
                                            <div class="callapp_checkbox">
                                                <input type="checkbox" id="check-all_pers" name="check-all_pers" />
                                                <label for="check-all_pers"></label>
                                            </div>
                                        </th>            
                                    </tr>
                                </thead>
                           </table>
                           <table style="width: 15%;">
                              <tr style="height: 35px;">
                                  <td style="width: 15px;"><input style="width: 15px;" id="trust_pers_checkbox" type="checkbox" '.$checked.'></td>
                                  <td style="width: 50px;"><label style="padding-top: 8px; margin-left: -19px;" for="client_name">მინდობილი პირი</label></td>
                              </tr>
                           </table>
                           <table style="width: 100%; '.$table_hidde.'" id="truste_table">
                              <tr>
                                  <td style="width: 215px;"><label for="client_surname">სახელი</label></td>
            	                  <td style="width: 215px;"><label for="phone1">გვარი</label></td>
                                  <td style="width: 215px;"><label for="phone2">პირადი ნომერ</label></td>
                                  <td style="width: 215px;"><label for="client_surname">ტელეფონი</label></td>
                              </tr>
                              <tr>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_name" type="text" value="'.$res[client_trusted_name].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_surname" type="text" value="'.$res[client_trusted_lastname].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_tin" type="text" value="'.$res[client_trusted_pid].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_phone" type="text" value="'.$res[client_trusted_phone].'"></td>
                              </tr>
                              <tr style="height:20px"></tr>
                              <tr>
                                  <td style="width: 215px;"><label for="client_surname">მეილი</label></td>
            	                  <td style="width: 215px;"><label for="phone1">ფაქტობრივი მისამართი</label></td>
                                  <td colspan="2" style="width: 215px;"><label for="phone2">იურიდიული მისამართი</label></td>
                              </tr>
                              <tr>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_mail" type="text" value="'.$res[client_trusted_email].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_fact_address" type="text" value="'.$res[client_trusted_actual_address].'"></td>
                                  <td colspan="2" style="width: 215px;"><input style="width: 195px;" id="client_trust_jur_address" type="text" value="'.$res[client_trusted_juridical_address].'"></td>
                              </tr>
                         </table>
                    </fieldset>
                   
            	    <fieldset style="display:none;" id="agreement">
                        <legend>ძირითადი ინფორმაცია</legend>
        	            <span class="hide_said_menu">x</span>
                        <table style="width: 100%;">
                           <tr>
                           </tr>
                           <tr>
                           </tr>
                        </table>
        	        </fieldset>
            	    <fieldset style="display:none;" id="auto_mobile">
            	        <span class="hide_said_menu">x</span>
                        <legend>ძირითადი ინფორმაცია</legend>
                        <table style="width: 100%;">
                           <tr>
                               <td style="width: 220px;"><label for="client_name">მოდელი</label></td>
                               <td style="width: 220px;"><label for="client_surname">გამოშვების წელი</label></td>
        	                   <td style="width: 220px;"><label for="phone1">ფერი</label></td>
                               <td style="width: 220px;"><label for="phone2">ტიპი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_model" type="text" value="'.$res[client_car_model].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_born" type="text" value="'.$res[client_car_manufacturing_date].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_color" type="text" value="'.$res[client_car_color].'"></td>
                               <td><select id="car_type" style="width: 195px;">'.car_type($res[client_car_type_id]).'</select></td>
                           </tr>
                           <tr style="height:20px"></tr>
                           <tr>
                               <td style="width: 220px;"><label for="client_name">ძრავის მოცულობა</label></td>
                               <td style="width: 220px;"><label for="client_surname">რეგისტრაციის ნომერი</label></td>
        	                   <td style="width: 220px;"><label for="phone1">მესაკუთრე</label></td>
                               <td style="width: 220px;"><label for="phone2">საიდენთიპიკაციო ნომერი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_engine" type="text" value="'.$res[client_car_engine_size].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_registration_number" type="text" value="'.$res[client_car_registration_number].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_owner" type="text" value="'.$res[client_car_owner].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_ident" type="text" value="'.$res[client_car_car_id].'"></td>
                           </tr>
                           <tr style="height:20px"></tr>
                           <tr>
                               <td colspan="4" style="width: 220px;"><label for="client_name">მოწმობის ნომერი</label></td>
                           </tr>
                           <tr>
                               <td colspan="4" style="width: 220px;"><input style="width: 200px;" id="car_ertificate" type="text" value="'.$res[client_car_certificate_id].'"></td>
                           </tr>
                        </table>
        	        </fieldset>
                    <fieldset style="display:none;" id="pledge">
                        <legend>ძირითადი ინფორმაცია</legend>
        	            <span class="hide_said_menu">x</span>
                        <table style="width: 100%;">
                           <tr>
                           </tr>
                           <tr>
                           </tr>
                        </table>
        	        </fieldset>
            	    <fieldset style="display:none;" id="papers">
                        <legend>ძირითადი ინფორმაცია</legend>
        	            <span class="hide_said_menu">x</span>
        	                '.show_papers_file($res).'
                    </fieldset>
        	        <fieldset style="display:none;" id="documents">
                        <legend>ძირითადი ინფორმაცია</legend>
        	            <span class="hide_said_menu">x</span>
        	                '.show_documents_file($res).'
                    </fieldset>
                </div>
            </fieldset>
        </div>';

	return $data;
}

function show_documents_file($res){
    
    $file_tbale = mysql_query(" SELECT  file.`name`,
                        				file.`rand_name`,
                        				file.`date`,
                        				file.`id`
                                FROM   `client_documents`
                                JOIN    file ON file.id = client_documents.file_id
                                WHERE   client_documents.`client_id` = '$res[id]' AND file.`actived` = 1");
    
    while ($file_res_incomming = mysql_fetch_assoc($file_tbale)){
        $str_file_documents .= '
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_incomming[date].'</div>
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_incomming[name].'</div>
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:28%; float:left;" onclick="download_file(\''.$file_res_incomming[rand_name].'\')">ჩამოტვირთვა</div>
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:8%; float:left;" onclick="delete_file(\''.$file_res_incomming[id].'\',\'client_documents\')">-</div>';
    }
    
   $data = '<div style="margin-top: 15px;">
                <div style="width: 100%; border:1px solid #CCC;float: left;">    	            
                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%; float:left;">თარიღი</div>
            	    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%; float:left;">დასახელება</div>
            	    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:28%; float:left;">ჩამოტვირთვა</div>
                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:8%; float:left;">-</div>
                    <div style="text-align: center; vertical-align:middle; float:left; width: 100%;"><button id="upload_file" style="cursor: pointer;background: none;border: none;width: 100%;height: 25px;padding: 0;margin: 0;">აირჩიეთ ფაილი</button><input style="display:none;" type="file" name="file_name" id="file_name"></div>
                    <div id="paste_files">
                        '.$str_file_documents.'
                    </div>
        	    </div>
            </div>';
    
    return $data;
}

function show_papers_file($res){

    $file_papers = mysql_query("SELECT  file.`name`,
                        				file.`rand_name`,
                        				file.`date`,
                        				file.`id`
                                FROM   `client_papers`
                                JOIN    file ON file.id = client_papers.file_id
                                WHERE   client_papers.`client_id` = '$res[id]' AND file.`actived` = 1");

    while ($file_res_papers = mysql_fetch_assoc($file_papers)){
        $str_file_papers .= '
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_papers[date].'</div>
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_papers[name].'</div>
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:28%; float:left;" onclick="download_file(\''.$file_res_papers[rand_name].'\')">ჩამოტვირთვა</div>
            <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:8%; float:left;" onclick="delete_file(\''.$file_res_papers[id].'\',\'client_papers\')">-</div>';
    }
    
    $data = '<div style="margin-top: 15px;">
                <div style="width: 100%; border:1px solid #CCC;float: left;">
                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%; float:left;">თარიღი</div>
            	    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%; float:left;">დასახელება</div>
            	    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:28%; float:left;">ჩამოტვირთვა</div>
                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:8%; float:left;">-</div>
                    <div style="text-align: center; vertical-align:middle; float:left; width: 100%;"><button id="file_upload" style="cursor: pointer;background: none;border: none;width: 100%;height: 25px;padding: 0;margin: 0;">აირჩიეთ ფაილი</button><input style="display:none;" type="file" name="file_name1" id="file_name1"></div>
                    <div id="paste_files1">
                        '.$str_file_papers.'
                    </div>
        	    </div>
            </div>';

    return $data;
}

?>