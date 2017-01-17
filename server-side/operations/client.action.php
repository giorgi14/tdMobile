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
$ltd_name     = $_REQUEST['ltd_name'];
$ltd_id       = $_REQUEST['ltd_id'];
$client_type  = $_REQUEST['client_type'];

switch ($action) {
	case 'get_add_page':
		$page = GetPage('');
		$data = array('page' => $page);

		break;
	case 'get_edit_page':
		$page = GetPage(GetClient($id));
		$data = array('page' => $page);

		break;
	case 'show_document':
	    $file_type  = $_REQUEST['file_type'];
	    $local_id   = $_REQUEST['local_id'];
	    
	    $page = Getdocumets_page($file_type, $local_id);
	    $data = array('documets_page' => $page);
	
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
                        			   client_loan_agreement.id,
                        			   ''
                                FROM  `client`
	  	                        LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = client.id
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
            insert($local_id, $user_id, $client_type, $name, $surname, $ltd_id, $ltd_name, $born_date, $tin, $tin_number, $tin_date,$mail, $phone, $fact_address, $jur_address, $comment);
        }else{
            update($id_hidden, $user_id, $client_type, $name, $ltd_id, $ltd_name, $surname, $born_date, $tin, $tin_number, $tin_date, $mail, $phone, $fact_address, $jur_address, $comment);
        }
        break;
    case 'get_local_id':
        
        $table_name = $_REQUEST['table_name'];
        
        $res = mysql_fetch_assoc(mysql_query("SELECT `value` FROM increment WHERE `table` = '$table_name'"));
        
        mysql_query("UPDATE `increment` SET `value` = $res[value]+1 WHERE `table` = '$table_name'");
        
        $data = array('local_id' => $res[value]);
        
        break;
    case 'upload_papers':
    
        $local_id = $_REQUEST['local_id'];
    
        $file_tbale = mysql_query("SELECT   file.`name`,
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
    case 'upload_picture':
    
        $local_id         = $_REQUEST['local_id'];
        $str_file_picture = '';
        
        $picture_tbale = mysql_query("SELECT   file.`name`,
                                               file.`rand_name`,
                                               file.`id`
                                      FROM    `car_picture`
                                      JOIN    file ON file.id = car_picture.file_id
                                      WHERE   car_picture.`client_id` = '$local_id' AND file.`actived` = 1");
        
        $check=mysql_num_rows($picture_tbale);
        
        if ($check>0) {
            $str_file_picture .= '<table style="width: 100%;">
                    				<tr style="width: 100%;">';
            $i=0;
            while ($res_picture = mysql_fetch_assoc($picture_tbale)){
                $i = $i+1;
                $str_file_picture .= ' <td style="width: 115px;">
                						  <table style="120px">
                							    <tr style="width: 100%;">
                    							    <td>
                    							         <img width="105" height="85" id="upload_img" src="media/uploads/file/'.$res_picture[rand_name].'" />
                    							    </td>
                							    </tr>
                							    <tr style="width: 100%;">
                    							    <td>
                    							         <a style="margin-left: 20px;" href="#" onclick="view_image('.$res_picture[id].')" class="complate">View</a> | <a href="#" id="delete_image" image_id="'.$res_picture[id].'" class="delete">Delete</a>
                    							    </td>
                							    </tr>
                						    </table>
                						</td>';
                if ($i%7 == 0) {
                   $str_file_picture.='</tr><tr>';
                }
            }
        
            $col = $i%7;
    
            $str_file_picture.=' <td colspan="'.$col.'"></td>
                               </tr>
                             </table>';
        }
        $data = array('str_file_picture' => $str_file_picture);
    
        break;
    case 'delete_image':
    
        $local_id = $_REQUEST['local_id'];
        $image_id = $_REQUEST['image_id'];
        $str_file_picture = '';
        mysql_query("UPDATE `file`
                        SET `actived` = 0
                      WHERE `id`      = $image_id");
        
        mysql_query("UPDATE `car_picture`
                        SET `actived` = 0
                      WHERE `file_id`  = $image_id");
        
        $picture_tbale = mysql_query("  SELECT  file.`name`,
                                                file.`rand_name`,
                                                file.`id`
                                        FROM   `car_picture`
                                        JOIN    file ON file.id = car_picture.file_id
                                        WHERE   car_picture.`client_id` = '$local_id' AND file.`actived` = 1");
        
        $check=mysql_num_rows($picture_tbale);
        
        if ($check>0) {
            $str_file_picture .= '<table style="width: 100%;">
                				<tr style="width: 100%;">';
            $i=0;
            while ($res_picture = mysql_fetch_assoc($picture_tbale)){
                $i = $i+1;
                $str_file_picture .= ' <td style="width: 115px;">
            						      <table style="120px">
            							    <tr style="width: 100%;">
                							    <td>
                							         <img width="105" height="85" id="upload_img" src="media/uploads/file/'.$res_picture[rand_name].'" />
                							    </td>
            							    </tr>
            							    <tr style="width: 100%;">
                							    <td>
                							         <a style="margin-left: 20px;" href="#" onclick="view_image('.$res_picture[id].')" class="complate">View</a> | <a href="#" id="delete_image" image_id="'.$res_picture[id].'" class="delete">Delete</a>
                							    </td>
            							    </tr>
            						    </table>
            						</td>';
                if ($i%7 == 0) {
                    $str_file_picture.='</tr><tr>';
                }
            }
            $str_file_picture.=' <td colspan="'.$col.'"></td>
                                   </tr>
                                 </table>';
        }
        $data = array('str_file_picture' => $str_file_picture);
    
        break;
    case 'view_img':
        $page		= GetIMG($_REQUEST[id]);
        $data		= array('page'	=> $page);
    
        break;
    case 'get_default':
        
        $loan_agreement_type = $_REQUEST['loan_agreement_type'];
        $agreement_type_id   = $_REQUEST['agreement_type_id'];
        
        $res = mysql_fetch_assoc(mysql_query("	SELECT   `default`.percent,
                                    					 `default`.loan_fee,
                                    					 `default`.proceed_fee,
                                    					 `default`.proceed_percent,
                                    					 `default`.rs_message_number,
                                    					 `default`.penalty_days,
                                    					 `default`.penalty_percent,
                                    					 `default`.penalty_additional_percent
                                                FROM     `default`
                                                WHERE    `default`.`agreement_type_id` = $agreement_type_id AND `default`.`loan_type_id` = '$loan_agreement_type'" ));
        $data = array('percent'	=> $res[percent],
                      'loan_fee' => $res[loan_fee],
                      'proceed_fee' => $res[proceed_fee], 
                      'proceed_percent'	=> $res[proceed_percent],
                      'rs_message_number' => $res[rs_message_number],
                      'penalty_days' => $res[penalty_days], 
                      'penalty_percent' => $res[penalty_percent],
                      'penalty_additional_percent' => $res[penalty_additional_percent]);
        break;
   default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function insert($local_id, $user_id, $client_type, $name, $surname, $ltd_id, $ltd_name, $born_date, $tin, $tin_number, $tin_date,$mail, $phone, $fact_address, $jur_address, $comment){
    
    mysql_query("INSERT INTO `client` 
                            (`id`, `user_id`, `datetme`, `type`, `name`, `lastname`, `ltd_id`, `ltd_name`, `born_date`, `pid`, `pid_number`, `pid_date`, `email`, `phone`, `actual_address`, `juridical_address`, `comment`, `actived`) 
                      VALUES 
                            ('$local_id', '$user_id', NOW(), '$client_type', '$name', '$surname', '$ltd_id', '$ltd_name', '$born_date', '$tin', '$tin_number', '$tin_date', '$mail', '$phone', '$fact_address', '$jur_address', '$comment', 1);");
    
    $client_id = mysql_insert_id();
    
    //მინდობილი პირის მონაცემები//
    $client_trust_name         = $_REQUEST['client_trust_name'];
    $client_trust_surname      = $_REQUEST['client_trust_surname'];
    $client_trust_tin          = $_REQUEST['client_trust_tin'];
    $client_trust_phone        = $_REQUEST['client_trust_phone'];
    $client_trust_mail         = $_REQUEST['client_trust_mail'];
    $client_trust_fact_address = $_REQUEST['client_trust_fact_address'];
    $client_trust_jur_address  = $_REQUEST['client_trust_jur_address'];
    $trusting_number           = $_REQUEST['trusting_number'];
    $trusting_date             = $_REQUEST['trusting_date'];
    $trusting_notary           = $_REQUEST['trusting_notary'];
    $trusting_notary_address   = $_REQUEST['trusting_notary_address'];
    $trusting_notary_phone     = $_REQUEST['trusting_notary_phone'];
    
    if ($trusting_number != '' || $trusting_date != '' || $trusting_notary != '' || $trusting_notary_address != '' || $trusting_notary_phone != '' || $client_trust_name != '' || $client_trust_surname != '' || $client_trust_tin != '' || $client_trust_phone != '' || $client_trust_mail != '' || $client_trust_fact_address != '' || $client_trust_jur_address != '') {
        mysql_query("INSERT INTO `client_trusted_person` 
                                (`user_id`, `datetime`, `client_id`, `name`, `lastname`, `pid`, `phone`, `email`, `actual_address`, `juridical_address`, `trusting_number`, `trusting_date`, `trusting_notary`, `trusting_notary_address`, `trusting_notary_phone`, `actived`) 
                          VALUES 
                                ('$user_id', NOW(), '$client_id', '$client_trust_name', '$client_trust_surname', '$client_trust_tin', '$client_trust_phone', '$client_trust_mail', '$client_trust_fact_address', '$client_trust_jur_address', '$trusting_number', '$trusting_date', '$trusting_notary', '$trusting_notary_address', '$trusting_notary_phone', 1)");
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
    
    //ხელშეკრულების მონაცემები//
    $loan_agreement_type        = $_REQUEST['loan_agreement_type'];
    $agreement_type_id          = $_REQUEST['agreement_type_id'];
    $agreement_number           = $_REQUEST['agreement_number'];
    $agreement_date             = $_REQUEST['agreement_date'];
    $loan_amount                = $_REQUEST['loan_amount'];
    $loan_months                = $_REQUEST['loan_months'];
    $pledge_fee                 = $_REQUEST['pledge_fee'];
    $month_percent              = $_REQUEST['month_percent'];
    $monthly_pay                = $_REQUEST['monthly_pay'];
    $rs_message_number          = $_REQUEST['rs_message_number'];
    $pay_day                    = $_REQUEST['pay_day'];
    $exchange_rate              = $_REQUEST['exchange_rate'];
    $penalty_days               = $_REQUEST['penalty_days'];
    $penalty_percent            = $_REQUEST['penalty_percent'];
    $penalty_additional_percent = $_REQUEST['penalty_additional_percent'];
    $insurance_fee              = $_REQUEST['insurance_fee'];
    $loan_fee                   = $_REQUEST['loan_fee'];
    $proceed_fee                = $_REQUEST['proceed_fee'];
    $proceed_percent            = $_REQUEST['proceed_percent'];
    
    
    if ($loan_agreement_type != 0 || $loan_agreement_id != 0 || $agreement_number != '' || $agreement_date != 0 || $loan_amount != '' || $loan_months != '' || $pledge_fee != '' || $month_percent != '' || $monthly_pay != '' || $rs_message_number != '' || $pay_day != '' || $exchange_rate != '' || $proceed_fee!='' || $penalty_days != '' || $penalty_percent != '' || $penalty_additional_percent != '') {
        mysql_query("INSERT INTO `client_loan_agreement` 
                                (`user_id`, `datetime`, `client_id`, `loan_type_id`, `agreement_type_id`, `loan_amount`, `loan_months`, `percent`, `payable_amount`, `monthly_pay`, `penalty_days`, `penalty_percent`, `penalty_additional_percent`, `insurance_fee`, `pledge_fee`, `loan_fee`, `proceed_fee`, `proceed_percent`, `rs_message_number`, `pay_day`, `exchange_rate`, `actived`) 
                          VALUES 
                                ('$user_id', NOW(), '$client_id', '$loan_agreement_type', '$agreement_type_id', '$loan_amount', '$loan_months', '$month_percent', '', '$monthly_pay', '$penalty_days', '$penalty_percent', '$penalty_additional_percent', '$insurance_fee', '$pledge_fee', '$loan_fee', '$proceed_fee', '$proceed_percent', '$rs_message_number', '$pay_day', '$exchange_rate', '1');");
    }
    
}

function update($id_hidden, $user_id, $client_type, $name, $ltd_id, $ltd_name, $surname, $born_date, $tin, $tin_number, $tin_date, $mail, $phone, $fact_address, $jur_address, $comment){
    
    mysql_query("UPDATE `client`
            	    SET `user_id`           = '$user_id',
                        `type`              = '$client_type',
            		    `name`              = '$name',
            		    `lastname`          = '$surname',
                        `ltd_id`            = '$ltd_id',
                        `ltd_name`          = '$ltd_name',
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
    $trusting_number           = $_REQUEST['trusting_number'];
    $trusting_date             = $_REQUEST['trusting_date'];
    $trusting_notary           = $_REQUEST['trusting_notary'];
    $trusting_notary_address   = $_REQUEST['trusting_notary_address'];
    $trusting_notary_phone     = $_REQUEST['trusting_notary_phone'];
    
    $res = mysql_query("SELECT client_id FROM  `client_trusted_person` WHERE  client_trusted_person.client_id = '$id_hidden'");
    
    if (mysql_num_rows($res) == 0) {
        if ($trusting_number != '' || $trusting_date != '' || $trusting_notary != '' || $trusting_notary_address != '' || $trusting_notary_phone != '' || $client_trust_name != '' || $client_trust_surname != '' || $client_trust_tin != '' || $client_trust_phone != '' || $client_trust_mail != '' || $client_trust_fact_address != '' || $client_trust_jur_address != '') {
            mysql_query("INSERT INTO `client_trusted_person`
                                    (`user_id`, `datetime`, `client_id`, `name`, `lastname`, `pid`, `phone`, `email`, `actual_address`, `juridical_address`, `trusting_number`, `trusting_date`, `trusting_notary`, `trusting_notary_address`, `trusting_notary_phone`, `actived`)
                               VALUES
                                    ('$user_id', NOW(), '$id_hidden', '$client_trust_name', '$client_trust_surname', '$client_trust_tin', '$client_trust_phone', '$client_trust_mail', '$client_trust_fact_address', '$client_trust_jur_address', '$trusting_number', '$trusting_date', '$trusting_notary', '$trusting_notary_address', '$trusting_notary_phone', 1)");
        }
   }else{
        mysql_query("UPDATE `client_trusted_person`
                    	SET `user_id`                 = '$user_id',
                    	    `datetime`                =  NOW(),
            			    `name`                    = '$client_trust_name',
            			    `lastname`                = '$client_trust_surname',
            			    `pid`                     = '$client_trust_tin',
            			    `phone`                   = '$client_trust_phone',
            			    `email`                   = '$client_trust_mail',
            			    `actual_address`          = '$client_trust_fact_address',
            			    `juridical_address`       = '$client_trust_jur_address',
                            `trusting_number`         = '$trusting_number',
                            `trusting_date`           = '$trusting_date',
                            `trusting_notary`         = '$trusting_notary',
                            `trusting_notary_address` = '$trusting_notary_address',
                            `trusting_notary_phone`   = '$trusting_notary_phone'
                     WHERE  `client_id`               = '$id_hidden'");
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
    
    
    //ხელშეკრულების მონაცემები//
    $loan_agreement_type        = $_REQUEST['loan_agreement_type'];
    $agreement_type_id          = $_REQUEST['agreement_type_id'];
    $agreement_number           = $_REQUEST['agreement_number'];
    $agreement_date             = $_REQUEST['agreement_date'];
    $loan_amount                = $_REQUEST['loan_amount'];
    $loan_months                = $_REQUEST['loan_months'];
    $pledge_fee                 = $_REQUEST['pledge_fee'];
    $month_percent              = $_REQUEST['month_percent'];
    $monthly_pay                = $_REQUEST['monthly_pay'];
    $rs_message_number          = $_REQUEST['rs_message_number'];
    $pay_day                    = $_REQUEST['pay_day'];
    $exchange_rate              = $_REQUEST['exchange_rate'];
    $penalty_days               = $_REQUEST['penalty_days'];
    $penalty_percent            = $_REQUEST['penalty_percent'];
    $penalty_additional_percent = $_REQUEST['penalty_additional_percent'];
    $insurance_fee              = $_REQUEST['insurance_fee'];
    $loan_fee                   = $_REQUEST['loan_fee'];
    $proceed_fee                = $_REQUEST['proceed_fee'];
    $proceed_percent            = $_REQUEST['proceed_percent'];
    
    $res_car = mysql_query("SELECT client_id FROM  `client_loan_agreement` WHERE  client_loan_agreement.client_id = '$id_hidden'");
    
    if (mysql_num_rows($res_car) == 0) {
        if ($loan_agreement_type != 0 || $loan_agreement_id != 0 || $agreement_number != '' || $agreement_date != 0 || $loan_amount != '' || $loan_months != '' || $pledge_fee != '' || $month_percent != '' || $monthly_pay != '' || $rs_message_number != '' || $pay_day != '' || $exchange_rate != '' || $proceed_fee!='' || $penalty_days != '' || $penalty_percent != '' || $penalty_additional_percent != '') {
            mysql_query("INSERT INTO `client_loan_agreement` 
                                    (`user_id`, `datetime`, `client_id`, `loan_type_id`, `agreement_type_id`, `loan_amount`, `loan_months`, `percent`, `payable_amount`, `monthly_pay`, `penalty_days`, `penalty_percent`, `penalty_additional_percent`, `insurance_fee`, `pledge_fee`, `loan_fee`, `proceed_fee`, `proceed_percent`, `rs_message_number`, `pay_day`, `exchange_rate`, `actived`) 
                              VALUES 
                                    ('$user_id', NOW(), '$id_hidden', '$loan_agreement_type', '$agreement_type_id', '$loan_amount', '$loan_months', '$month_percent', '', '$monthly_pay', '$penalty_days', '$penalty_percent', '$penalty_additional_percent', '$insurance_fee', '$pledge_fee', '$loan_fee', '$proceed_fee', '$proceed_percent', '$rs_message_number', '$pay_day', '$exchange_rate', '1');");
        }
    }else{
        mysql_query("UPDATE  `client_loan_agreement`
                    	SET  `user_id`                    = '$user_id',
                			 `loan_type_id`               = '$loan_agreement_type',
                             `agreement_type_id`          = '$agreement_type_id',
                			 `loan_amount`                = '$loan_amount',
                			 `loan_months`                = '$loan_months',
                			 `percent`                    = '$month_percent',
                			 `monthly_pay`                = '$monthly_pay',
                			 `penalty_days`               = '$penalty_days',
                			 `penalty_percent`            = '$penalty_percent',
                			 `penalty_additional_percent` = '$penalty_additional_percent',
                			 `insurance_fee`              = '$insurance_fee',
                			 `pledge_fee`                 = '$pledge_fee',
                			 `loan_fee`                   = '$loan_fee',
                			 `proceed_fee`                = '$proceed_fee',
                             `proceed_percent`            = '$proceed_percent',
                			 `rs_message_number`          = '$rs_message_number',
                			 `pay_day`                    = '$pay_day',
                			 `exchange_rate`              = '$exchange_rate'
                     WHERE   `client_id`                  = '$id_hidden'");
    }
}
function spellNumber($number) {

    if ($number > 0 && $number <= 20) {

        switch ($number){

            case 1: $text = "ერთი"; break;
            case 2: $text = "ორი"; break;
            case 3: $text = "სამი"; break;
            case 4: $text = "ოთხი"; break;
            case 5: $text = "ხუთი"; break;
            case 6: $text = "ექვსი"; break;
            case 7: $text = "შვიდი"; break;
            case 8: $text = "რვა"; break;
            case 9: $text = "ცხრა"; break;
            case 10: $text = "ათი"; break;
            case 11: $text = "თერთმეტი"; break;
            case 12: $text = "თორმეტი"; break;
            case 13: $text = "ცამეტი"; break;
            case 14: $text = "თოთხმეტი"; break;
            case 15: $text = "თხუთმეტი"; break;
            case 16: $text = "თექვსმეტი"; break;
            case 17: $text = "ჩვიდმეტი"; break;
            case 18: $text = "თვრამეტი"; break;
            case 19: $text = "ცხრამეტი"; break;
            case 20: $text = "ოცი"; break;

        }

    }elseif ($number > 20 && $number <= 100){

        if ($number%10 == 0) {

            switch (intval($number/10)){

                case 3 : $mtelitext = "ოცდაათი"; break;
                case 4 : $mtelitext = "ორმოცი"; break;
                case 5 : $mtelitext = "ორმოცდაათი"; break;
                case 6 : $mtelitext = "სამოცი"; break;
                case 7 : $mtelitext = "სამოცდაათი"; break;
                case 8 : $mtelitext = "ოთხმოცი"; break;
                case 9 : $mtelitext = "ოთხმოცდაათი"; break;
                case 10 : $mtelitext = "ასი"; break;

            }

            $text = $mtelitext;

        }else{

            $nawili = $number%20;
            $mteli  = intval($number/10);

            switch ($mteli){

                case 2 :
                case 3 : $mtelitext = "ოცდა"; break;
                case 4 :
                case 5 : $mtelitext = "ორმოცდა"; break;
                case 6 :
                case 7 : $mtelitext = "სამოცდა"; break;
                case 8 :
                case 9 : $mtelitext = "ოთხმოცდა"; break;

            }

            $text = $mtelitext.spellNumber($nawili);

        }
         

    }elseif ($number > 100 && $number <= 1000){

        if ($number%100 == 0) {

            switch (intval($number/100)) {
                case 1 : $mtelitext = "ასი"; break;
                case 2 : $mtelitext = "ორასი"; break;
                case 3 : $mtelitext = "სამასი"; break;
                case 4 : $mtelitext = "ოთხასი"; break;
                case 5 : $mtelitext = "ხუთასი"; break;
                case 6 : $mtelitext = "ექვსასი"; break;
                case 7 : $mtelitext = "შვიდასი"; break;
                case 8 : $mtelitext = "რვაასი"; break;
                case 9 : $mtelitext = "ცხრაასი"; break;
                case 10 : $mtelitext = "ათასი"; break;
            }

            $text = $mtelitext;

        }else{

            $mteli  = intval($number/100);
            $nawili = $number%100;

            switch ($mteli) {
                case 1 : $mtelitext = "ას"; break;
                case 2 : $mtelitext = "ორას"; break;
                case 3 : $mtelitext = "სამას"; break;
                case 4 : $mtelitext = "ოთხას"; break;
                case 5 : $mtelitext = "ხუთას"; break;
                case 6 : $mtelitext = "ექვსას"; break;
                case 7 : $mtelitext = "შვიდას"; break;
                case 8 : $mtelitext = "რვაას"; break;
                case 9 : $mtelitext = "ცხრაას"; break;
            }

            $text = $mtelitext.spellNumber($nawili);

        }

    }elseif ($number > 1000 && $number < 1000000){

        $mteli = intval($number/1000);
        $nawili = $number%1000;

        if ($mteli == 1) {
            $text = 'ათას'.spellNumber($nawili);
        }else{
            if ($nawili%1000 == 0) {
                $text = spellNumber($mteli).'ათასი';
            }else{
                $text = spellNumber($mteli).'ათას'.spellNumber($nawili);
            }

        }

    }

    return $text;

}




function GetIMG($id){
    $res = mysql_fetch_array(mysql_query("SELECT rand_name FROM `file` WHERE id = $id"));
    if (empty($res[0])) {
        $image = '0.jpg';
    }else{
        $image = $res[0];
    }
    $data = '<div id="dialog-form">
	           <fieldset>
                <img style="margin: auto;display: block;" width="350" height="350"  src="media/uploads/file/'.$image.'">
               </fieldset>
             </div>
            ';

    return $data;
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

function loan_type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   loan_type");

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

function agreement_type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   agreement_type");

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

function Getdocumets_page($file_type, $local_id){
    
    
    $res = mysql_fetch_assoc(mysql_query("SELECT  client.id,
                                                 `month`.`name` AS `month`,
                                                 `month`.`name1` AS `month1`,
                                                 `month`.`name2` AS `month2`,
                                                  DATE_FORMAT(client.datetme,'%m') AS `month_id`,
                                        		  DATE_FORMAT(client.datetme,'%Y') AS `year`,
                                                  DATE_FORMAT(client.datetme,'%d') AS `day`,
                                    			  CONCAT(client.`name`, ' ', client.lastname) AS `name`,
                                    			  client.pid,
                                    			  client.pid_date,
                                    			  client.pid_number,
                                    			  client.born_date,
                                    			  client.actual_address,
                                    			  client.juridical_address,
                                    			  client.phone,
                                                  client.email,
                                    			  CONCAT(client_trusted_person.`name`,' ',client_trusted_person.lastname) AS trust_pers,
                                    			  client_trusted_person.actual_address AS trusted_actual_address,
                                    			  client_trusted_person.juridical_address AS trusted_juridical_address,
                                                  client_trusted_person.pid AS trusted_pid,
                                        		  client_trusted_person.phone AS trusted_phone,
                                                  client_trusted_person.email AS trusted_email,
                                                  client_loan_agreement.id AS agreement_id,
			                                      client_loan_agreement.loan_amount,
                                                  client_loan_agreement.loan_months,
                                                  client_loan_agreement.percent,
                                                  client_loan_agreement.monthly_pay,
                                                  client_loan_agreement.id AS loan_agreement_id,
                                                  client_loan_agreement.loan_type_id AS loan_type_id,
                                                  client_loan_agreement.penalty_days AS penalty_days,
                                                  client_loan_agreement.penalty_percent AS penalty_percent,
                                                  client_loan_agreement.penalty_additional_percent AS penalty_additional_percent,
                                                  client_car.model,
                                    			  client_car.car_id,
                                    			  client_car.manufacturing_date,
                                    			  client_car.color,
                                    			  client_car.registration_number,
                                                  car_type.`name` AS car_type_name,
                                                  client_car.engine_size,
                                    			  client_car.certificate_id,
                                                  GROUP_CONCAT(CONCAT(client_person.phone,' ',client_person.person)) AS client_person_person
                                         FROM    `client`
                                         JOIN    `month` ON `month`.id = DATE_FORMAT(client.datetme,'%m')
                                         LEFT JOIN client_trusted_person ON client_trusted_person.client_id = client.id
                                         LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = client.id
                                         LEFT JOIN client_car ON client_car.client_id = client.id
                                         LEFT JOIN client_person ON client_person.client_id = client.id
                                         LEFT JOIN car_type ON car_type.id = client_car.type_id
                                         WHERE client.id = '$local_id'"));
    
    if ($file_type == 'receipt') {
        $data  .= ' <div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                         <div style="width:100%; text-align: center; font-size: 18px;">ხელწერილი</div>
                         <div style="width:100%; text-align: center; font-size: 16px; margin-top: 20px;">შედგენილი '.$res[year].' წლის '.$res[day].'  '.$res[month].'</div>
                         <div style="width:100%; font-size: 12px; margin-top: 30px;">
                           <a style="margin-left:30px;">წინამდებარე</a> ხელწერილით მე, '.$res[name].' (piradi # '.$res[pid].')  პირადობის მოწმობა No: 
                           '.$res[pid_number].' გაცემული: იუსტიციის სამინისტროს მიერ; '.$res[pid_date].'  წელს; დაბადებული: '.$res[born_date].'  წელს.; 
                                                                მცხოვრები: '.$res[actual_address].'; '.$res[juridical_address].' 
                                                                საკონტაქტო ტელეფონის ნომერი :  '.$res[phone].') ვადასტურებ, რომ გავეცანი სს “სადაზღვევო კომპანია ალდაგი”-ს, 
                                                                როგორც მზღვეველსა და შ.პ.ს. "თი ჯი მობაილი"–ს  როგოც დამზღვევს შორის 2015 წლის 19 მაისს  
                                                                გაფორმებულ გენერალურ ხელშეკრულებას ავტოსატრანსპორტო საშუალებების დაზღვევის შესახებ, შესაბამისად, 
                                                                ჩემთვის ცნობილი და გასაგებია  ამ ხელშეკრულების პირობები და ყველა ის ვალდებულება და პასუხისმგებლობა, 
                                                                მათ შორის ხელშეკრულებით გათვალისწინებულ შემდეგ შემთხვევებში მზღვეველის მიერ ჩემს მიმართ რეგრესის გამოყენების შესაძლებლობა
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 30px; padding: 0px 30px;">
                            1.	თუ დაზღვეული ავტომობილი სამართავად გადაეცემა მართვის უფლებამოსილების არმქონე პირს და/ან არაუფლებამოსილ მძღოლს და ასეთ დროს დადგება შემთხვევა;
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 30px; padding: 0px 30px;">
                            2.	შემთხვევაზე, რომელიც მოხდა მძღოლის ალკოჰოლური ან ნარკოტიკული (ფსიქოტროპული) ნივთიერებების ზემოქმედების ქვეშ ყოფნის დროს
                                (მიუხედავად იმისა, ზარალი დაზღვეული ავტომაქნანის უფლებამოსილი მძღოლის მიზეზით/ბრალით დადგა, თუ სხვა მესამე პირის მიზეზით/ბრალით);
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 30px; padding: 0px 30px;">
                            3.	მძღოლის თვითმკვლელობას ან თვითმკვლელობის მცდელობას ან ისეთ განზრახ ქმედებას, რომელიც მიზნად ისახავდა შემთხვევით გამოწვეული შედეგის დადგომას;
                        </div>
                
                        <div style="width:100%; font-size: 12px; margin-top: 30px;">
                                                                ზემოხსენებულის სისწორეს ვადასტურებ ხელმოწერით:
                        </div>
                        <div style="width:100%; font-size: 14px; margin-top: 30px;">
                            <table style="width:100%;">
                                <tr style="width:100%;">
                                    <td style="width:30%;"><label>-----------------------------------------------</label></td>
                                    <td style="width:70%;"><label>---------------------------</label></td>
                                </tr>
                                <tr style="width:100%;">
                                    <td style="width:30%;"><label style="padding: 0px 30px; font-size: 12px;">(სახელი, გვარი)</label></td>
                                    <td style="width:70%;"><label></label></td>
                                </tr>
                            </table>
                        </div>
                   </div';
    }else if ($file_type == 'acceptance_act'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                         <div style="width:100%; text-align: center; font-size: 18px;">მიღება–ჩაბარების აქტი # 1/'.$res[loan_agreement_id].'</div>
                         <div style="width:100%; margin-top: 60px;  padding: 0px 30px;">
                            <table style="width:100%;">
                                <tr style="width:100%;">
                                    <td style="width:60%;"><label style="font-size: 14px;">ქ.თბილისი</label></td>
                                    <td style="width:40%;"><label style="font-size: 14px;">'.$res[day].' '.$res[month].' '.$res[year].'  წ.</label></td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 50px;">
                           <a style="margin-left: 25px;"> ჩვენ, </a>ქვემოთ ხელმომწერნი: ერთის მხვრივ, შპს ”თი ჯი მობაილი”-ი (ს/კ 205270277),მისი დირექტორი გიორგი კილაძე,
                                                                წარმოდგენილი ვახტანგ ბახტაძის სახით (მინდობილობა #001 12.06.2012 წელი) "გამსესხებელი"  
                                                                და მეორეს მხვრივ ფიზიკუირი პირი '.$res[name].' (piradi # '.$res[pid].') "მსესხებელი" 
                                                                ვაწერთ ხელს შემდგომზე, რომ „მსესხებელს“ გადაეცა '.$res[year].' წლის '.$res[day].' '.$res[month1].'   დადებული 
                                                                სალომბარდო მომსახურების ხელშეკრულების # '.$res[loan_agreement_id].'  საფუძველძე გასესხებული  თანხა  '.$res[loan_amount].' ('.spellNumber($res[loan_amount]).') ლარის ოდენობით.
                        </div>
                        <div style="width:100%; margin-top: 60px;">
                            <table style="width:100%;">
                                <tr style="width:100%;">
                                    <td style="width:32%;"><label style="font-size: 12px; text-align: center;">გამსესხებელი:<br>შპს ”თი ჯი მობაილი”<br>ს/კ  205270277<br>მის. ქ.თბილისი,დოლიძის ქ.25/121</label></td>
                                    <td style="width:65%;"><label style="font-size: 12px; text-align: center;">მსესხებელი:<br>'.$res[name].'<br>პ/ნ  '.$res[pid].'<br>'.$res[actual_address].'<br>'.$res[juridical_address].'</label></td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:100%; margin-top: 60px;">
                            <table style="width:100%;">
                                <tr style="width:100%;">
                                    <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:100%; margin-top: 30px;">
                            <table style="width:100%;">
                                <tr style="width:100%;">
                                    <td style="width:50%;"><label style="font-size: 12px;">მენეჯერი  --------------------------------- /ვ.ბახტაძე/<label></td>
                                    <td style="width:50%;"><label style="font-size: 12px;"> --------------------------------- /'.$res[name].'/</label></td>
                                </tr>
                            </table>
                        </div>
                        
                   </div';
    }elseif ($file_type == 'Client_car_confiscation'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                         <div style="width:100%; font-size: 12px;">ვისგან: შპს "თი ჯი მობაილ"<br>მის: ქ. თბილისი , კერესელიძის ქ. შეს.1, # 12, 2/4<br>ს/ნ – 205270277<br>ტელ.: -579796921, 577768127</div>
                         <div style="width:100%; font-size: 12px; text-align: right;">ვის: Qბ-ნ/ '.$res[name].' <br> '.$res[actual_address].'  </div>
                         <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">პ/ნ  '.$res[pid].' <br> ტელ.: '.$res[client_person_person].' <br> ელ.ფოსტა: '.$res[pid].' </div>
                         <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">მინდობილი პირი ბ-ნ: Qბ-ნ/ '.$res[trust_pers].' <br> მის.: '.$res[trusted_actual_address].' <br> '.$res[trusted_juridical_address].'</div>
                         <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">პ/ნ '.$res[trusted_pid].' <br> ტელ.: '.$res[trusted_phone].' <br> ელ.ფოსტა: '.$res[trusted_email].' </div>
                         
                         <div style="width:100%; font-size: 12px; margin-top: 15px;">
                            <a style="margin-left: 25px;"> Qბ-ნ </a> '.$res[trust_pers].' წინამდებარე წერილით შეგახსენებთ, ბ-ნ '.$res[name].' (პირადი #'.$res[pid].') და (შემდგომში მინდობილი პირი) 
                            '.$res[trust_pers].' (პირადი # '.$res[trusted_pid].') რომ '.$res[year].' წლის '.$res[day].' '.$res[month1].' ჩვენს კომპანიასა და თქვენს შორის მინდობილობის N140655779, საფუძველზე 
                                                                დადებულია საკრედიტო და გირავნობის ხელშეკრულება № '.$res[agreement_id].', რომლის შესაბამისადაც თქვენს სახელზე გაცემულია თანხა '.$res[loan_amount].' აშშ დოლარის ექვივალენტი ლარში ოდენობით.
                         </div>
                         <div style="width:100%; font-size: 12px;">
                                                                რომლის თანახმადაც თქვენს მიერ ყოველთვიურად, შესაბამისი თვის  '.$res[day].' რიცხვში გადახდილი უნდა ყოფილიყო ყოველთვიური სარგებელი ('.$res[monthly_pay].' აშშ დოლარი) ჩვენს შორის დადებული სესხის ხელშეკრულების 3.8 
                                                                მუხლის შესაბამისად. ზემოაღნიშნული ვალდებულება მის მხრიდან დაირღვა '.$res[year].' წლის '.$res[day].' '.$res[month2].', რის შედეგადაც თქვენი დავალიანება შპს "თი ჯი მობაილი"- ს წინაშე შეადგენს პროცენტი - '.$res[monthly_pay].'
                                                               აშშ დოლარს (ექვივალენტი ლარში), ჯარიმა - ? აშშ დოლარი (ექვივალენტი ლარში) ყოველ გადაცილებულ დღეში. ძირი თანხა - '.$res[loan_amount].' აშშ დოლარი.
                                                                გამომდინარე იქიდან, რომ გირავნობის საგანი -  სატრანსპორტო საშუალება მოდელი – '.$res[model].', ტრანსპორტის საიდენტიფიკაციო # '.$res[car_id].'; გამოშვების წელი - '.$res[manufacturing_date].', 
                                                               ფერი – '.$res[color].', სახელმწიფო სანომრე ნიშნით  – '.$res[registration_number].' არის '.$res[name].'ს საკუთრებაში მოვითხოვთ დაუყოვნებლივ გადმოგვცეთ მფლობელობაში
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                            <a style="margin-left: 25px;"> საქართველოს </a> სამოქალაქო კოდექსის 281-ე მუხლის პირველი ნაწილის შესაბამისად თქვენ ვალდებული ხართ დაუყოვნებლივ დააკმაყოფილოთ ჩვენი მოთხოვნა გირავნობის საგნის მფლობელობაში გადმოცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; ">
                            <a style="margin-left: 25px;"> მოთხოვნის </a> ორი კვირის ვადაში დაუკმაყოფილებლობის შემთხვევაში, საქართველოს სამოქალაქო კოდექსის 2581-ე მუხლის შესაბამისად მივმართავთ სსიპ 
                                                                 საქართველოს შინაგან საქმეთა სამინისტროს მომსახურების სააგენტოს გირავნობის მოწმობის გასაცემად. განგიმარტავთ, რომ გირავნობის მოწმობა არის აღსრულების ქვემდებარე აქტი, 
                                                                 რომლითაც საქართველოს კანონმდებლობით დადგენილი გარემოებების არსებობისას მოგირავნეს უფლება აქვს მოსთხოვოს უფლებამოსილ ორგანოს (თანამდებობის პირს) 
                                                                 გირავნობით უზრუნველყოფილი მოთხოვნის დაკმაყოფილების მიზნით დაგირავებული ნივთის მის მფლობელობაში გადაცემა.
                        </div>
                        <div style="width:100%; font-size: 12px; ">
                            <a style="margin-left: 25px;"> ამასთან  </a> საქართველოს სამოქალაქო კოდექსის 282-ე მუხლის პირველი ნაწილის შესაბამისად გატყობინებთ, 
                                                                 რომ ზემოთ აღნიშნული სატრანსპორტო საშუალების შპს "თი ჯი მობაილ"-ზე გადმოცემის შემდეგ საქართველოს სამოქალაქო კოდექსის 283-ე 
                                                                 მუხლის პირველი ნაწილისა და '.$res[year].' წლის '.$res[day].' '.$res[month1].'  № '.$res[loan_agreement_id].' საკრედიტო და გირავნობის ხელშეკრულების მე-7 მუხლის მე-7.1.3 
                                                                 პუნქტის შესაბამისად შპს "თი ჯი მობაილ"-ის მიერ განხორციელდება სატრანსპორტო საშუალების განბაჟება, აღრიცხვიდან მოხსნა (ჩამოწერა),  
                                                                 რეექსპორტი, ექსპორტი, რეალიზაცია პირდაპირი მიყიდვის გზით ან საქართველოს სამოქალაქო კოდექსის 2601 მუხლის და 2016 წლის  12 
                                                                 დეკემბერს  № '.$res[loan_agreement_id].'   საკრედიტო და გირავნობის ხელშეკრულების მე-7 მუხლის მე-7.1.2 პუნქტის შესაბამისად სატრანსპორტო საშუალება გადმოვა 
                                                                 შპს "თი ჯი მობაილ"-ის საკუთრებაში.
                        </div>
                        <div style="width:100%; margin-top: 60px;">პატივისცემით,</div>
                        <div style="width:100%; text-align: right;">/დირექტორი გიორგი კილაძე/</div>
                   </div';
    }elseif ($file_type == 'approval'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                         <div style="width:100%; font-size: 16px; text-align: center">თანხმობა</div>
                         <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'  '.$res[month].' '.$res[year].'წ</div>
                         <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                         
                         <div style="width:100%; font-size: 12px; margin-top:15px">
                           <a style="margin-left: 25px;"> მე, </a>  '.$res[name].' (პირადი # '.$res[pid].'), ვადასტურებ,  რომ შპს "თი ჯი მობაილისგან" '.$res[day].'.'.$res[month_id].'.'.$res[year].' წელს ავიღე სესხი. 
                                                               ამავე დღეს ხელი მოვაწერე სალომბარდო მომსახურების ხელშეკრულებას და ვადასტურებ რომ მასში მითითებულ ყველა პირობას ვეთანხმები.
                        </div>
                               
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                             ხელშეკრულების გაფორმების შემდგომ გადასახდელი მექნება :
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 10px;">
                            <a style="margin-left: 25px;"> 1. </a> დაზღვევის საფასური ყოველ სამ თვეშ ერთხელ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 10px;">
                            <a style="margin-left: 25px;"> 2. </a>წინსრების  დაფარვის საკომისიო - სესხის ძირითადი თანხის 3%.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 10px;">
                            <a style="margin-left: 25px;"> 3. </a>ხელშეკრულებით N '.$res[loan_agreement_id].' გათვალისწინებული გადახდის ვადის გადაცილების შემთხვევაში, 
                                                                                                                                       დაერიცხება პირგასამტეხლო სესხის ძირი თანხის '.$res[penalty_percent].'%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან '.$res[penalty_days].' დღის განმავლობაში;
                                                                                                                                       ხოლო გადახდის ვადის გადაცილების '.$res[penalty_days].' ('.spellNumber($res[penalty_days]).') დღეზე მეტი ვადით გაგრძელების შემთხვევაში
                                                                                                                                       მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის '.$res[penalty_additional_percent].'% ყოველ ვადაგადაცილებულ დღეზე.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 10px;">
                            <a style="margin-left: 25px;"> ვეთანხმები </a>ვეთანხმები აღნიშნულ პირობებს და მასზე რაიმე პრეტენზია არ მექნება.
                        </div>
                        <div style="width:100%; margin-top: 100px; text-align: right;">'.$res[name].'  ___________________</div>
                        
                   </div';
    }elseif ($file_type == 'receipt_insurance'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                         <div style="width:100%; font-size: 16px; text-align: center">ხელწერილი</div>
                         <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'  '.$res[month_id].' '.$res[year].'</div>
                         <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                         
                         <div style="width:100%; font-size: 12px; margin-top: 15px;">
                           <a style="margin-left: 25px;"> მე, </a>  '.$res[name].' (პირადი # '.$res[pid].'), "მსესხებელი", ვაცხადებ თანხმობას, რომ შპს "თი ჯი მობაილმა"(ს/კ 205270277)  '.$res[loan_amount].' აშშ დოლარად (ექვივალენტი ლარში) 
                                                                დააზღვიოს ჩემს საკუთრებაში არსებული ავტომობილი, რაზედაც პრეტენზია არ გამაჩნია. ავტომობილის მონაცემებია:
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                           მარკა, მოდელი: '.$res[model].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                             გამოშვების წელი:'.$res[manufacturing_date].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                             ფერი: '.$res[color].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                             ტიპი : '.$res[car_type_name].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ძრავის მოსცულობა : '.$res[engine_size].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            სარეგისტრაციო ნომერი : '.$res[registration_number].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                           მესაკუთრე : '.$res[name].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტრანსპორტის საიდენთიპიკაციო ნომერი : '.$res[registration_number].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტრანსპორტის სარეგისტრაციო მოწმობა : '.$res[certificate_id].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 10px;">
                                                             იმ შემთხვევაში, თუ დაზიანდა ჩემს საკუთრებაში არსებული ზემოაღნიშნული ავტომობილი,ხოლო სადაზღვევო კომპანიის მიერ ჩარიცხული 
                                                           თანხა არ იქნება საკმარისი ზარალის სრულად ასანაზღაურებლად, ვიღებ ვალდებულებას საკუთარი ხარჯებით შევაკეთო გირავნობის საგანი  
                                                           და ავტომობილი მოვიყვანო გირავნობით დატვირთვის დროისათვის არსებულ მდგომარეობაში.
                        </div>
                        <div style="width:100%; margin-top: 100px; text-align: left;">მსესხებელი: ___________________</div>
                        
                   </div';
    }elseif ($file_type == 'guarantee'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                         <div style="width:100%; font-size: 16px; text-align: center">სოლიდარული პასუხისმგებლობის ხელშეკრულება</div>
                         <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                         <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                         <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                             მხარეები:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                          ერთის მხრივ, "თი ჯი მობაილ" (საიდენტიპიკაციო  #205270277), წარმოდგენილი მისი დირექტორის  გიორგი კილაძის წარმომადგენლის ვახტანგ ბახტაძის სახით 
                             (მინდობილობა #001, 10.05.2012 წელი) (შემდგომში გამსესხებელი), მეორეს მხრივ '.$res[name].'(პირადი #'.$res[pid].'), შემდგომში "თავდები" და '.$res[trust_pers].' (პირადი # '.$res[trusted_pid].'), შემდგომში "მსესხებელი", შევთანხმდით და ვდებთ წინამდებარე ხელშეკრულებას შემდეგზე:
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align:center;">
                        1.ხელშეკრულების საგანი:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        1.1 წინამდებარე თავდებობის ხელშეკრულებით თავდები შპს "თი ჯი მობაილ"-ის წინაშე იკისრა შემდეგი 
                                                         ვალდებულება- იმ შემთხვევაში თუ მსესხებელი დაარღვევს შპს "თი ჯი მობაილ"-სა და '.$res[trust_pers].' შორის '.$res[year].' წლის '.$res[day].' 
                        '.$res[month1].' გაფორმებულ საკრედიტო ხელშეკრულებას N '.$res[loan_agreement_id].' ხელშეკრულებას - დაფაროს მსესხებლის ნაკისრი ყველა ვალდებულება 
                        (როგორც სესხის ძირი თანხა, ასევე მასზე დარიცხული სარგებელი, პირგასამტეხლო ყოველ ვადაგადაცილებული დღისათვის 
                                                         და გამსესხებლის მიერ გაწეული ყველა ხარჯი, რომელიც პირდაპირ თუ არაპირდაპირ გათვალისწინებულია '.$res[day].' '.$res[month1].' '.$res[year].' წლის N '.$res[loan_agreement_id].' საკრედიტო ხელშეკრულებით).
                        </div>
                        <div style="width:100%; font-size: 12px;">
                         1.2.წინამდებარე თავდებობის ხელშეკრულებით თავდებმა იკისრა სოლიდარული პასუხისმგებლობა და მას შეიძლება 
                                                            წაეყენოს მოთხოვნა იძულებითი აღსრულების მცდელობის გარეშეც, თუ ძირითადმა მოვალემ გადააცილა გადახდის ვადას 
                                                             და უშედეგოდ იქნა გაფრთხილებული, ან და მისი გადახდისუუნარობა აშკარაა ან/და კანონით გათვალისწინებულ სხვა შემთხვევებში 
                          (მსესხებელსა და თავდებს მოთხოვნა შეიძლება წაეყენოთ ერთსა და იმავე დროს).
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        1.3.თავდებობის მაქსიმალური თანხა შეადგენს 10970 ლარს, რომელშიც შედის ძირითადი ვალი და თანხის 
                                                         ამოღებისათვის გაწეული ხარჯები, მათ შორის სასამართლო და სხვა აუცილებელი ხარჯები.
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        1.6.წინამდებარე თავდებობის ხელშეკრულება შეწყდება თუ თავდები ან მესამე პირი შეასრულებენ თავიანთ ვალდებულებებს.
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        1.7იმ შემთხვევაში თუ ძირითად მამოვალემ დაკარგა ქმედუნარიანობა, ან გახდა შეზღუდულ ქმედუნარიანი, 
                                                        ან გარდაიცვალა ძირითადი მოვალე თავდები არ თავისუფლდება აღებული პასუხიმგებლობისაგან;
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align:center;">
                        2. მხარეთა ვალდებულებანი:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          2.1 თავდები იღებს ვალდებულებას კრედიტორის წინაშე მსესხებელთან ერთად, იმავე მოცულობით სოლიდარულად იყოს პასუხიმგებელი
                                                             ამ უკანასკნელის მიერ ნაკისრი ვალდებულებისათვის. მათ შორის მსესხებლის მიერ ვალდებულების შეუსრულებლობის შემთხვევაში 
                                                             ძირითადი ვალის_ფულადი თანხის დაბრუნებისა და საჯარიმო სანქციების ჩათვლით;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                         2.2.  თავდების პასუხიმგებლობის საფუძველს წარმოადგენს, კერძოდ:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          2.2.1.მსესხებლის მიერ ნაკისრი ვალდებულებების შეუსრულებლობა ხელშეკრულებით გათვალისწინებული პირობებით; 
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          2.3. მსესხებლის მიერ ვალდებულების შესრულების ვადის გადაცილებისას, გამსესხებელი ვალდებულია ამის თაობაზე შეატყობინოს თავდებს. 
                                                             კრედიტორს უფლება აქვს მოითხოვოს ვალდებულების შესრულება ერთსა და იმავე დროს, როგორც მსესხებლისგან, ისე თავდებისაგან;
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align:center;">
                        3.ხელშეკრულების ძალაში შესვლა, ხელშეკრულების შეცვლა:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          3.1.წინამდებარე ხელშეკრულება ძალაში შედის მხარეების მიერ ხელმოწერისთანავე და მოქმედებს მსესხებლის ან/და 
                                                             თავდების მიერ ვალდებულების სრულად შესრულებამდე.
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          3.1.წინამდებარე ხელშეკრულება ძალაში შედის მხარეების მიერ ხელმოწერისთანავე და მოქმედებს მსესხებლის ან/და 
                                                             თავდების მიერ ვალდებულების სრულად შესრულებამდე.
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          3.2.წინამდებარე ხელშეკრულებასთან დაკავშირებული ნებისმიერი ცვლილება და დამატება ძალაში შედის თუ ის გაფორმებულია წერილობით და ხელმოწერილი იქნება მხარეების მიერ. 
                                                             ნებისმიერი ზეპირი შეთანხმება, რომელიც დაკავშირებული იქნება სასესხო ხელშეკრულებასთან ჩაითვლება იურიდიული ძალის არმქონედ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align:center;">
                        4. ფორს-მაჟორი:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          4.1.მხარეები თავისუფლდებიან ხელშეკრულებით განსაზღვრული ვალდებულების შესრულებისაგან თუ ისგამოწვეულია ფორს მაჟორული გარემოების შედეგად. 
                          (წყალდიდობით, მიწისძვრით, გაფიცვით, სამხედრო მოქმედებით, ბლოკადით, სახელმწიფო ორგანოს აქტით და ა.შ)
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          4.2.ფორსმაჟორული გარემოებისდადგომისშემთხვევაში, მხარემ რომლისთვისაც შეუძლებელი ხდება ნაკისრი ვალდებულების შესრულება, 
                                                             დაუყოვნებლივ უნდა გაუგზავნოს მეორე მხარეს წერილობითი შეტყობინება ასეთი გარემოების და მისი გამოწვევი მიზეზების შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          4.3. იმ შემთხვევაში თუ ფორსმაჟორული გარემოების ხანგრძლივობა 30 (ოცდაათი) დღეზე მეტხანს გაგრძელდა ან მისი დადგომისთანავე ცხადი გახდა,
                                                             რომ მოქმედება 30 (ოცდაათი) დღეზე მეტხანს გაგრძელდება მხარეები წყვეტენ ხელშეკრულების შეწყვეტის მიზანშეწონლობის საკითხს. ხელშეკრულების ამგვარი მოშლის დროს მხარეები 5 
                          (ხუთი) დღის განმავლობაში ასწორებენ ყველა ადრე არსებულ ვალებულებებს ერთმანეთის მიმართ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align:center;">
                        5. დავათა გადაჭრის წესი
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          5.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილი ნებისმიერ დავას 
                                                                    განიხილავს თბილისის საქალაქო სასამართლო.
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          5.2. პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება უნდა მიექცეს 
                                                            დაუყონებლივ აღსასრულებლად საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align:center;">
                        6. საკონტაქტო ინფორმაცია
                        </div>
                        <div style="width:100%; font-size: 12px;">
                         6.1 მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისმართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          6.2. თავდები აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითიტებულ მისამართზე ან ელ/ფოსტებზე;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          6.3. მხარეები თანხმდებიან, ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ თავდების მისმართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align:center;">
                        7. დამატებითი დებულებები
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        7.1.წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების
                                                                    ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, 
                                                                     ისინი ეთანხმებიან ამ პირობებს და სურთ ხლშეკრულების დადება აღნიშნული პირობებით;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                         7.2. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ
                                                                     ამ ხელშეკრულებით გატვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          7.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათილობა არ იწვევს ხელშეკრულების ბათილობას;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          7.4.ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება  ამ უფლებაზე  უარის თქმად;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          7.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებითი ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების 
                                                             ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევის შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშვნელობის მქონე შედეგებს;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                          7.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი 
                                                            დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების 
                                                          ყველა რეგულირება, გარდა იმ შემთხვევისა, როდესაც სხვა ხელშეკრულების, შეთანხმების ან/და დანართის 
                                                         მიზანს არ წარმოადგენს ამ ხელშეკრულების პირობებში ცვლილების ან/და დამატების შეტანა ან თუ მხარეები 
                                                         დამატებით ხელშეკრულებაში, შეტანხმებაში ან/და დანართში არ შეთანხმდებიან ამ წესისაგან განსხვავებულ წესზე. 
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        7.7. თუ შეთანხმების ან/და დანართის მიზანს წარმოადგენს ამ ხელშეკრულების პირობებში ცვლილების შეტანა, 
                                                                    ამ ხელშეკრულების რეგულირება არ გავრცელდება იმ მუხლებზე/პუნქტებზე, 
                                                                     რომლებშიც შესულია ცვლილებები. სხვა ნაწილებში რეგულირება რჩება უცვლელი;
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        7.8. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 03 (სამი) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                         მსესხებელი:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        /'.$res[name].'/  _______________
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        '.$res[actual_address].', '.$res[juridical_address].'
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                          ელ/ფოსტა '.$res[email].'
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                          ტელ: '.$res[phone].'
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                         თავდები:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        /სალომე ქურდაძე / _______________ 
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                           მის: თბილისი, ქსანის ქუჩა, კორპუსი 12ა, ბინა 15. (თბილისი, თემქა მე-11 მ/რ, მე-2 კვ, კორპ 25 ბ. 9)
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                         მის: ხაშური, იაკობ გოგებაშვილის ქუჩა, N 72, ბინა 2(ყოფ. კავშირის); (თბილისი, თემქა მე-11 მ/რ, მე-2 კვ, კორპ 25 ბ. 9)
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                        ელ/ფოსტა; s.qurdadze.1@gmail.com
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                        ტელ: 598674067
                        </div>
                            
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                         გამსესხებელი:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                        /შპს "თი ჯი მობაილ"/ ------------------------------
                        </div>
                        <div style="width:100%; font-size: 12px;">
                                                        მინდ. პირი ვ.ბახტაძე
                        </div>
                   </div';
    }elseif ($file_type == 'Schedule_ltd_nocustoms'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                                წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე, 
                                                                წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), და მეორეს მხრივ, შპს „მანი“/“MANI“ 
                           LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974) (შემდგომში მსესხებელი), 
                                                                ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.6.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.7.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი;
                            <br>3.8.	ყოველთვიურად მსესხებელი იხდის თანხას გრაფიკის მიხედვით,რომელიც თანდართულია ხელშეკრულებას,რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში;
                            <br>3.9.	იმ შემთხვევაში თუ მსესხებელმა შემოსავლების სამსახურის შეტყობინება N 4035101 განსაზღვრულ                  ვადაში, არ განაბაჟა ავტომობილი, შსს-ს სსიპ მომსახურების სააგენტოს ავტომობილის საექსპორტო დათვალიერების აქტში ასახული თანხა მთლიანად დაემატა ამ ხელშეკრულების 3.1. პუნქტით განსაზღვრული სესხის ძირ თანხას და სრულად დაეკისრება მსესხებელს.
                            <br>3.10.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში (თუ გრაფიკში სხვა რამ არ არის გათვალისწინებული) .
                            <br>3.11.	იმ შემთხვევაში, თუ მსესხებელი წინსწრებით დაფარავს ძირი თანხის ნაწილს, მაშინ სესხი უნდა გადაანგარიშდეს და შედგეს ახალი გადახდის გრაფიკი;
                            <br>3.12.	მსესხებელს წინასწრებით ძირი თანხის ნაწილის დაფარვა შეუძლია მხოლოდ იმ რიცხვებში, რომლებიც არის მითითებული გრაფიკში;
                            <br>3.13.	წინსწრებით დაფარვის საკომისიო დარჩენილი ძირი თანხის -  3 %
                            <br>3.14.   სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.15.	 საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                                                                                             ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.16.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.17.	ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.18.	წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.19.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	         სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5.      მხარეთა უფლებები და ვალდებულებები
                            <br>5.1.    მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2.    გამსესხებელი უფლებამოსილია:
                            <br>5.2.1.  მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2.  მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3.  ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3.    გამსესხებელი ვალდებულია:
                            <br>5.3.1.  გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2.  მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3.  მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4.  დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5.  საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4.    მსესხებელი უფლებამოსილია:
                            <br>5.4.1.  მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2.  მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3.  მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4.  ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5.  ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5.    მსესხებელი ვალდებულია:
                            <br>5.5.1.  დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                        </div>
                        <div style="width:100%; margin-top: 60px;">
                            <table style="width:100%;">
                                <tr style="width:100%;">
                                    <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                    <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>სახელწოდება: შპს "მანი"/"MANI" LTD<br>(საიდენტიფიკაციო კ. № 445437965 )<br>დირექტორი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:100%; margin-top: 60px;">
                            <table style="width:100%;">
                                <tr style="width:100%;">
                                    <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                </tr>
                            </table>
                        </div>
                     </div>
               </div';
    }elseif ($file_type == 'Schedule_ltd_trusted'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                                წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე,
                                                                წარმომადგენილი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), 
                                                                და მეორეს მხრივ, შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974) 
                                                                და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი# 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, 
                                                                რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი : მარიამ ნავროზაშვილი, 
                           (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ: 598270730) (შემდგომში მსესხებელი), 
                                                                 ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  "თი ჯი მობაილ"
                            <br>1.2.	მსესხებელი – შპს "მანი"/"MANI" LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი# 01019068974) სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი : მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ: 598270730), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.6.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.7.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი;
                            <br>3.8.	ყოველთვიურად მსესხებელი იხდის თანხას გრაფიკის მიხედვით,რომელიც თანდართულია ხელშეკრულებას,რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში;
                            <br>3.9.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში (თუ გრაფიკში სხვა რამ არ არის გათვალისწინებული) .
                            <br>3.10.	იმ შემთხვევაში, თუ მსესხებელი წინსწრებით დაფარავს ძირი თანხის ნაწილს, მაშინ სესხი უნდა გადაანგარიშდეს და შედგეს ახალი გადახდის გრაფიკი;
                            <br>3.11.		მსესხებელს წინასწრებით ძირი თანხის ნაწილის დაფარვა შეუძლია მხოლოდ იმ რიცხვებში, რომლებიც არის მითითებული გრაფიკში;
                            <br>3.12.		წინსწრებით დაფარვის საკომისიო დარჩენილი ძირი თანხის -  3 %
                            <br>3.13.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.14.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.15.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.16.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.17.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.18.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>სახელწოდება: შპს "მანი"/"MANI" LTD<br>(საიდენტიფიკაციო კ. № 445437965 )<br>დირექტორი:<br>პირ ნომერი:<br>მინდობილი პირი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'Schedule_trusted_nocustoms'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                               წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე, 
                                                                წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), და მეორეს მხრივ, 
                                                                ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974),
                                                                სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, 
                            (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730) 
                            (შემდგომში მსესხებელი), ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.6.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.7.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი;
                            <br>3.8.	ყოველთვიურად მსესხებელი იხდის თანხას გრაფიკის მიხედვით,რომელიც თანდართულია ხელშეკრულებას,რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში;
                            <br>3.9.	იმ შემთხვევაში თუ მსესხებელმა შემოსავლების სამსახურის შეტყობინება N 4035101 განსაზღვრულ                  ვადაში, არ განაბაჟა ავტომობილი, შსს-ს სსიპ მომსახურების სააგენტოს ავტომობილის საექსპორტო დათვალიერების აქტში ასახული თანხა მთლიანად დაემატა ამ ხელშეკრულების 3.1. პუნქტით განსაზღვრული სესხის ძირ თანხას და სრულად დაეკისრება მსესხებელს.
                            <br>3.10.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში (თუ გრაფიკში სხვა რამ არ არის გათვალისწინებული) .
                            <br>3.11.	იმ შემთხვევაში, თუ მსესხებელი წინსწრებით დაფარავს ძირი თანხის ნაწილს, მაშინ სესხი უნდა გადაანგარიშდეს და შედგეს ახალი გადახდის გრაფიკი;
                            <br>3.12.		მსესხებელს წინასწრებით ძირი თანხის ნაწილის დაფარვა შეუძლია მხოლოდ იმ რიცხვებში, რომლებიც არის მითითებული გრაფიკში;
                            <br>3.13.		წინსწრებით დაფარვის საკომისიო დარჩენილი ძირი თანხის -  3 %
                            <br>3.14.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.15.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.16.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.17.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.18.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.19.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>მინდობილი პირი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'Schedule_ltd'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                               წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - 
                                                                დირექტორი გიორგი კილაძე, წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), 
                                                                და მეორეს მხრივ, შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974) (შემდგომში მსესხებელი),
                                                                ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.6.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.7.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი;
                            <br>3.8.	ყოველთვიურად მსესხებელი იხდის თანხას გრაფიკის მიხედვით,რომელიც თანდართულია ხელშეკრულებას,რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში;
                            <br>3.9.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში (თუ გრაფიკში სხვა რამ არ არის გათვალისწინებული) .
                            <br>3.10.	იმ შემთხვევაში, თუ მსესხებელი წინსწრებით დაფარავს ძირი თანხის ნაწილს, მაშინ სესხი უნდა გადაანგარიშდეს და შედგეს ახალი გადახდის გრაფიკი;
                            <br>3.11.		მსესხებელს წინასწრებით ძირი თანხის ნაწილის დაფარვა შეუძლია მხოლოდ იმ რიცხვებში, რომლებიც არის მითითებული გრაფიკში;
                            <br>3.12.		წინსწრებით დაფარვის საკომისიო დარჩენილი ძირი თანხის -  3 %
                            <br>3.13.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.14.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.15.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.16.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.17.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.18.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>სახელწოდება: შპს "მანი"/"MANI" LTD<br>(საიდენტიფიკაციო კ. № 445437965 )<br>დირექტორი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'Schedule_trusted'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                              წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე,
                                                                წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), 
                                                                და მეორეს მხრივ, ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი)
                                                                აკაკი ელისაშვილი (პირადი № 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, 
                                                                რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730)
                            (შემდგომში მსესხებელი), ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.6.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.7.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი;
                            <br>3.8.	ყოველთვიურად მსესხებელი იხდის თანხას გრაფიკის მიხედვით,რომელიც თანდართულია ხელშეკრულებას,რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში;
                            <br>3.9.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში (თუ გრაფიკში სხვა რამ არ არის გათვალისწინებული) .
                            <br>3.10.	იმ შემთხვევაში, თუ მსესხებელი წინსწრებით დაფარავს ძირი თანხის ნაწილს, მაშინ სესხი უნდა გადაანგარიშდეს და შედგეს ახალი გადახდის გრაფიკი;
                            <br>3.11.		მსესხებელს წინასწრებით ძირი თანხის ნაწილის დაფარვა შეუძლია მხოლოდ იმ რიცხვებში, რომლებიც არის მითითებული გრაფიკში;
                            <br>3.12.		წინსწრებით დაფარვის საკომისიო დარჩენილი ძირი თანხის -  3 %
                            <br>3.13.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.14.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                                                                                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.15.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.16.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.17.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.18.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>მინდობილი პირი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'Schedule_nocustoms'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                             წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), 
                            - დირექტორი გიორგი კილაძე, წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  
                            (შემდგომში გამსესხებელი), და მეორეს მხრივ, აკაკი ელისაშვილი (პირადი № 01019068974), (შემდგომში მსესხებელი),
                                                                ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- აკაკი ელისაშვილი, რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.6.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.7.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი;
                            <br>3.8.	ყოველთვიურად მსესხებელი იხდის თანხას გრაფიკის მიხედვით,რომელიც თანდართულია ხელშეკრულებას,რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში;
                            <br>3.9.	იმ შემთხვევაში თუ მსესხებელმა შემოსავლების სამსახურის შეტყობინება N 4035101 განსაზღვრულ                  ვადაში, არ განაბაჟა ავტომობილი, შსს-ს სსიპ მომსახურების სააგენტოს ავტომობილის საექსპორტო დათვალიერების აქტში ასახული თანხა მთლიანად დაემატა ამ ხელშეკრულების 3.1. პუნქტით განსაზღვრული სესხის ძირ თანხას და სრულად დაეკისრება მსესხებელს.
                            <br>3.10.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში (თუ გრაფიკში სხვა რამ არ არის გათვალისწინებული) .
                            <br>3.11.	იმ შემთხვევაში, თუ მსესხებელი წინსწრებით დაფარავს ძირი თანხის ნაწილს, მაშინ სესხი უნდა გადაანგარიშდეს და შედგეს ახალი გადახდის გრაფიკი;
                            <br>3.12.		მსესხებელს წინასწრებით ძირი თანხის ნაწილის დაფარვა შეუძლია მხოლოდ იმ რიცხვებში, რომლებიც არის მითითებული გრაფიკში;
                            <br>3.13.		წინსწრებით დაფარვის საკომისიო დარჩენილი ძირი თანხის -  3 %
                            <br>3.14.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.15.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.16.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.17.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.18.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.19.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            
                            
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'Schedule'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ 
                         (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე, წარმომადგენელი
                                                            ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), და მეორეს მხრივ,
                                                            აკაკი ელისაშვილი (პირადი № 01019068974), (შემდგომში მსესხებელი), 
                                                            ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- აკაკი ელისაშვილი, რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.6.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.7.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი;
                            <br>3.8.	ყოველთვიურად მსესხებელი იხდის თანხას გრაფიკის მიხედვით,რომელიც თანდართულია ხელშეკრულებას,რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში;
                            <br>3.9.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში (თუ გრაფიკში სხვა რამ არ არის გათვალისწინებული) .
                            <br>3.10.	იმ შემთხვევაში, თუ მსესხებელი წინსწრებით დაფარავს ძირი თანხის ნაწილს, მაშინ სესხი უნდა გადაანგარიშდეს და შედგეს ახალი გადახდის გრაფიკი;
                            <br>3.11.		მსესხებელს წინასწრებით ძირი თანხის ნაწილის დაფარვა შეუძლია მხოლოდ იმ რიცხვებში, რომლებიც არის მითითებული გრაფიკში;
                            <br>3.12.		წინსწრებით დაფარვის საკომისიო დარჩენილი ძირი თანხის -  3 %
                            <br>3.13.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.14.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.15.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.16.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.17.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.18.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4.  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;"><br>მსესხებელი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'agreement_nocustoms_trusted'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                                წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე, 
                                                                წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), და მეორეს მხრივ, 
                                                                ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი 
                            (პირადი № 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, 
                                                                ნოტარიუსი: მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730) 
                            (შემდგომში მსესხებელი), ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	საკრედიტო ხელშეკრულების გაგრძელების საფასური (საჭიროების შემთხვევაში, იხდის მსესხებელი)- 70 ლარი.
                            <br>3.6.	საკრედიტო ხელშეკრულების გაგრძელების შემთხვევაში, მსესხებელი ვალდებულია დაფაროს ძირი თანხის 15 %;
                            <br>3.7.	 სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.8.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.9.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი, რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში.
                            <br>3.10.	იმ შემთხვევაში თუ მსესხებელმა შემოსავლების სამსახურის შეტყობინება N 4035101 განსაზღვრულ                  ვადაში, არ განაბაჟა ავტომობილი, შსს-ს სსიპ მომსახურების სააგენტოს ავტომობილის საექსპორტო დათვალიერების აქტში ასახული თანხა მთლიანად დაემატა ამ ხელშეკრულების 3.1. პუნქტით განსაზღვრული სესხის ძირ თანხას და სრულად დაეკისრება მსესხებელს.
                            <br>3.11.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში .
                            <br>3.12.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.13.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.14.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.15.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.16.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.17.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4.  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                                                        
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>მინდობილი პირი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'agreement_nocustoms'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                               წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - 
                                                                დირექტორი გიორგი კილაძე, წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), 
                                                                და მეორეს მხრივ, აკაკი ელისაშვილი (პირადი № 01019068974), 
                            (შემდგომში მსესხებელი), ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- აკაკი ელისაშვილი, რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	საკრედიტო ხელშეკრულების გაგრძელების საფასური (საჭიროების შემთხვევაში, იხდის მსესხებელი)- 70 ლარი.
                            <br>3.6.	საკრედიტო ხელშეკრულების გაგრძელების შემთხვევაში, მსესხებელი ვალდებულია დაფაროს ძირი თანხის 15 %;
                            <br>3.7.	 სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.8.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.9.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი, რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში.
                            <br>3.10.	იმ შემთხვევაში თუ მსესხებელმა შემოსავლების სამსახურის შეტყობინება N 4035101 განსაზღვრულ                  ვადაში, არ განაბაჟა ავტომობილი, შსს-ს სსიპ მომსახურების სააგენტოს ავტომობილის საექსპორტო დათვალიერების აქტში ასახული თანხა მთლიანად დაემატა ამ ხელშეკრულების 3.1. პუნქტით განსაზღვრული სესხის ძირ თანხას და სრულად დაეკისრება მსესხებელს.
                            <br>3.11.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში .
                            <br>3.12.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.13.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.14.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.15.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.16.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.17.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                                                        
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br><br>მსესხებელი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'agreement_fee_ltd_nocustoms'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                             წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - 
                                                            დირექტორი გიორგი კილაძე, წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  
                            (შემდგომში გამსესხებელი), და მეორეს მხრივ, შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965),
                                                            დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974) (შემდგომში მსესხებელი), 
                                                            ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	საკრედიტო ხელშეკრულების გაგრძელების საფასური (საჭიროების შემთხვევაში, იხდის მსესხებელი)- 70 ლარი.
                            <br>3.6.	საკრედიტო ხელშეკრულების გაგრძელების შემთხვევაში, მსესხებელი ვალდებულია დაფაროს ძირი თანხის 15 %;
                            <br>3.7.	 სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.8.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.9.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი, რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში.
                            <br>3.10.	იმ შემთხვევაში თუ მსესხებელმა შემოსავლების სამსახურის შეტყობინება N 4035101 განსაზღვრულ                  ვადაში, არ განაბაჟა ავტომობილი, შსს-ს სსიპ მომსახურების სააგენტოს ავტომობილის საექსპორტო დათვალიერების აქტში ასახული თანხა მთლიანად დაემატა ამ ხელშეკრულების 3.1. პუნქტით განსაზღვრული სესხის ძირ თანხას და სრულად დაეკისრება მსესხებელს.
                            <br>3.11.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში .
                            <br>3.12.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.13.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.14.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.15.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.16.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.17.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            
                                                        
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>სახელწოდება: შპს „მანი“/“MANI“ LTD<br>(საიდენტიფიკაციო კ. № 445437965 ) <br>დირექტორი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'agreement_fee_trusted'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე, 
                                                            წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), და მეორეს მხრივ, 
                                                            ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974), 
                                                            სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, 
                            (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730)
                            (შემდგომში მსესხებელი), ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	საკრედიტო ხელშეკრულების გაგრძელების საფასური (საჭიროების შემთხვევაში, იხდის მსესხებელი)- 70 ლარი.
                            <br>3.6.	საკრედიტო ხელშეკრულების გაგრძელების შემთხვევაში, მსესხებელი ვალდებულია დაფაროს ძირი თანხის 15 %;
                            <br>3.7.	 სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.8.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.9.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი, რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში.
                            <br>3.10.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში .
                            <br>3.11.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.12.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.13.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.14.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.15.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.16.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                                                        
                             </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>მინდობილი პირი:</br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'agreement_fee_trusted'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - დირექტორი გიორგი კილაძე, 
                                                            წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), და მეორეს მხრივ, 
                                                            ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974), 
                                                            სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, 
                            (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730)
                            (შემდგომში მსესხებელი), ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- ბონდო ახვლედიანი (პირადი №01003003149) (შემდგომში მსესხებელი) და (შემდგომში მინდობილი პირი) აკაკი ელისაშვილი (პირადი № 01019068974), სანოტარო მოქმედების რეგისტრაციის ნომერი N140655779, რეგისტრაციის თარიღი 25.06.2014 წელს, ნოტარიუსი: მარიამ ნავროზაშვილი, (მისამართი ქ.თბილისი, თემქის 3მ/რ 4კვ კორ 57-ის მიმდებარედ ტელ:598270730), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	საკრედიტო ხელშეკრულების გაგრძელების საფასური (საჭიროების შემთხვევაში, იხდის მსესხებელი)- 70 ლარი.
                            <br>3.6.	საკრედიტო ხელშეკრულების გაგრძელების შემთხვევაში, მსესხებელი ვალდებულია დაფაროს ძირი თანხის 15 %;
                            <br>3.7.	 სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.8.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.9.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი, რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში.
                            <br>3.10.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში .
                            <br>3.11.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.12.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.13.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.14.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.15.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.16.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                                                        
                             </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი<br>მინდობილი პირი:</br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'agreement_fee_'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), -
                                                            დირექტორი გიორგი კილაძე, წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი),
                                                            და მეორეს მხრივ, აკაკი ელისაშვილი (პირადი № 01019068974), (შემდგომში მსესხებელი),
                                                            ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- აკაკი ელისაშვილი, რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	საკრედიტო ხელშეკრულების გაგრძელების საფასური (საჭიროების შემთხვევაში, იხდის მსესხებელი)- 70 ლარი.
                            <br>3.6.	საკრედიტო ხელშეკრულების გაგრძელების შემთხვევაში, მსესხებელი ვალდებულია დაფაროს ძირი თანხის 15 %;
                            <br>3.7.	 სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.8.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.9.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი, რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში.
                            <br>3.10.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში .
                            <br>3.11.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.12.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.13.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.14.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.15.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.16.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4..  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                            
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მინდობილი პირი:</br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'agreement_fee_ltd'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">საკრედიტო ხელშეკრულება #1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            წინამდებარე ხელშეკრულების მონაწილე მხარეები, ერთის მხრივ შპს „თი ჯი მობაილ“ (საიდენტიფიკაციო № 205170177), - 
                                                            დირექტორი გიორგი კილაძე, წარმომადგენელი ვახტანგ ბახტაძის სახით (მინდობილობა №001, 10.25.2012 წელი)  (შემდგომში გამსესხებელი), 
                                                            და მეორეს მხრივ, შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974) (შემდგომში მსესხებელი), 
                                                            ჩვენს შორის მიღწეული შეთანხმების შედეგად, ვდებთ ხელშეკრულებას სასყიდლიანი სესხის გაცემის შესახებ.
                        </div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                                                            ტერმინთა განმარტება:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            <br>1.1.	გამსესხებელი- შპს  „თი ჯი მობაილ“
                            <br>1.2.	მსესხებელი- შპს „მანი“/“MANI“ LTD (საიდენტიფიკაციო კ. № 445437965), დირექტორი აკაკი ელისაშვილი (პირადი# 01019068974), რომელიც სარგებლობს ამ ხელშეკრულების შესაბამისად სასყიდლიანი სესხის სახით გაცემული ფულადი თანხით;
                            <br>1.3.	ხელშეკრულება- წინამდებარე ხელშეკრულება, რომელიც განსაზღვრავს ამ ხელშეკრულების მონაწილე მხარეთა შორის არსებულ უფლებებსა და ვალდებულებებს, ასევე არეგულირებს გამსესხებელსა და მსესხებელს შორის არსებული სამართალურთიერთობის ძირითად პირობებს და პარამეტრებს;
                            <br>1.4.	სესხი- მსესხებელზე წინამდებარე ხელშეკრულებით გათვალისწინებული წესით და ოდენობით პირადი მოხმარებისთვის გაცემული თანხა, საქართველოს მოქმედი კანონმდებლობის შესაბამისად;
                            <br>1.5.	სესხის თანხა- ამ ხელშეკრულების შესაბამისად დადგენილი ოდენობით მსესხებელზე გამსესხებლის მიერ გაცემული ფულადი თანხა;
                            <br>1.6.	საპროცენტო განაკვეთი- ამ ხელშეკრულებით გათვალისწინებული ფიქსირებული ან იდექსირებული საპროცენტო განაკვეთი ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე;
                            <br>1.7.	ფიქსირებული საპროცენტო განაკვეთი- საპროცენტო განაკვეთი, რომელიც დაფიქსირებულია ამ ხელშეკრულებაში და უცვლელია ხელშეკრულების მოქმედების სრული პერიოდის მანძილზე ან რომლის ცვლილებაც შესაძლებელია ხელშეკრულებით გათვალისწინებული ცალკეული გარემოებების დადგომის შემთხვევაში. საპროცენტო განაკვეთის ცვლილებად არ მიიჩნევა ხელშეკრულებაში წინასწარ განსაზღვრული პირობების შესაბამისად მსესხებლის ქმედებასთან დაკავშირებული გარემოებების დადგომიდან გამომდინარე საპროცენტო განაკვეთის ავტომატური ცვლილება;
                            <br>1.8.	საპროცენტო სარგებელი- მსესხებლის მიერ გამსესხებლისთვის გადასახდელი ფულადი თანხა, რომელიც წარმოადგენს სარგებელს მსესხებლის მიერ გამსესხებლის მხრიდან მიღებული ფულადი სახსრებით სარგებლობისთვის;
                            <br>1.9.	ფინანსური ხარჯი- ნებისმიერი ხარჯი, რომელიც პირდაპირ ან არაპირდაპირ გასწია ან/და მომავალში გასწევს გამსესხებელი და წარმოადგენს მსესხებლის მიერ სესხის მიღებასა და მისი ამოღებისთვის საჭირო ხარჯს;
                            <br>1.10.	მხარეები- გამსესხებელი და მსესხებელი ერთად. 
                            <br>2.	ხელშეკრულების საგანი
                            <br>2.1.	წინამდებარე ხელშეკრულების შესაბამისად, გამსესხებელი კისრულობს ვალდებულებას, გასცეს მსესხებელზე სესხი ამ ხელშეკრულებით გათვალისწინებულ ვადებში და ოდენობით, ხოლო მსესხებელი კისრულობს ვალდებულებას დააბრუნოს მიღებული სესხი, მასზე დარიცხული საპროცენტო სარგებელი, დაფაროს ყველა ფინანსური ხარჯი და სრულიად შეასრულოს ყველა ნაკისრი ვალდებულება.
                            <br>3.	სესხით სარგებლობის პირობები
                            <br>3.1.	სესხის სრული მოცულობა (ოდენობა) – 2380 აშშ დოლარი (ექვივალენტი ლარში)
                            <br>3.2.	გატანის პერიოდულობა- ერთჯერადი;
                            <br>3.3.	სესხით სარგებლობის ვადა- 24 (ოცდაოთხი) თვე;
                            <br>3.4.	სესხის გაცემის საკომისიო - 3% (აკისრია მსესხებელს)
                            <br>3.5.	საკრედიტო ხელშეკრულების გაგრძელების საფასური (საჭიროების შემთხვევაში, იხდის მსესხებელი)- 70 ლარი.
                            <br>3.6.	საკრედიტო ხელშეკრულების გაგრძელების შემთხვევაში, მსესხებელი ვალდებულია დაფაროს ძირი თანხის 15 %;
                            <br>3.7.	 სადაზღვევო ხარჯი- 3 თვის დაზღვევის საფასური -110 (ასათი) აშშ დოლარი (ექვივალენტი ლარში). მსესხებელს ასევე ეკისრება ყოველ 3 თვეში დაზღვევის საფასურის დამატებით გადახდა იმ დროისთვის არსებული ოდენობით;
                            <br>3.8.	გირავნობის ხარჯი - 225 (ორასორმოცდათხუთმეტი) ლარი; (აკისრია მსესხებელს)
                            <br>3.9.	სარგებელი სესხით სარგებლობისთვის- ამ ხელშეკრულების მოქმედების მანძილზე ყოველთვიურად -3% პროცენტი, რაც შეადგენს 140.53 (ასორმოცი და ორმოცდაცამეტი) აშშ დოლარის ექვივალენტს ლარში.
                            <br>3.10.	მსესხებელი ვალდებულია ამ ხელშეკრულების  3.8 პუნქტში მითითებული თანხის გადახდები განახორციელოს ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში ყოველი თვის 12 რიცხვში .
                            <br>3.11.		სესხის გაცემის ვადა – 01 (ერთი) დღე;
                            <br>3.12.	        საპროცენტო სარგებლის გადახის პერიოდულობა განისაზღვრება წინამდებარე ხელშეკრულებით;
                            ვალუტის კურსი ხელშეკრულების ხელმოწერის დღისათვის – 1 აშშ დოლარი = 2.5887 ლარი;
                            <br>3.13.	სესხის ხელშეკრულებით გათვალისწინებული სესხის თანხის დადგენილ ვადაში დაუბრუნებლობის ან/და ამ ხელშეკრულებით დადგენილი პერიოდულობით გადაუხდელობის /ნაწილობრივ გადახდის შემთხვევაში, მსესხებელს დაერიცხება ამ ხელშეკრულებით გათვალისწინებული პირგასამტეხლო (საურავი), რომელიც დადგენილია გადახდის ვადის გადაცილებისათვის და რომელიც ანგარიშდება სესხის ძირი თანხიდან.
                            <br>3.14.		ფინანსური ხარჯები: ამ ხელშეკრულების მიზნებისათვის ფინანსურ ხარჯს წარმოადგენს ყველა ის ხარჯი, რომელიც აუცილებელია სესხის გაცემისა და მისი ამოღებისათვის (მათ შორის სადაზღვევო ხარჯი, სააღსრულებო ხარჯი, სესხის გაპრობლემების შემთხვევაში პრობლემური სამსახურის მომსახურების საფასური, აუდიტის მომსახურების საფასური. გირავნობის საფასური, ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ნებისმიერი გადასახდელი, რომელიც გასწია გამსესხებელმა სესხის გასაცემად და დასაბრუნებლად);
                            <br>3.15.		წინამდებარე ხელშეკრულების მიზნებიდან გამომდინარე, ფინანსურ ხარჯად განიხილება ასევე ნებისმიერი გადასახდელი, რომელსაც მსესხებელი უხდის მესამე პირს, თუკი ეს გადასახდელი წარმოადგენს აუცილებლობას და მის გარეშე გამსესხებლის მიერ ნაკისრი ვალდებულების შესრულება იქნება შეუძლებელი ან არსებითად გართულდება;
                            <br>3.16.	ფინანასურ ხარჯად არ განიხილება ის ხარჯები, რომლების გაღებაც მსესხებლის მიერ იქნებოდა აუცილებელი სესხის მიღების გარეშეც. ასეთი ტიპის ხარჯები არ წარმოადგენს წინამდებარე ხელშეკრულების რეგულირების სფეროს და სრულად ეკისრება მსესხებელს.
                            <br>4.	სესხის უზრუნველყოფა
                            <br>4.1.	სესხი უზრუნველყოფილია გირავნობით, ასევე მსესხებლის მთელი ქონებით;
                            <br>4.2.	უზრუნველყოფიდან გამომდინარე მხარეთა უფლებები და ვალდებულებები დეტალურად რეგულირდება ზემოაღნიშნული სესხის უზრუნველსაყოფად მხარეთა (ან მესამე პირსა და გამსესხებელს შორის) შორის დადებული გირავნობის ხელშეკრულებით;
                            <br>4.3.	იმ შემთხვევაში, თუ მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი ვალდებულება იქნება დარღვეული, ხოლო გირავნობის საგნის რეალიზაციის შედეგად მიღებული ამონაგები არ იქნება საკმარისი გამსესხებლის მოთხოვნების სრულად დასაკმაყოფილებლად, გამსესხებელი უფლებამოსილია მოითხოვოს მსესხებლის ან/და მესამე პირის (ასეთის არსებობის შემთხვევაში) საკუთრებაში არსებული უძრავ-მოძრავი ქონების რეალიზაცია საკუთარი მოთხოვნის დაკმაყოფილების მიზნით;
                            <br>4.4.	მსესხებლის მიერ ნაკისრ ვალდებულებათა ნებისმიერი დარღვევა იძლევა საფუძველს გამოყენებულ იქნას უზრუნველყოფის ხელშეკრულებით გათვალისწინებული აქტივები მათი რეალიზაციის უფლებით;
                            <br>4.5.	დარღვევად განიხილება მსესხებლის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და ნაწილობრივი შესრულება, რომელიც არ მოდის შესაბამისობაში მსესხებლის მიერ ნაკისრ ვალდებულებებთან და ეწინააღმდეგება ვალდებულების შესრულების ძირითად პრინციპებს.
                            
                            <br>4.6.	იმ შემთხვევაში, თუკი უზრუნველყოფის საგნად გამოყენებული ნივთის მდგომარეობა გაუარესდება, განადგურდება, ღირებულება შემცირდება ან/და ადგილი ექნება რაიმე ისეთ გარემოებას, რომლიდან გამომდინარეც რეალური საფრთხე ექმნება სესხის უზრუნველყოფას, მსესხებელი ვალდებულია ამის თაობაზე აცნობოს გამსესხებელს ასეთი ფაქტის დადგომის მომენტიდან 48 (ორმოცდარვა) საათის განმავლობაში;
                            <br>4.7.	ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული შემთხვევის დადგომისას გამსესხებელი უფლებამოსილია მოსთხოვოს მსესხებელს დამატებითი უზურნველყოფის საშუალების წარდგენა, ხოლო მსესხებელი ვალდებულია წარმოუდგინოს გამსესხებელს ასეთი უზურნველყოფა;
                            <br>4.8.	მსესხებლის მიერ ამ ხელშეკრულების 4.7 პუნქტით გათვალისწინებული უზრუნველყოფის საშუალების წარმოდგენის შეუძლებლობის შემთხვევაში გამსესხებელი უფლებამოსილია დაუყოვნებლივ შეწყიტოს სესხის ხელშეკრულება და მოითხოვოს მსესხებლისაგან სესხის ძირი თანხის და მასზე დარიცხული პროცენტისა თუ პირგასამტეხლოს გადახდა;
                            <br>4.9.	მსესხებლის მხრიდან ამ ხელშეკრულების 4.6 პუნქტით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა გამსესხებელს აძლევს უფლებას, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს მსესხებლისაგან ნაკისრი ვალდებულების დაუყოვნებლივ სრულად შესრულება.
                            <br>5. მხარეთა უფლებები და ვალდებულებები
                            <br>5.1. მხარეები კისრულობენ ვალდებულებას, რომ შეასრულებენ წინამდებარე ხელშეკრულებით ნაკისრ ვალდებულებებს ჯეროვნად, კეთისინდისიერად, დათქმულ დროსა და ადგილას;
                            <br>5.2. გამსესხებელი უფლებამოსილია:
                            <br>5.2.1. მოითხოვოს მსესხებლისაგან ხელშეკრულებით ნაკისრი ვალდებულებების განუხრელად დაცვა და შესრულება;
                            <br>5.2.2. მოითხოვოს მსესხებლისგან სრული ინფომრაციის წარმოდგენა მისი ფინანსური მდგომარეობის შესახებ, როგორც ხელშეკრულების დადებამდე, ასევე ხელშეკრულების მოქმედების სრული პერიოდის განმავლობაში; 
                            <br>5.2.3. ვადაზე ადრე შეწყვიტოს ხელშეკრულება, თუკი მსესხებლის ქონებრივი მდგომარეობა იმდენად გაუარესდება, რომ შესაძლოა საფრთხე შეექმნება სესხის დაბრუნებას;
                            <br>5.3. გამსესხებელი ვალდებულია:
                            <br>5.3.1. გასცეს სესხის თანხა ამ ხელშეკრულებით გათვალისწინებული ოდენობით და ვადებში;
                            <br>5.3.2. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის პირობებთან დაკავშირებით;
                            <br>5.3.3. მიაწოდოს მსესხებელს სრული ინფორმაცია სესხის უზრუნველყოფის პირობებთან დაკავშირებით;
                            <br>5.3.4. დაუყოვნებლივ აცნობოს მსესხებელს ნებისმიერი ისეთი გარემოების ცვლილების შესახებ, რამაც შეიძლება გავლენა მოახდინოს მხარეთა უფლებებსა და ვალდებულებებზე;
                            <br>5.3.5. საკუთარი შესაძლებლობის და ვალდებულების ფარგლებში ყველანაირად შეუწყოს ხელი ხელშეკრულების შესრულებას;
                            <br>5.4. მსესხებელი უფლებამოსილია:
                            <br>5.4.1. მოსთხოვოს გამსესხებელს სესხის თანხის გადაცემა ამ ხელშეკრულებით გათვალისწინებული პირობებით, ოდენობით და ვადებში;
                            <br>5.4.2. მიიღოს სრული ინფორმაცია სესხის პირობებზე;
                            <br>5.4.3. მიიღოს სრული ინფორმაცია მოთხოვნის უზრუნველყოფის პირობებზე;
                            <br>5.4.4. ვალდებულების სრულად შესრულების შემდგომ მოსთხოვოს გამსესხებელს ვალდებულების შესრულების და შეწყვეტის დამადასტურებელი დოკუმენტის შედგენა და მისთვის გადაცემა;
                            <br>5.4.5. ამ ხელშეკრულებით გათვალსწინებული წესით და პირობებით ვადაზე ადრე შეასრულოს ნაკისრი ვალდებულება.
                            <br>5.5. მსესხებელი ვალდებულია:
                            <br>5.5.1. დააბრუნოს სესხის თანხა სარგებელთან (პროცენტთან) ერთად ამ ხელშეკრულებით გათვალისწინებული ოდენობით, პირობებით და ამ ხელშეკრულებით გათვალისწინებულ ვადებში.
                            <br>5.5.2. მსესხებელი ვალდებულია დააბრუნოს სესხის თანხა სესხის დაბრუნების დღისთვის არსებული ეროვნული ბანკის კურსით ლარში.
                            <br>5.5.3. მიაწოდოს გამსესხებელს სრული ინფორმაცია მისი ქონებრივი და ფინანსური მდგომარეობის შესახებ;
                            <br>5.5.4 დაუყოვნებლიც აცნობოს გამსესხებელს მისი ფინანსური/ქონებრივი მდგომარეობის ისეთი გაუარესების შესახებ, რამაც შეიძლება საფრთხე შეუქმნას მის მიერ ნაკისრი ვალდებულების შესრულებას;
                            <br>5.5.6 არ იკისროს რაიმე სახის ფინანსური (საკრედიტო, სასესხო და ა.შ.) ვალდებულებები ფიზიკური/იურიდიული პირების წინაშე გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.6 არ გამოიყენოს ამ ხელშეკრულებით გათვალისწინებული ვალდებულების შესრულების უზრუნველსაყოფად გამოყენებული ქონება საკუთარი ან/და მესამე პირების მიერ ნაკისრი ვალდებულების უზრუნველსაყოფად გამსესხებელთან წინასწარი წერილობითი შეთანხმების გარეშე;
                            <br>5.5.7 აუნაზღაუროს გამსესხებელს ყველა ის დანახარჯი, რომელიც ამ უკანასკნელმა გასწია მსესხებლის მიერ ნაკისრი ვალდებულების იძულებით შესრულებისათვის (ასეთის არსებობის შემთხვევაში- კერძოდ: აღსრულების ეროვნულ ბიუროს მომსახურების საფასური, ,აუდიტის მომსახურებისსაფასური ,სსიპ შსს მომსახურების სააგენტოში გაწეული ხარჯი და ამ სახის სხვა ხარჯები, რომელიც გამსხებელმა გასწია)
                            <br>5.6. მსესხებელი უფლებამოსილია ვადაზე ადრე შეასრულოს მის მიერ ნაკისრი ვალდებულება;
                            <br>5.7. მსესხებლის მიერ ნაკისრი ვალდებულების ვადაზე ადრე შესრულებად ჩაითვლება მის მიერ ამ ხელშეკრულების მოქმედების ვადის გასვლამდე სესხის დაფარვა
                            <br>5.8. გამსესხებელი ვალდებულია მიიღოს მსესხებლისაგან ვადაზე ადრე განხორციელებული შესრულება მხოლოდ იმ პირობით, თუ მსესხებლის მიერ დაფარული იქნება როგორც სესხის ძირი თანხა, ასევე ვადამოსული სარგებელი (პროცენტი) იმ ოდენობით, რა ოდენობითაც ის დაანგარიშდება ანგარიშსწორების დღისათვის;
                            <br>5.9. ხელშეკრულების მონაწილე მხარეები საკუთარი ვალდებულების ფარგლებში ვალდებული არიან შეასრულონ ყველა ის მოქმედება ან თავი შეიკავონ ყველა იმ მოქმედებისაგან, რომელიც პირდაპირ არ არის გათვალისწინებული ამ ხელშეკრულებით, მაგრამ გამომდინარეობს ვალდებულების არსიდან, ამ ხელშეკრულების მიზნებიდან, კეთილსინდისიერების პრინციპიდან და აუცილებელია მხარეთა ვალდებულებების სრულყოფილად შესასრულებლად.
                            <br>5.10. მსესხებელს ეკრძალება ა/მანქანის მართვა ნასვამ, ნარკოტიკულ და კანონით აკრძალული ყველანაირი საშუალებების ზემოქმედების ქვეშ. დამგირავებელს ასევე ეკრძალება ა/მანქანის გადაცემა მართვის უფლების არმქონე, არასრულწლოვან, სადაზღვეო პოლისში არ მითითებულ და ზემოთ აღნიშნული კანონით აკრძალული ნივთიერებების და საშუალებების ზემოქმედების ქვეშ მყოფი პირებისადმი.
                            <br>5.11. მსესხებელს ეკისრება ყველა  ხარჯი, რომელიც გამსესხებელმა გასწია და ყველა სახის ზიანი, რომელიც გამსესხებელს მიადგა მსესხებლის მიერ ამ ხელშეკრულების 5.10 პუნქტის დარღვევის გამო.
                            <br>6. ვალდებულების დარღვევა
                            <br>6.1. ამ ხელშეკრულებით გათვალისწინებული ვალდებულების დარღვევად ჩაითვლება მისი მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შეუსრულებლობა ან/და არაჯეროვანი შესრულება;
                            <br>6.2. ვალდებულების შეუსრულებლობად ითვლება ხელშეკრულების მონაწილე რომელიმე მხარის მიერ ნაკისრი ვალდებულების შესრულებაზე უარის გაცხადება ან/და რაიმე ისეთი მოქმედება/უმოქმედობა, რომლიდან გამომდინარეც ნათელია ან არსებობს საფუძვლიანი ეჭვი იმის თაობაზე, რომ ვალდებულება არ იქნება შესრულებული სრულად;
                            <br>6.3. ვალდებულების არაჯეროვან შესრულებად ითვლება შეთანხმების რომელიმე მხარის მიერ ნაკისრი ვალდებულების არასრულფასოვან ან/და არასრულყოფილი შესრულება;
                            <br>6.4. ვალდებულების შეუსრულებლობას ან არაჯეროვან შესრულებას უთანაბრდება ამ ხელშეკრულებით გათვალისწინებული ინფორმაციის მიწოდების ვალდებულების დარღვევა, არასწორი ან არასრულფასოვანი ინფორმაციის მიწოდება;
                            <br>6.5. მსესხებლის მიერ ვალდებულების შეუსრულებლობის/არაჯეროვანი შესრულების შემთხვევაში გამსესხებელი უფლებამოსილია ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს გადახდა;
                            <br>6.6. თითოეული მხარე უფლებამოსილია მოითხოვოს იმ ზიანის ანაზღაურება, რომელიც მას მიადგა ვალდებულების დარღვევის შედეგად;
                            <br>6.7. ზიანის ანაზღაურება არ ათავისუფლებს ვალდებულების დამრღვევ მხარეს ძირითადი ვალდებულებების შესრულებისაგან
                            <br>6.8.  5.10. პუნქტით დადგენილი ვალდებულებების შეუსრულებლობის შემთხვევაში ხელშეკრულება ჩაითვლება დარღვეულად და სესხის უზრუნველყოფის საგანი გაჩერდება გამსესხებლის მიერ შერჩეულ ავტოსადგომზე ვალდებულების სრულად შესრულებამდე.
                            <br>7. ხელშეკრულების შეწყვეტა
                            <br>7.1. წინამდებარე ხელშეკრულება წყდება  ვალდებულების სრულად შესრულებით;
                            <br>7.2. წინამდებარე ხელშეკრულება შესრულებით შეწყვეტილად ჩაითვლება მსესხებლის მიერ ნაკისრი ვალდებულების სრულად შესრულებით, ანუ სესხის ძირი თანხის, მასზე დარიცხული სარგებლის (პროცენტის), პირგასამტეხლოს და ხელშეკრულების შეწყვეტისათვის აუცილებელი ხარჯების (ასეთის არსებობის შემთხვევაში) სრულად გადახდის შემდგომ;
                            <br>7.3. ხელშეკრულების მოქმედების ვადის ამოწურვის შემდგომ ხელშეკრულება გაგრძელდება, თუ ორივე მხარე წერილობით დააფიქსირებს პოზიციას ხელშეკრულების გაგრძელების თაობაზე.
                            <br>7.4.  მსესხებელი ცნობად იღებს იმ გარემოებას, რომ ხელშეკრულების მოქმედების ვადის  წინამდებარე ხელშეკრულების  7.3 პუნქტით გათვალისწინებული პირობებით გაგრძელების შემთხვევაში მსესხებელი ვალდებულია ხელშეკრულების გაგრძელებისათვის გადაიხადოს სადაზღვევო ხარჯი ამ ხელშეკრულებაში მითითებული ოდენობით.
                            <br>7.5. მხარეთა შეთანხმებით, ხელშეკრულება შეიძლება გაგრძელდეს ამ ხელშეკრულებით გათვალსწინებული პირობებისაგან განსხვავებული პირობებით .  
                            <br>7.6 ხელშეკრულების  გაგრძელება დასაშვებია მხოლოდ ამ ხელშეკრულების ფორმის  (წერილობითი) დაცვით . ამ წესის დარღვევით დადებული შეთანხმება არის ბათილი და იურიდიული მნიშვნელობის მქონე შედეგებს არ წარმოშობს .
                            <br>8. ხელშეკრულების ვადაზე ადრე შეწყვეტა
                            <br>8.1. ხელშეკრულება შეიძლება ვადაზე ადრე შეწყდეს რომელიმე მხარის მიერ ნაკისრი ვალდებულების დარღვევის შემთხვევაში;
                            <br>8.2. მსესხებლის მიერ ამ ხელშეკრულებით ნაკისრი  ვალდებულებების დარღვევის შემთხვევაში გამსესხებელი უგზავნის მსესხებელს წერილობით შეტყობინებას ვალდებულების შესრულების მოთხოვნით და განუსაზღვრავს მას ვალდებულების შესრულების ვადას.
                            <br>8.3. გაფრთხილების მიუხედავად, მსესხებლის მიერ ვალდებულების შეუსრულებლბა ან არაჯეროვანი შესრულება აძლევს გამსესხებელს უფლებას ცალმხრივად, ვადაზე ადრე შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.4. იმ შემთხვევაში, თუ მსესხებელი გაფრთხილების მიღებიდან განსაზღვრულ ვადაში დაფარავს დავალიანებას, ხელშეკრულების მოქმედება გაგრძელდება იმავე პირობებით, რაც გათვალისწინებულია ამ ხელშეკრულებით. 
                            <br>8.5. ამ ხელშეკრულებით ნაკისრი ვალდებულების  განმეორებით დარღვევის შემთხვევაში გამსესხებელი უფლებამოსილია ცალმხრივად და უპირობოდ შეწყვიტოს ხელშეკრულება და მოითხოვოს სესხის თანხის, მასზე დარიცხული სარგებლის (პროცენტის) და პირგასამტეხლოს ერთიანად გადახდა, ასევე ყველა იმ დამატებითი დანახარჯის ანაზღაურება, რომელიც მას წარმოეშვა ხელშეკრულების ვადაზე ადრე შეწყვეტის შედეგად;
                            <br>8.6. ამ ხელშეკრულებით  გათვალსიწინებული თანხის ნაწილობრივ გადახდა არ ჩაითვლება ხელშეკრულების პირობის შესრულებად.
                            <br>9.პირგასამტეხლო
                            <br>9.1. წინამდებარე ხელშეკრულებით ნაკისრი ვალდებულებების შესრულების მოთხოვნის უზრუნველყოფის დამატებით საშუალებას წარმოადგენს პირგასამტეხლო;
                            <br>9.2. პირგასამტეხლოს გადახდის და მისი ოდენობის დაანგარიშების წესი განისაზღვრება ამ ხელშეკრულებით;
                            <br>9.3. ამ ხელშეკრულების მიზნებისათვის პირგასამტეხლოს გადახდევინება ხორციელდება ნაკისრი ვალდებულების შესრულების ვადის გადაცილებისათვის;
                            <br>9.4. მსესხებელს, ამ ხელშეკრულებით გათვალსწინებული გადახდის ვადის გადაცილების შემთხვევაში, დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.5%-ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე, ვალდებულების დარღვევიდან 3 დღის განმავლობაში; ხოლო გადახდის ვადის გადაცილების 3 (სამი) დღეზე მეტი ვადით გაგრძელების შემთხვევაში მსესხებელს დაერიცხება პირგასამტეხლო სესხის ძირი თანხის 0.8% ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.5. ამ ხელშეკრულების 9.4 მუხლით გათვალისწინებული პირგასამტეხლოს ოდენობა დაანგარიშდება სესხის ძირი თანხიდან;
                            <br>9.6. პირგასამტეხლოს დაკისრება ხდება ყოველი კონკრეტული ვალდებულების დარღვევისთვის (ყოველთვიური სარგებლის, გრაფიკით გათვალისწინებული თანხის თუ სესხის ძირი თანხის გადახდის ვადაგადაცილების შემთხვევაში)  ცალკ-ცალკე, სესხის ძირი თანხიდან.
                            <br>9.7. სესხის სარგებლის (ან გრაფიკით გათვალისწინებული თანხის) გადახდის ვადის 2 ან მეტი თვით ვადა გადაცილების შემთხვევაში, თითოეული თვის ვადაგადაცილებისათვის მსესხებელს ცალკ-ცალკე ერიცხება პირგასამტეხლო. იმ შემთხვევაში, თუ მსესხებლის მიერ უკვე დარღვეულია სესხის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადა და მსესხებელმა მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის გადახდის ვადასაც გადააცილა, პირგასამტეხლოს დარიცხვა პირველი თვის სარგებლისთვის იმავე ფარგლებში გაგრძელდება და ამასთან, მეორე და მომდევნო თვის სარგებლის ან გრაფიკით გათვალისწინებული თანხის ვადაგადაცილებისთვის ცალკ-ცალკე, დამატებით მოხდება პირგასამტეხლოს დარიცხვა სესხის ძირი თანხის 0.5% -ის ოდენობით ყოველ ვადაგადაცილებულ დღეზე.
                            <br>9.8. მსესხებლის მიერ გადახდილი პირგასამტეხლო არ ჩაითვლება სესხის თანხის ანგარიშში და რაიმე ფორმით მისი უკან დაბრუნების მოთხოვნა არის დაუშვებელი;
                            <br>9.10. პირგასამტეხლოს გადახდა არ ათავისუფლებს მის გადამხდელ მხარეს ძირითადი ვალდებულების შესრულების ვალდებულებისაგან
                            <br>10. საკონტაქტო ინფორმაცია
                            <br>10.1. მხარეთა შორის კონტაქტი ხორციელდება ამ ხელშეკრულებაში აღნიშნულ მისამართებზე, ტელეფონებზე და/ან ელ. ფოსტებზე;
                            <br>10.2. მსესხებელი აცხადებს, რომ თანახმაა მიიღოს გამსესხებლის მიერ გამოგზავნილი ყველანაირი შეტყობინება ამ ხელშეკრულებაში მითითებულ მისამართზე ან ელ/ფოსტებზე;
                            <br>10.3. მხარეები თანხმდებიან, რომ ყველა შეტყობინება, რომელიც გაიგზავნება ამ ხელშეკრულებაში მითითებულ მსესხებლის მისამართსა თუ ელ ფოსტაზე, მიუხედავად მისი ჩაბარებისა ან მიღებაზე უარის თქმისა, ჩაითვლება მსესხებლის მიერ მიღებულად;
                            <br>10.4. იმ შემთხვევაში, თუ მსესხებელი შეიცვლის საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას- იგი ვალდებულია აღნიშნულის თაობაზე წერილობით აცნობოს გამსესხებელს;
                            <br>10.5. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ახალი მისამართი და მასზე სრულად გავრცელდება ამ ხელშეკრულებით დადგენილი წესები;
                            <br>10.6. ამ ხელშეკრულების 10.4 მუხლით გათვალისწინებულ შემთხვევაში, თუკი მსესხებელი წერილობით არ აცნობებს გამსესხებელს მის შეცვლილ საკონტაქტო მონაცემს -ადგილსამყოფელს (მისამართს), ან ელ/ფოსტას-საკონტაქტო მისამართად ჩაითვლება ამ ხელშეკრულებაში მითითებული მონაცემები და მასზე გავრცელდება ამ ხელშეკრულების  წესები;
                            <br>10.7. მსესხებლის მიერ წინამდებარე ხელშეკრულების 10.4 მუხლით გათვალსწინებული წერილობითი შეტყობინებით გათვალისწინებული ვალდებულების დარღვევის შემთხვევაში, დაუშვებელია რაიმე სახის პრეტენზიის დაყენება გამსესხებლის მიმართ.
                            <br>11. ფორს-მაჟორი
                            <br>11.1. ხელშეკრულების მონაწილე მხარეები დროებით თავისუფლდებიან პასუხისმგებლობისაგან იმ შემთხვევაში, თუკი ვალდებულების შეუსრულებლობა გამოწვეულია დაუძლეველი ძალით (ფორს-მაჟორი);
                            <br>11.2. დაუძლეველ ძალად განიხილება ბუნებრივი კატაკლიზმები ან/და სახელმწიფოს მიერ მიღებული აქტები, რაც დროებით შეუძლებელს ხდის ვალდებულების შესრულებას;
                            <br>11.3. დაუძლეველი ძალის არსებობის შემთხვევაში ვალდებულების შესრულება გადაიწევა ამ გარემოების აღმოფხვრამდე
                            <br>12. დავათა გადაჭრის წესი:
                            <br>12.1.ამ ხელშეკრულებიდან გამომდინარე მხარეთა შორის წარმოშობილ ნებისმიერ დავას განიხილავს თბილისის საქალაქო სასამართლო.
                            <br>12.2.მხარეები შეთანხმდნენ, რომ პირველი ინსტანციის სასამართლოს მიერ მიღებული გადაწყვეტილება დაუყონებლივ აღსასრულებლად მიექცევა საქართველოს სამოქალაქო საპროცესო კოდექსის 268-ე მუხლის I1 ნაწილის შესაბამისად;
                            <br>13. დამატებითი დებულებები
                            <br>13.1. წინამდებარე ხელშეკრულებაზე ხელის მოწერით მხარეები აცხადებენ, რომ ამ ხელშეკრულების ყველა პირობა წარმოადგენს მათი ნამდვილი ნების გამოვლენას, ისინი ეთანხმებიან ამ პირობებს და სურთ ხელშეკრულების დადება აღნიშნული პირობებით;
                            <br>13.2 წინადებარე ხელშეკრულებაზე ხელის მოწერით მსესხებელი ადასტურებს მის მიერ ამ ხელშეკრულებით გათვალისწინებული ოდენობით სესხის თანხის მიღების ფაქტს.
                            <br>13.3. წინამდებარე ხელშეკრულების რომელიმე მუხლის, პუნქტის, დათქმის, წინადადების და ა.შ. ბათლობა არ იწვევს მთელი ხელშეკრულების ბათილობას;
                            <br>13.4. ხელშეკრულების მონაწილე რომელიმე მხარის მიერ უფლების გამოუყენებლობა არ განიხილება ამ უფლებაზე უარის თქმად;
                            <br>13.5. წინამდებარე ხელშეკრულებაში შეტანილი ცვლილებები ან/და დამატებები ძალაშია მხოლოდ იმ შემთხვევაში, თუ ისინი შესრულებულია ამ ხელშეკრულების ფორმის დაცვით და ხელმოწერილია მხარეთა მიერ. ამ წესის დარღვევით შეტანილი ნებისმიერი ცვლილება ან/და დამატება ბათილია მისი შეტანის მომენტიდან და არ წარმოშობს იურიდიული მნიშნველობის მქონე შედეგებს;
                            <br>13.6. ამ ხელშეკრულების საფუძველზე ან/და მისგან გამომდინარე ყველა ხელშეკრულება ან/და შეთანხმება და მათი დანართები წარმოადგენენ ამ ხელშეკრულების განუყოფელ ნაწილს და მათზე სრულად ვრცელდება ამ ხელშეკრულების წესები.
                            <br>13.7. ხელშეკრულება შედგენილია ქართულ ენაზე, თანაბარი იურიდიული ძალის მქონე 02 (ორ) ეგზემპლარად, ხელმოწერილია და ინახება მხარეებთან.
                                                        
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">გამსესხებელი: შპს "თი ჯი მობაილ" <br> მისამართი: ქ. თბილისი, დოლიძის ქ. 25/121<br>ს/კ 205270277<br>სს "საქართველოს ბანკი"<br>ბანკის კოდი: BAGAGE22<br>ა/ა GE12BG0000000523102000<br>ტელეფონის ნომერი: (ოფისი)<br>ტელეფონის ნომერი: 579796921<br>ტელეფონის ნომერი: 579131813 (ლერი)<br>ხელის მომწერის: ვახტანგ ბახტაძე,<br>პოზიცია: მენეჯერი<br>ელ.ფოსტა: tgmobail@mail.ru<br>s.qurdadze.1@gmail.com</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: left;">მსესხებელი: სახელწოდება: შპს „მანი“/“MANI“ LTD<br>(საიდენტიფიკაციო კ. № 445437965 ) <br>დირექტორი: აკაკი ელისაშვილი<br>პირ № 01019068974<br>მინდობილი პირი:</br>პირ № 01019068974<br>მისამართი:<br>ტელ.ნომერი:<br>ელ.ფოსტა:</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                   </div';
    }elseif ($file_type == 'Pledge'){
        $data  .= '<div style="size: 7in 9.25in; margin: 15mm 16mm 15mm 16mm;" id="dialog-form">
                        <div style="width:100%; font-size: 16px; text-align: center">giravnobis xelSekruleba # 1893</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px; text-align: right;">'.$res[day].'.'.$res[month_id].'.'.$res[year].'</div>
                        <div style="width:100%; font-size: 12px; text-align: left;">ქ. თბილისი</div>
                        <div style="width:100%; font-size: 12px; margin-top: 15px;">
                           erTis mxriv, Sps `Ti ji mobail ~ (saidentifikacio #205270277) (SemdgomSi mogiravne), - direqtori giorgi kilaZe ,  warmomadgenili vaxtang baxtaZis saxiT (mindobiloba #001, 10.05.2012 weli)   da  meores mxriv, bondo axvlediani (piradi #01003003149) (SemdgomSi მსესხებელი) და (SemdgomSi mindobili piri) აკაკი ელისაშვილი (piradi # 01019068974), sanotaro moqmedebis registraciis nomeri N140655779, registraciis TariRi 25.06.2014 wels, notariusi : mariam navrozaSvili, (misamarTi q.Tbilisi, Temqis 3m/r 4kv kor 57-is mimdebared tel: 598270730), vmoqmedebT ra 12.12.2016 wels dadebuli #1893 საკრედიტო xelSekrulebis safuZvelze da misi uzrunvelyofis mizniT, vdebT winamdebare xelSekrulebas Semdegze:
                        </div>
                        <div style="width:100%; font-size: 12px;">
                            muxli 1. terminTa ganmarteba.
                            <br>1.1.	mogiravne – Sps `Ti ji mobail~;
                            <br>1.2.	damgiravebeli – bondo axvlediani, (SemdgomSi mindobili piri) აკაკი ელისაშვილი (piradi # 01019068974), sanotaro moqmedebis registraciis nomeri N140655779, registraciis TariRi 25.06.2014 wels, notariusi : mariam navrozaSvili, (misamarTi q.Tbilisi, Temqis 3m/r 4kv kor 57-is mimdebared tel: 598270730), romlis qonebiTac uzrunvelyofilia mogiravnis moTxovna;
                            <br>1.3.	 giravnobis sagani – qoneba, romelic am xelSekrulebis Sesabamisad itvirTeba giravnobiT;
                            <br>1.4.	xelSekruleba – xelSekruleba salombardo momsaxurebis Sesaxeb an winamdebare giravnobis xelSekruleba, koteqstis Sesabamisad;
                            <br>1.5.	 mxareebi – mogiravne da damgiravebeli erTad;
                            <br>muxli 2. xelSekrulebis sagani
                            <br>2.1. damgiravebeli giraos sagniT uzrunvelyofs mogiravnesa da damgiravebels Soris 12.12.2016 wels gaformebuli # 1893 საკრედიტო xelSekrulebiT nakisr valdebulebebs;
                            <br>2.2. giravnobis sagniT agreTve uzrunvelyofilia საკრედიტო xelSekrulebis safuZvelze gaformebuli nebismieri damatebiTi xelSekrulebebiT nakisri valdebulebebis SeusruleblobiT gamowveuli zianis anazRaureba, aseve giravnobis  sagnis Senaxva-realizaciaze, davalianebis iZulebiTi wesiT gadaxdevinebaze mogiravnis mier gaweuli yvela danaxarjis anazRaureba;
                            <br>2.3. giravnobis sagani uzrunvelyofs mogiravnis moTxovnas im moculobiT, rogoric mas aqvs moTxovnis dakmayofilebis momentisaTvis, kerZod, ძირი თანხის, სარგებლის, pirgasamtexlosა თუ  Sesrulebis vadis gadacilebiT gamowveuli zaralis anazRaurebas da agreTve mogiravnis mier dagiravebuli nivTis Senaxvaze da Tanxis iZulebiT gadaxdevinebaze gaweuli xarjebis anazRaurebas. amasTan, im SemTxvevaSi, Tu giravnobis sagnis realizaciidan miRebuli amonagebi ar iqneba sakmarisi mogiravnis moTxovnis dasakmayofileblad, mogiravne uflebamosilia sakuTari moTxovnis dakmayofilebis mizniT moiTxovos damgiraveblis pirad sakuTrebaSi arsebuli uZrav-moZravi qonebis realizacia;
                            <br>2.4. damgiravebeli 2016 wlis 12 დეკემბერს dadebuli  # 1893 საკრედიტო xelSekrulebiT nakisri valdebulebebis Sesrulebis uzrunvelsayofad, mogiravnes giraos saxiT gadascems avtosatransporto saSualebas Semdegi maxasiaTeblebiT:
                            <br>marka, modeli:  NISSAN NOTE
                            <br>gamoSvebis weli: 2007
                            <br>feri: ლურჯი 5/8
                            <br>tipi: ჰეტჩბეკი
                            <br>Zravis moculoba: 1498
                            <br>saregistracio nოmeri: 
                            <br> mesakuTre: აკაკი ელისაშვილი
                            <br>transportis saidentifikacio # NE11041722
                            <br>transportis saregistracio mowmoba:
                            <br>2.5 giravnobis uzrunvelyofili moTxovnis maqsimaluri Tanxa Seadgens 2300 dolars eqvivalenti larSi.
                            <br>muxli 3. mxareTa gancxadebebi da garantiebi
                            <br>3.1. damgiravebeli acxadebs da iZleva garantias imis Sesaxeb, rom giravnobis sagani aris karg mdgomareobaSi, aris nivTobrivad da uflebrivad unakloa.
                            <br>3.2. giravnobis sagnis kargi mdgomareoba niSnavs, rom giravnobis sagans ar gaaCnia raime iseTi saxis nakli, romelic saWiroebs gamosworebas;
                            <br>3.3. nivTobrivi unaklooba niSnavs, rom giravnobis sagani vargisia Cveulebrivi sargeblobisaTvis;
                            <br>3.4. uflebrivi unaklooba niSnavs, rom mesame pirebs ar aqvT da ar SeiZleba hqondeT giravnobis saganze pretenziis wayenebis ufleba;
                            <br>3.5. damgiravebeli acxadebs, rom:
                            <br>3.5.1. marTlzomierad flobs giravnobis sagans sakuTrebis uflebiT, gaaCnia winamdebare xelSekrulebis xelmoweris, dadebisa da SesrulebisaTvis aucilebeli yvela uflebamosileba srulad da am mizniT mis mier mopovebulia yvela aucilebeli Tanxmoba;
                            <br>3.5.2. ar monawileobs arc erT sasamarTlo procesSi, maT Soris administraciul, sisxlis, samoqalaqo samarTalwarmoebaSi mosarCelis, mopasuxis, mesame piris an sxva subieqtis saxiT, riTac safrTxe eqmneba an SeiZleba safrTxe Seeqmnas mis qonebas, sakuTrebis/sargeblobis/mflobelobis uflebas giravnobis saganTan mimarTebiT an/da mis mier winamdebare xelSekrulebis an sxva romelime xelSekrulebis pirobebis Sesrulebas;
                            <br>3.5.3. mis mier mogiravnisaTvis wadgenili informacia winamdebare xelSekrulebis dadebis dRisaTvis aris utyuari da zusti;
                            <br>3.5.4. winamdebare xelSekrulebis dadeba da misi Sesruleba ar gamoiwvevs mis mier adre dadebuli raime xelSekrulebis pirobebis darRvevas;
                            <br>3.5.5. winamdebare xelSekruleba dadebulia mis mier nebayoflobiT da ar warmoadgens mogiravnis an sxva mesame pirTa mxridan Zaladobis, muqaris, motyuebis, Secdomis an/da raime sxva raime negatiuri garemoebis zemoqmedebis Sedegs;
                            <br>3.5.6. misTvis cnobilia, rom mogiravnisaTvis yalbi cnobebis an/da dokumentebis miwodeba giravnobis xelSekrulebis gaformebis mizniT warmoadgens dasjad qmedebas saqarTvelos moqmedi kanonmdeblobiT.
                            <br>muxli 4. xelSekrulebis moqmedebis vada
                            <br>4.1. winamdebare xelSekruleba ZalaSia 12.12.2016 wels dadebuli # 1893 საკრედიტო xelSekrulebis moqmedebis sruli periodis ganmavlobaSi, aseve mis safuZvelze dadebuli nebismieri da yvela damatebiTi xelSekrulebis an/da SeTanxmebis moqmedebis sruli periodis ganmavlobaSi.
                            <br>muxli 5. dazRveva
                            <br>5.1. damgiravebeli valdebulia, mogiravnis moTxovnis SemTxvevaSi, sakuTari saxeliTa da xarjebiT daazRvios giravnobis sagani;
                            <br>5.2. dazRveva xorcieldeba mogiravnis mier moTxovnili riskebis dasafarad;
                            <br>5.3. mogiravnes aqvs ufleba daikmayofilos sakuTari moTxovnebi damgiraveblis mier miRebuli an/da misaRebi sadazRvevo anazRaurebidan;
                            <br>5.4. sadazRvevo polisi unda moicavdes da faravdes am xelSekrulebis moqmedebis srul periods;
                            <br>5.5. sadazRvevo polisi unda iTvaliswinebdes mogiravnes, rogorc dazRvevis amonagebis erTpirovnul mimRebs;
                            <br>5.6. sadazRvevo polisi unda gansazRvravdes, rom mogiravnis mimarT dauSvebelia raime saxis moTxovnis wayeneba premiis an sxva Tanxebis gadaxdasTan dakavSirebiT.
                            <br>muxli 6. mxareTa uflebebi da movaleobebi
                            <br>6.1. giravnobis sagani inaxeba damgiravebelTan;
                            <br>6.2. damgiravebeli valdebulia miiRos yvela saWiro zoma giravnobis sagnis dacvisa da karg mdgomareobaSi SenarCunebisaTvis;
                            <br>6.3. damgiravebeli valdebulia daicvas giravnobis sagani mesame pirebis xelyofisa da moTxovnebisagan;
                            <br>6.4. damgiravebeli valdebulia acnobos mogiravnes nebismieri iseTi garemoebis Sesaxeb, ramac SeiZleba gavlena iqonios giravnobis saganze;
                            <br>6.5. damgiravebeli, mogiravnis  werilobiTi moTxovnis SemTxvevaSi da mis safuZvelze, warmoudgens mogiravnes giravnobis saganTan dakavSirebul nebismier informacias da dokuments;
                            <br>6.6. giravnobis sagnis Secvla SesaZlebelia mxolod mogiravnis winaswari werilobiTi TanxmobiT;
                            <br>6.7. giravnobis sagnis Semdgomi dagiraveba dauSvebelia mogiravnis winaswari werilobiTi Tanxmobis gareSe;
                            <br>6.8. im SemTxvevaSi, Tu giravnobis sagani daiRupeba, daziandeba an Tu masze sxvagvarad Sewydeba sakuTrebis ufleba saqarTvelos moqmedi kanonmdeblobiT gaTvaliswinebul SemTxvevaSi, damgiravebeli valdebulia mogiravnis mier gansazRvrul vadaSi aRadginos igi an Secvalos igive Rirebulebis sxva qonebiT;
                            <br>6.9. am xelSekrulebis 6.8 muxliT muxliT gaTvaliswinebuli mogiravnis mier gansazRvruli vada unda iyos gonivruli;
                            <br>6.10. damgiravebels aqvs giravnobis sagnis daniSnulebisamebr gamoyenebis ufleba;
                            <br>6.11. giravnobis ufleba aseve vrceldeba giravnobis sagnidan miRebul nayofzec;
                            <br>6.12. am xelSekrulebis moqmedebis periodSi damgiravebels mogiravnis winaswari werilobiTi Tanxmobis gareSe ar aqvs ufleba sasyidlianad an usasyidlod gadasces sakuTrebis ufleba giravnobis saganze mesame pirebs;
                            <br>6.13. am xelSekrulebis moqmedebis sruli periodis ganmavlobaSi damgiravebels ar aqvs ufleba droebiT sasyidlian an usasyidlo sargeblobaSi gadasces giravnobis sagani mesame pirebs;
                            <br>6.14. am xelSekrulebis moqmedebis sruli periodis ganmavlobaSi damgiravebels ar aqvs am xelSekrulebis 6.12 da 6.13 muxlebSi aRniSnuli qmedebis garda giravnobis sagnis sxvagvari gankargvis uflebamosileba;
                            <br>6.15. am xelSekrulebis miznebisaTvis am xelSekrulebis 6.12, 6.13 da 6.14 muxlebiT gaTvaliswinebul SemTxvevebs uTanabrdeba damgiraveblis mier mesame pirebisaTvis giravnobis saganTan dakavSirebuli mindobilobis/rwmunebulebis gacema rogorc sruli, ise arasruli uflebamosilebiT. aRniSnuli moqmedeba damgiraveblis mxridan namdvilia mxolod im SemTxvevaSi, Tuki arsebobs mogiravnis mier gacemuli winaswari werilobiTi Tanxmoba;
                            <br>6.16. damgiraveblis mier am xelSekrulebis 6.12, 6.13, 6.14 da 6.15 muxlebiT gaTvaliswinebuli valdebulebis darRvevis SemTxvevaSi mogiravne uflebamosilia dauyovnebliv moiTxovos sakuTari moTxovnis dakmayofileba;
                            <br>6.17. mogiravne uflebamosilia mosTxovos damgiravebels giravnobis sagnis misTvis samarTavad gadacema, Tuki irkveva, rom damgiravebeli ver asrulebs mis mier nakisr valdebulebebs;
                            <br>6.18. mogiravnes ufleba aqvs moiTxovos giravnobis sagnis mis sakuTrebaSi gadacema damgiraveblis mier nakisri valdebulebebis Seusruleblobis an arajerovani Sesrulebis SemTxvevaSi;
                            <br>6.19. damgiravebels ar aqvs ufleba mTlianad an nawilobriv gadaakisros sxva pirs/pirebs sakuTari valdebulebis Sesruleba mogiravnis winaswari werilobiTi Tanxmobis gareSe;
                            <br>6.20damgiravebels ekrZaleba a/manqanis marTva nasvam, narkotikul da kanoniT akrZaluli yvelanairi saSualebebis zemoqmedebis qveS. Ddamgiravebels aseve ekrZaleba a/manqanis gadacema marTvis uflebis armqone, arasrulwlovan, sadazRveo polisSi ar miTiTebul da zemoT aRniSnuli kanoniT akrZaluli nivTierebebis da saSualebebis zemoqmedebis qveS myofi pirebisadmi.
                            <br>6.21  6.20 punqtiT dadgenili valdebulebebis Seusruleblobis SemTxvevaSi xelSekruleba CaiTvleba darRveulad da dagiravebuli a/manqana gaCerdeba მოგირავნის mier arCeul avtosadgomze ვალდებულებბის სრულად შესრულებამდე.
                            <br>muxli 7. mogiravnis interesebis dakmayofileba
                            <br>7.1. mxareebi Tanxmdebian, rom giravnobiT uzrunvelyofili romelime valdebulebis droulad da jerovnadSeusruleblobis SemTxvevaSi mogiravnes ufleba eqneba sakuTari arCevaniT gamoiyenos qvemoT CamoTvlili RonisZiebaTagan nebismieri:
                            <br>7.1.1. giravnobis sagnis realizacia ganxorcieldes auqcionis meSveobiT saqarTvelos samoqalaqo kodeqsiTa da `saaRsrulebo warmoebaTa Sesaxeb~ saqarTvelos kanoniT dadgenili wesiT; amasTan, damgiravebeli am xelSekrulebis xelmoweriT winaswar acxadebs Tanxmobas auqcionis Catarebis mizniT mogiravnis mier SerCeuli piris specialistad daniSvnaze. სპეციალისტის anazRaureba ganisazRvreba im valdebulebebis Rirebulebis aranakleb 0.5%-iT da araumetes 5%-iT, romlis Sesasrulebladac tardeba auqcioni;
                            <br>7.1.2. mogiravne uflebamosilia pirdapir miiRos sakuTrebaSi giravnobis sagani (ssk 2601 muxli). giravnobis sagnis sakuTrebaSi pirdapiri formiT gadasvlis miznebisaTvis es xelSekruleba ganixileba xelSekrulebad registraciis Sesaxeb da Sinagan saqmeTa saministros momsaxurebis saagento valdebulia mogiravnis gancxadebis safuZvelze giravnobis sagnis mesakuTred daaregistriros mogiravne. mogiravnis gancxadebas registraciis Sesaxeb Tan unda daerTos am xelSekrulebis asli;
                            <br>7.1.3. saqarTvelos samoqalaqo kodeqsis 283-e muxlis Sesabamisad, giravnobis sagnis realizacia ganaxorcielos pirdapiri miyidvis wesiT. am SemTxvevaSi mogiravnis, rogorc damgiraveblis mier saTanado uflebamosilebiT aRWurvil pirs ufleba eqneba dados moZravi nivTis (aramaterialuri qonebrivi sikeTis) nasyidobis xelSekruleba, romlis safuZvelzec mogiravne, damgiraveblis (mesakuTris) saxeliT gadascems sakuTrebis uflebas giravnobis saganze mis mier SerCeul nebismier pirs;
                            <br>7.1.4. giravnobis sagnis gayidva miandos specialur savaWro dawesebulebas;
                            <br>7.2. im SemTxvevaSi, Tu giravnobis sagnis realizaciidan amonagebi Tanxa sakmarisi ar iqneba giravnobiT uzrunvelyofili moTxovnis dasafarad, uzrunvelyofili moTxovna dakmayofilebulad CaiTvleba mxolod amonagebi Tanxis toli odenobiT;
                            <br>7.3. damgiravebeli valdebulia mogiravnis moTxovnis miRebidan araugvianes 02 (ori) kalendaruli dRisa uzrunvelyos nebismieri im sabuTis (gancxadebis, SeTaxmebis da a.S.) xelmowera da mogiravnisaTvis dauyovnebliv gadacema, romelic auclebelia mogiravnis mier am xelSekrulebis 7.1  muxlSi CamoTvlili uflebis/uflebebis srulfasovani da daubrkolebeli realizaciisaTvis;
                            <br>7.4. am xelSekrulebis 7.3 muxliT nakisri valdebulebis droulad da jerovnad Seusruleblobis gamo damgiravebeli valdebuli iqneba gadauxados mogiravnes pirgasamtexlo davalianebis Tanxis 0.5%-is odenobiT;
                            <br>7.5. im SemTxvevaSi, Tu giravnobis sagnis realizaciidan miRebuli amonagebi ar iqneba sakmarisi mogiravnis moTxovnebis dasakmayofileblad, an/da giravnobis sagnidan (mogiravnis/kreditoris) interesebis sxvagvari daukmayofileblobis SemTxvevaSi, mogiravne uflebamosilia moiTxovos damgiraveblis sakuTrebaSi arsebuli uZrav-moZravi qonebis realizacia sakuTari moTxovnis srulad dakmayoflebis mizniT.
                            <br>muxli 8. sakontaqto informacia
                            <br>8.1. mxareTa Soris kontaqti xorcieldeba am xelSekrulebaSi aRniSnul misamarTebzე ან el/fostiT;
                            <br>8.2. damgiravebeli acxadebs, rom Tanaxmaa miiRos mogiravnis mier gamogzavnili yvelanairi Setyobineba am xelSekrulebaSi miTiTebul misamarTze მათ შორის ელ.ფოსტაზე;
                            <br>8.3. mxareebi Tanxmdebian, rom yvela Setyobineba, romelic gaigzavneba am xelSekrulebaSi miTiTebul damgiraveblis misamarTსა თუ ელ/ფოსტაზე miuxedavad misi Cabarebisa an miRebaze uaris Tqmisa, CaiTvleba damgiraveblis mier miRebulad;
                            <br>8.4. im SemTxvevaSi, Tu damgiravebeli Seicvlis საკონტაქტო მონაცემებს - adgilsamyofels (misamarTs), ან ელ ფოსტას, igi valdebulia aRniSnulis Taobaze werilobiT acnobos mogiravnes;
                            <br>8.5. am xelSekrulebis 8.4 muxliT gaTvaliswinebul SemTxvevaSi, Tuki damgiravebeli werilobiT acnobebs mogiravnes mis Secvlil საკონტაქტო მონაცემებს - adgilsamyofels (misamarTs), ან ელ ფოსტას, sakontaqto მონაცემად CaiTvleba ახალი მისამართი da masze srulad gavrceldeba am xelSekrulebis 8.2 da 8.3 muxlebiT dadgenili wesebi;
                            <br>8.6. am xelSekrulebis 8.4 muxliT gaTvaliswinebul SemTxvevaSi, Tuki damgiravebeli werilobiT ar acnobebs mogiravnes mis საკონტაქტო მონაცემებს - adgilsamyofels (misamarTs), ან ელ ფოსტას, sakontaqto misamarTad CaiTvleba am xelSekrulebaSi miTiTebuli მონაცემები და მასზე gavrceldeba am xelSekrulebis 8.2 da 8.3 muxlebiT dadgenili wesebi;
                            <br>8.7. damgiraveblis mier winamdebare xelSekrulebis 8.4 muxliT gaTvalswinebuli werilobiTi Setyobinebis valdebulebis darRvevis SemTxvevaSi, dauSvebelia raime saxis pretenziis dayeneba mogiravnis mimarT, garda im SemTxvevisa, rodesac calsaxad, araorazrovnad da werilobiTi formiT ar dasturdeba is garemoeba, rom mogiravnisaTvis cnobili iyo damgiraveblis axali misamarTi.
                            <br>muxli 9. damatebiTi debulebebi
                            <br>9.1. winamdebare xelSekrulebaze xelis moweriT mxareebi acxadeben, rom am xelSekrulebis yvela piroba warmoadgens maTi namdvili nebis gamovlenas, isini eTanxmebian am pirobebs da surT xelSekrulebis dadeba aRniSnuli pirobebiT;
                            <br>9.2. winamdebare xelSekruleba ZalaSi Sedis mxareTa mier misi xelmoweris da ssip saqarTvelos Sinagan saqmeTa saministros momsaxurebis saagentoSi registraciis momentidan;
                            <br>9.3. winamdebare xelSekruleba regulirdeba saqarTvelos moqmedi kanonmdeblobiT;
                            <br>9.4. winamdebare xelSekrulebis romelime muxlis, punqtis, daTqmis, winadadebis da a.S. baTloba ar iwvevs mTeli xelSekrulebis baTilobas;
                            <br>9.5. xelSekrulebis monawile romelime mxaris mier uflebis gamouyenebloba ar ganixileba am uflebaze uaris Tqmad;
                            <br>9.6. winamdebare xelSekrulebaSi Setanili cvlilebebi an/da damatebebi ZalaSia mxolod im SemTxvevaSi, Tu isini Sesrulebulia am xelSekrulebis formis dacviT da xelmowerilia mxareTa mier. am wesis darRveviT Setanili nebismieri cvlileba an/da damateba baTilia misi Setanis momentidan da ar warmoSobs iuridiuli mniSnvelobis mqone Sedegebs;
                            <br>9.7. am xelSekrulebis safuZvelze an/da misgan gamomdinare yvela xelSekruleba an/da SeTanxmeba da maTi danarTebi warmoadgenen am xelSekrulebis ganuyofel nawils da maTze srulad vrceldeba am xelSekrulebis yvela regulireba, garda im SemTxvevisa, rodesac sxva xelSekrulebis, SeTanxmebis an/da danarTis mizans ar warmoadgens am xelSekrulebis pirobebSi cvlilebis an/da damatebis Setana an Tu mxareebi damatebiT xelSekrlebaSi, SeTanxmebaSi an/da danarTSi ar SeTanxmebian am wesisagan gansxvavebul wesze;
                            <br>9.8 am xelSekrulebidan gamomdinare mxareTa Soris warmoSobil nebismier davas ganixilavs Tbilisis saqalaqo sasamarTlo. Ppirveli instanciis sasamarTlos mier miRebuli gadawyvetileba unda mieqces dauyonebliv aRsasruleblad saqarTvelos samoqalaqo saproceso kodeqsis 268-e muxlis I1 nawilis Sesabamisad;
                            <br>9.9. xelSekruleba Sedgenilia qarTul enaze, Tanabari iuridiuli Zalis mqone sam identur egzemplarad, romelTagan erTi inaxeba mogiravnesTan, erTi – damgiravebelTan, xolo erTi egzemplari waredgineba Sesabamis saregistracio samxasurs registraciis mizniT.
                             <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">გამსესხებელი: შპს  "თი ჯი მობაილ"</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">მინდობილი პირი  :</label></td>
                                    </tr>
                                </table>
                            </div>    
                            <div style="width:100%;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">მისამართი : ქ .თბილისი, დოლიძის ქ. 25 / 121</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">სახელი, გვარი: აკაკი ელისაშვილი</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">ს/კ 205270277</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">პირ.#  01019068974</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">სს  "საქართველოს ბანკი" ბანკის კოდი : BAGAGE22 ა/ა GE12BG0000000523102000  </label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">მისამართი:  თბილისი, ქსანის ქუჩა, კორპუსი 12ა, ბინა 15  თბილისი, თემქა მე-11 მ/რ, მე-2 კვ, კორპ 25 ბ. 9</label></td>
                                    </tr>
                                </table>
                            </div>
                                 
                            <div style="width:100%;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;"> ტელეფონის ნომერი : (ოფისი);</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;"></label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">ტელეფონის ნომერი  : 579796921 <br> ტელეფონის ნომერი : 579131813 <br> ხელის მომწერი : ვახტანგ ბახტაძე, <br> პოზიცია : მენეჯერი.</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">ტელ. ნომერი:  599199120, 598674067 სალომე, 599797727 მარიამი 598665111 სოფო, აკაკი 598606900 აკაკი, 598404271 ზურა, 591177110 ლერი.</label></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width:100%;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">ელ. ფოსტა:  tgmobail@mail.ru </label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">ელ.ფოსტა: a.elisashvili.1@gmail.com</label></td>
                                    </tr>
                                </table>
                            </div>
                                 
                            <div style="width:100%; margin-top: 60px;">
                                <table style="width:100%;">
                                    <tr style="width:100%;">
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">ხელმოწერა:</label></td>
                                        <td style="width:50%;"><label style="font-size: 12px; text-align: center;">ხელმოწერა:</label></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                   </div';
    }elseif ($file_type == 'payment_schedule'){
        $id_hidden = $_REQUEST['id_hidden'];
        $c_date	   = date('Y-m-d H:i:s');
        
        if ($id_hidden == ''){
            $loan_amount         = $_REQUEST['loan_amount'];
            $month_percent       = $_REQUEST['month_percent'];
            $loan_months         = $_REQUEST['loan_months'];
            $loan_agreement_type = $_REQUEST['loan_agreement_type'];
            $name                = $_REQUEST['name'].' '.$_REQUEST['surname'];
            $month_id            = date('m');
            $day                 = date('d');
            $year                = date('Y');
        }else{
            $loan_amount   = $res[loan_amount];
            $month_percent = $res[percent];
            $loan_months   = $res[loan_months];
            $month_id      = $res[month_id];
            $day           = $res[day];
            $year          = $res[year];
            $name          = $res[name];
        }
        
        $sum_percent = 0;
        $sum_P       = 0;
        
        if ($id_hidden == ''){
            $loan_type  = $loan_agreement_type;
            $PV         = $loan_amount;
            $r          = $month_percent/100;
            $n          = $loan_months;
            $year_month = $month_percent*12;
        }else {
            $loan_type   = $res[loan_type_id];
            $PV          = $res[loan_amount]; //სესხის მოცულობა
            $r           = $res[percent]/100; //პროცენტი თვეში
            $n           = $res[loan_months]; //სესხის ვადა თვეში
            $year_month  = $res[percent]*12;
        } 
        
        
        
        $hint        = 'წლ';
        
        if ($loan_type == 1) {
            
            $P           = $PV*$r;
            $ziri        = 0.00;
            $percent     = $P;
            if ($id_hidden == ''){
                $year_month = $month_percent;
            }else{
                $year_month  = $res[percent];
            }
            $hint        = 'თვ';
            $sum_percent = $n*$percent;
            $sum_P       = $sum_percent+$PV;
        }else {
            $P = ($PV*$r)/(1-(pow((1+$r),-$n))); //ყოველთვიური გადასახდელი
        }
        
        for ($i = 1 ; $i<=$n; $i++){
            if ($id_hidden == ''){
                $month = $month_id +$i;
            }else{
                $month = $res[month_id]+$i;
            }
            
            if ($loan_type == 1 && $i == $n) {
                if ($id_hidden == ''){
                    $P       = $P + $loan_amount;
                    $ziri    = $loan_amount;
                    $PV      = 0.00;
                }else{
                    $P       = $P + $res[loan_amount];
                    $ziri    = $res[loan_amount];
                    $PV      = 0.00;
                }
                
            }elseif ($loan_type != 1){
                $percent      = $PV / $n * $r * $n; //ყოველთვიური გადასახდელი პროცენტი
                $ziri         = $P - $percent; //ყოველთვიური გადასახდელი ძირი
                $PV           = $PV - $ziri; //დარჩენილი ძირი
                $sum_percent += $percent;
                $sum_P        = $sum_P +$P;
            }
            
            if ($month<=12) {
                if ($month<10) {
                    $month = '0'.$month;
                }
                if ($id_hidden == ''){
                    $date = $day.'-'.$month.'-'.$year;
                }else{
                    $date = $res[day].'-'.$month.'-'.$res[year];
                }
            }else{
                $month = $month - 12;
                if ($month<10) {
                    $month = '0'.$month;
                }
                if ($id_hidden == ''){
                    $year  = $year +1;
                    $date = $day.'-'.$month.'-'.$year;
                }else{
                    $year  = $res[year] +1;
                    $date = $res[day].'-'.$month.'-'.$year;
                }
               
            }
            
            $dat.='<tr style="width:100%; border: 1px solid #000;">
                        <td style="width:5%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$i.'<label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$date.'</label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($ziri,2).'</label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($percent,2).'<label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($P,2).'</label></td>
                        <td style="width:19%;"><label style="font-size: 12px; text-align:center;">'.round($PV,2).'</label></td>
                    </tr>';
        }
        
        $data.='<div style="width:100%;">
                <div style="width:100%; font-size: 16px; text-align:center;">სესხის დაფარვის გრაფიკი</div>
                <div style="width:100%; font-size: 14px;">
                    <table style="width:100%; margin-top: 5px;">
                        <tr style="width:100%; border: 1px solid #000;">
                            <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 14px;">კლიენტის სახელი:<label></td>
                            <td style="width:80%;"><label style="font-size: 14px;">'.$name.'</label></td>
                        </tr>
                    </table> 
                </div>
                <div style="width:100%; margin-top: 5px;">
                    <table style="width:100%;">
                        <tr style="width:100%;border: 1px solid #000;">
                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">სესხის მცულობა:<label></td>
                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$loan_amount.'</label></td>
                            <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;"></label></td>
                            <td colspan="2" style="width:40%;"><label style="font-size: 12px; text-align:center;">სესხის გაცემის თარიღი</label></td>
                        </tr>
                        <tr style="width:100%;border: 1px solid #000;">
                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">საპროცემტო სარგ. ('.$hint.'.):<label></td>
                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.round($year_month,2).'</label></td>
                            <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                            <td style="width:10%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თვე</label></td>
                            <td style="width:10%;"><label style="font-size: 12px; text-align:center;">'.$month_id.'</label></td>
                        </tr>
                        <tr style="width:100%;border: 1px solid #000;">
                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">ვადა:<label></td>
                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$n.'</label></td>
                            <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                            <td style="width:10%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">რიცხვი</label></td>
                            <td style="width:10%;"><label style="font-size: 12px; text-align:center;">'.$day.'</label></td>
                        </tr>
                        <tr style="width:100%;border: 1px solid #000;">
                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">საშეღავათო პერიოდი:<label></td>
                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                            <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                            <td style="width:10%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">წელი</label></td>
                            <td style="width:10%;"><label style="font-size: 12px; text-align:center;">'.$year.'</label></td>
                        </tr>
                        <tr style="width:100%;border: 1px solid #000;">
                            <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">საკომისიო წინასწარ(%):<label></td>
                            <td colspan="4" style="width:20%;"><label style="font-size: 12px;"><label></td>
                        </tr>
                    </table>
                </div>
                <div style="width:100%; margin-top: 25px; border: 1px solid #000;">
                    <table style="width:100%;">
                        <tr style="width:100%;border: 1px solid #000;">
                            <td colspan="2" style="width:5%;border-right: 1px solid #000;"><label style="font-size: 12px;">სულ პროცენტი<label></td>
                            <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px;">სულ დასაფარი</label></td>
                            <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($sum_percent,2).'</label></td>
                            <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($sum_P, 2).'</label></td>
                            <td style="width:19%;"><label style="font-size: 12px; text-align:center;">0</label></td>
                        </tr>
                        <tr colspan="6" style="height:25px; border: 1px solid #000;">
                            <td colspan="6"style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;"><label></td>
                        </tr>
                        <tr style="width:100%; border: 1px solid #000;">
                            <td style="width:5%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">#<label></td>
                            <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თარიღი</label></td>
                            <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">ძირი<label></td>
                            <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">პროცენტი</label></td>
                            <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">შესატანი</label></td>
                            <td style="width:19%;"><label style="font-size: 12px; text-align:center;">ნაშთი შენატანის შემდეგ</label></td>
                        </tr>';
        $data.=$dat;
        
        $data.='<tr colspan="6" style="height:25px; border: 1px solid #000;">
                    <td colspan="6"style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;"><label></td>
                </tr>
                <tr style="width:100%;border: 1px solid #000;">
                    <td colspan="3"style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">ხელმოწერა ლ:<label></td>
                    <td colspan="3" style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">ხელმოწერა ლ:<label></td>
                </tr>
                </table>
               </div>
            </div>';
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
                                                 client.`type`,
                                                 client.`ltd_id`,
                                                 client.`ltd_name`,
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
                                                 client_trusted_person.trusting_number AS client_trusted_trusting_number,
                                    			 client_trusted_person.trusting_date AS client_trusted_trusting_date,
                                    			 client_trusted_person.trusting_notary AS client_trusted_trusting_notary,
                                    			 client_trusted_person.trusting_notary_address AS client_trusted_actual_trusting_notary_address,
                                    			 client_trusted_person.trusting_notary_phone AS client_trusted_trusting_notary_phone,
                                                 client_car.model AS client_car_model,
                                                 client_car.manufacturing_date AS client_car_manufacturing_date,
                                                 client_car.type_id AS client_car_type_id,
                                                 client_car.engine_size AS client_car_engine_size,
                                                 client_car.registration_number AS client_car_registration_number,
                                                 client_car.`owner` AS client_car_owner,
                                                 client_car.`car_id` AS client_car_car_id,
                                                 client_car.`certificate_id` AS client_car_certificate_id,
                                                 client_car.`color` AS client_car_color,
                                                 client_loan_agreement.datetime AS loan_agreement_datetime,
                                                 client_loan_agreement.loan_type_id AS loan_type_id,
                                                 client_loan_agreement.agreement_type_id AS agreement_type_id,
                                                 client_loan_agreement.loan_amount AS loan_agreement_loan_amount,
                                                 client_loan_agreement.loan_months AS loan_agreement_loan_months,
                                                 client_loan_agreement.percent AS loan_agreement_percent,
                                                 client_loan_agreement.monthly_pay AS loan_agreement_monthly_pay,
                                                 client_loan_agreement.penalty_days AS loan_agreement_penalty_days,
                                                 client_loan_agreement.penalty_percent AS loan_agreement_penalty_percent,
                                                 client_loan_agreement.penalty_additional_percent AS loan_agreement_penalty_additional_percent,
                                                 client_loan_agreement.insurance_fee AS loan_agreement_insurance_fee,
                                                 client_loan_agreement.pledge_fee AS loan_agreement_pledge_fee,
                                                 client_loan_agreement.loan_fee AS loan_agreement_loan_fee,
                                                 client_loan_agreement.proceed_fee AS loan_agreement_proceed_fee,
                                                 client_loan_agreement.rs_message_number AS loan_agreement_rs_message_number,
                                                 client_loan_agreement.pay_day AS loan_agreement_pay_day,
                                                 client_loan_agreement.exchange_rate AS loan_agreement_exchange_rate,
                                                 client_loan_agreement.id AS loan_agreement_id,
                                                 client_loan_agreement.proceed_percent AS loan_agreement_proceed_percent,
                                    			 '',
                                    			 ''
                                            FROM  `client`
                                            LEFT JOIN client_trusted_person ON client_trusted_person.client_id = client.id
                                            LEFT JOIN client_car ON client_car.client_id = client.id
                                            LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = client.id
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
    
    if ($res[type] == 2) {
        $check="checked";
        $check1="";
        $hidde = "";
    }else{
        $check="";
        $check1="checked";
        $hidde = "display:none;";
    }
    
    if ($res[loan_type_id] == 2) {
        $input_hidde = "display:none;";
    }else{
        $input_hidde = "display:block;";
    }
    
    $image = $res['image'];
    if(empty($image)){
        $image = '0.jpg';
    }else{
        $disable_img = 'disabled';
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
                    <spam class="auto_mobile" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'auto_mobile\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/car.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">მანქანა</div></spam>
                    <spam class="agreement" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'agreement\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/handshake.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">ხელშეკრუ<br>ლება</div></spam>
                    <spam class="papers" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'papers\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/file.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">საბუთები</div></spam>
                    <spam class="documents" style="display: block;padding: 10px 5px;  cursor: pointer;" onclick="show_right_side(\'documents\')"><img style="padding-left: 22px;padding-bottom: 5px;" src="media/images/icons/document.png" alt="24 ICON" height="24" width="24"><div style="text-align: center;">დოკუმენ<br>ტები</div></spam>
                </div>
    	        <div style="width:905px; float:left; margin-left:10px;" id="right_side">
                    <fieldset style="display:none;" id="info">
                        <legend>ძირითადი ინფორმაცია</legend>
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
                           <tr style="height:18px;"></tr>
                           <tr>
                               <td style="width: 215px;"><label for="client_name"></label></td>
                               <td style="width: 215px;"><label for="client_surname"></label></td>
        	                   <td style="width: 215px;"><label class="hidde_label" style="'.$hidde.'" for="phone1">"შპს" დასალეხება</label></td>
                               <td style="width: 215px;"><label class="hidde_label" style="'.$hidde.'" for="phone2">საიდენტიპიკაციო კოდი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 215px;">
                                   <table style="width: 100%;">
                                       <tr>
                                           <td style="width: 30px;"><input style="width: 15px;" class="client_type" name = "client_type" type="radio" value="1" '.$check1.'></td>
                                           <td style="width: 180px;"><label style="padding-top: 7px;">ფიზიკური</label></td>
                                       </tr>
                                   </table>
                               </td>
                               <td style="width: 215px;">
                                   <table style="width: 100%;">
                                       <tr>
                                           <td style="width: 30px;"><input style="width: 15px;" class="client_type" name="client_type" type="radio" value="2" '.$check.'></td>
                                           <td style="width: 180px;"><label style="padding-top: 7px;">იურიდიული</label></td>
                                       </tr>
                                   </table>
                               </td>
                               <td style="width: 215px;"><input style="width: 195px; '.$hidde.'" id="ltd_name" type="text" value="'.$res[ltd_name].'"></td>
                               <td style="width: 215px;"><input style="width: 195px; '.$hidde.'" id="ltd_id" type="text" value="'.$res[ltd_id].'"></td>
                           </tr>
                           <tr style="height:30px;"></tr>
                        </table>
                           <table style="width: 15%;">
                              <tr style="height: 35px;">
                                  <td style="width: 15px;"><input style="width: 15px;" id="trust_pers_checkbox" type="checkbox" '.$checked.'></td>
                                  <td style="width: 50px;"><label style="padding-top: 8px; margin-left: -19px;" for="client_name">მინდობილი პირი</label></td>
                              </tr>
                           </table>
                           <table style="width: 100%; '.$table_hidde.'" id="truste_table">
                              <tr>
                                  <td style="width: 215px;"><label for="client_surname">სანოტ. რეგისტრ. ნომერი </label></td>
            	                  <td style="width: 215px;"><label for="phone1">თარიღი</label></td>
                                  <td style="width: 215px;"><label for="phone2">ნოტარიუსი</label></td>
                                  <td style="width: 215px;"><label for="client_surname">ნოტარიუსის მისამართი</label></td>
                              </tr>
                              <tr>
                                  <td style="width: 215px;"><input style="width: 195px;" id="trusting_number" type="text" value="'.$res[client_trusted_trusting_number].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="trusting_date" type="text" value="'.$res[client_trusted_trusting_date].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="trusting_notary" type="text" value="'.$res[client_trusted_trusting_notary].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="trusting_notary_address" type="text" value="'.$res[client_trusted_actual_trusting_notary_address].'"></td>
                              </tr>
                              <tr style="height:18px"></tr>
                              <tr>
                                  <td style="width: 215px;"><label for="client_surname">ნოტარიუსის ტელეფონი</label></td>
                                  <td style="width: 215px;"><label for="client_surname">მ/პ სახელი</label></td>
            	                  <td style="width: 215px;"><label for="phone1">მ/პ გვარი</label></td>
                                  <td style="width: 215px;"><label for="phone2">მ/პ პირადი ნომერ</label></td>
                              </tr>
                              <tr>
                                  <td style="width: 215px;"><input style="width: 195px;" id="trusting_notary_phone" type="text" value="'.$res[client_trusted_trusting_notary_phone].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_name" type="text" value="'.$res[client_trusted_name].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_surname" type="text" value="'.$res[client_trusted_lastname].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_tin" type="text" value="'.$res[client_trusted_pid].'"></td>
                              </tr>
                              <tr style="height:18px"></tr>
                              <tr>
                                  <td style="width: 215px;"><label for="client_surname">მ/პ ტელეფონი</label></td>
                                  <td style="width: 215px;"><label for="client_surname">მ/პ მეილი</label></td>
            	                  <td style="width: 215px;"><label for="phone1">მ/პ ფაქტობრივი მისამართი</label></td>
                                  <td colspan="2" style="width: 215px;"><label for="phone2">მ/პ იურიდიული მისამართი</label></td>
                              </tr>
                              <tr>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_phone" type="text" value="'.$res[client_trusted_phone].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_mail" type="text" value="'.$res[client_trusted_email].'"></td>
                                  <td style="width: 215px;"><input style="width: 195px;" id="client_trust_fact_address" type="text" value="'.$res[client_trusted_actual_address].'"></td>
                                  <td colspan="2" style="width: 215px;"><input style="width: 195px;" id="client_trust_jur_address" type="text" value="'.$res[client_trusted_juridical_address].'"></td>
                              </tr>
                         </table>
                    </fieldset>
                    <fieldset style="display:none;" id="table_person_fieldset">
                        <legend>საკონტაქტო პირი</legend>
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
                    </fieldset>
                    <fieldset style="display:none;" id="auto_mobile">
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
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 220px;"><label for="client_name">ძრავის მოცულობა</label></td>
                               <td style="width: 220px;"><label for="client_surname">რეგისტრაციის ნომერი</label></td>
        	                   <td style="width: 220px;"><label for="phone1">მესაკუთრე</label></td>
                               <td style="width: 220px;"><label for="phone2">საიდენტიპიკაციო ნომერი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_engine" type="text" value="'.$res[client_car_engine_size].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_registration_number" type="text" value="'.$res[client_car_registration_number].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_owner" type="text" value="'.$res[client_car_owner].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="car_ident" type="text" value="'.$res[client_car_car_id].'"></td>
                           </tr>
                           <tr style="height:18px"></tr>
                           <tr>
                               <td colspan="4" style="width: 220px;"><label for="client_name">მოწმობის ნომერი</label></td>
                           </tr>
                           <tr>
                               <td colspan="4" style="width: 220px;"><input style="width: 200px;" id="car_ertificate" type="text" value="'.$res[client_car_certificate_id].'"></td>
                           </tr>
                        </table>
                    </fieldset>
                    <fieldset style="display:none;" id="upload_picture">
                    <legend>სურათები</legend>
                        <table style="width: 883px;">
            	    		<tr>
            					<td style="width: 100%;">
            						<div style="width: 130px;" class="file-uploader">
            							<input id="choose_file" type="file" name="choose_file" class="input" style="display: none; ">
            							<button style="width: 130px;" id="choose_button'.$disable_img.'" class="left" >დაამატე სურათი</button>
            						</div>
            					</td>
            		        </tr>
            				<tr>
            					<td id="img_colum" style="width: 100%;">
            					 '.show_car_picture($res[id]).' 
    							</td>
        					</tr>
            			</table>
                    </fieldset>
            	    <fieldset style="display:none;" id="agreement">
                        <legend>ძირითადი ინფორმაცია</legend>
                        <table style="width: 100%;">
                           <tr>
                               <td style="width: 220px;"><label for="phone1">საკრედიტო ხელშეკრულება N</label></td>
                               <td style="width: 220px;"><label for="phone2">თარიღი</label></td>
                               <td style="width: 220px;"><label for="loan_type">სესხის ტიპი</label></td>
                               <td style="width: 220px;"><label for="client_surname">ხელშეკრულების ტიპი</label></td>
        	               </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px;" id="agreement_number" type="text" value="'.$res[loan_agreement_id].'" disabled="disabled"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="agreement_date" type="text" value="'.$res[loan_agreement_datetime].'"></td>
                               <td><select id="loan_agreement_type" style="width: 195px;">'.loan_type($res[loan_type_id]).'</select></td>
                               <td><select id="agreement_type_id" style="width: 195px;">'.agreement_type($res[agreement_type_id]).'</select></td>
                           </tr>
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 220px;"><label for="client_name">სესხის სრული მოცულობა</label></td>
                               <td style="width: 220px;"><label for="phone2">ყოველთვიური პროცენტი</label></td>
                               <td style="width: 220px;"><label for="client_surname">სესხის სარგებლობის ვადა</label></td>
        	                   <td style="width: 220px;"><label for="phone1">სესხის გაცემის საკომისიო</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px;" id="loan_amount" type="text" value="'.$res[loan_agreement_loan_amount].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="month_percent" type="text" value="'.$res[loan_agreement_percent].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="loan_months" type="text" value="'.$res[loan_agreement_loan_months].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="loan_fee" type="text" value="'.$res[loan_agreement_loan_fee].'"></td>
                               
                           </tr>
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 220px;"><label style="'.$input_hidde.'" class="label_label" for="phone2">ხელშკრ. გაგრძ. საფასური</label></td>
                               <td style="width: 220px;"><label style="'.$input_hidde.'" class="label_label" for="client_name">პროცენტი</label></td>
                               <td style="width: 220px;"><label for="client_surname">სადაზღვევო ხარჯი</label></td>
        	                   <td style="width: 220px;"><label for="phone1">გირავნობის ხარჯი</label></td>
                               
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px; '.$input_hidde.'" id="proceed_fee" type="text" value="'.$res[loan_agreement_proceed_fee].'"></td>
                               <td style="width: 220px;"><input style="width: 200px; '.$input_hidde.'" id="proceed_percent" type="text" value="'.$res[loan_agreement_proceed_percent].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="insurance_fee" type="text" value="'.$res[loan_agreement_insurance_fee].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="pledge_fee" type="text" value="'.$res[loan_agreement_pledge_fee].'"></td>
                               
                           </tr>
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 220px;"><label for="client_name">ყოველთვიურად შეს. თანხა</label></td>
                               <td style="width: 220px;"><label for="client_surname">შემოსავლების სამსახ. შეტყობ. N</label></td>
        	                   <td style="width: 220px;"><label for="phone1">გადახდის რიცხვი</label></td>
                               <td style="width: 220px;"><label for="phone2">ვალუტის კურსი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px;" id="monthly_pay" type="text" value="'.$res[loan_agreement_monthly_pay].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="rs_message_number" type="text" value="'.$res[loan_agreement_rs_message_number].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="pay_day" type="text" value="'.$res[loan_agreement_pay_day].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="exchange_rate" type="text" value="'.$res[loan_agreement_exchange_rate].'"></td>
                           </tr>
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 220px;"><label for="client_surname">ვადაგადაცილებული დღეები</label></td>
                               <td style="width: 220px;"><label for="client_name">ვადაგადაც. პირგასამტეხლო%</label></td>
        	                   <td colspan="2" style="width: 220px;"><label for="phone1">ვადაგადაც. პირგასამტეხლო%</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input style="width: 200px;" id="penalty_days" type="text" value="'.$res[loan_agreement_penalty_days].'"></td>
                               <td style="width: 220px;"><input style="width: 200px;" id="penalty_percent" type="text" value="'.$res[loan_agreement_penalty_percent].'"></td>
                               <td colspan="2" style="width: 220px;"><input style="width: 200px;" id="penalty_additional_percent" type="text" value="'.$res[loan_agreement_penalty_additional_percent].'"></td>
                           </tr>
                        </table>
        	        </fieldset>
                    <fieldset style="display:none;" id="agreement_grafic">
                        <legend>გადახდის გრაფიკი</legend>
                        <table style="width:100%;">
                               <tr>
                                   <td><button id="show_payment_schedule">გრაფიკის ჩვენება</button> </td>
                                   <td><button style="float: right;" id="hidde_payment_schedule">დახურვა</button> </td>
                               </tr>
                               <tr style="height:15px;"><td colspan="2"></td></tr>
                               <tr><td colspan="2" id="payment_schedule_td"></td></tr>
                        </table>
                    </fieldset>
            	    <fieldset style="display:none;" id="papers">
                        <legend>ძირითადი ინფორმაცია</legend>
        	                '.show_papers_file($res).'
                    </fieldset>
        	        <fieldset style="display:none;" id="documents">
                        <legend>ძირითადი ინფორმაცია</legend>
        	                '.show_documents_file($res).'
                    </fieldset>
                </div>
            </fieldset>
        </div>';

	return $data;
}

function show_documents_file($res){
    
   $data = '<div style="margin-top: 15px;">
                <div style="width: 100%; border:1px solid #CCC;float: left;">    	            
            	    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%; float:left;">დასახელება</div>
                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:17%; float:left;">ქმედება</div>
                    <div id="paste_files">
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left; margin-top: 10px;">ხელწერილი.doc</div>
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left; margin-top: 10px;" onclick="show_document(\'receipt\', \'ხელწერილი\')">დეტალურად</div>
                            
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">მიღება-ჩაბარების აქტი.doc</div>
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'acceptance_act\', \'მიღება-ჩაბარების აქტი\')">დეტალურად</div>
                            
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">ვიზეც არის გაფორმებული წერილი კლიენტის მანქანის ჩამორთმევაზე.doc</div>
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Client_car_confiscation\', \'ვიზეც არის გაფორმებული წერილი კლიენტის მანქანის ჩამორთმევაზე\')">დეტალურად</div>
                            
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">გადახდის გრაფიკი.xls</div>
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'payment_schedule\', \'გადახდის გრაფიკი\')">დეტალურად</div>
       
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">თანხმობა.doc</div>
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'approval\', \'თანხმობა\')">დეტალურად</div>
       
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">ხელწერილი დაზღვევაზე.doc</div>
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'receipt_insurance\', \'ხელწერილი დაზღვევაზე\')">დეტალურად</div>
       
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">თავდებობა.doc</div>
                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'guarantee\', \'თავდებობა\')">დეტალურად</div>';
                        
                        if($res[loan_type_id] == 2 && $res[agreement_type_id]==1){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% გრაფიკით - შ.პ.ს + განუბაჟებელი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Schedule_ltd_nocustoms\', \'საკრედიტო ხელშეკრულება 3% გრაფიკით - შ.პ.ს + განუბაჟებელი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 2 && $res[agreement_type_id]==2){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% გრაფიკით - შ.პ.ს + მინდობილი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Schedule_ltd_trusted\', \'საკრედიტო ხელშეკრულება 3% გრაფიკით - შ.პ.ს + მინდობილი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 2 && $res[agreement_type_id]==3){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% გრაფიკი - შ.პ.ს.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Schedule_ltd\', \'საკრედიტო ხელშეკრულება 3% გრაფიკი - შ.პ.ს\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 2 && $res[agreement_type_id]==4){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% გრაფიკი - მინდობილობა + განუბაჟებელი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Schedule_trusted_nocustoms\', \'საკრედიტო ხელშეკრულება 3% გრაფიკი - მინდობილობა + განუბაჟებელი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 2 && $res[agreement_type_id]==5){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% გრაფიკი - მინდობილობა.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Schedule_trusted\', \'საკრედიტო ხელშეკრულება 3% გრაფიკი - მინდობილობა\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 2 && $res[agreement_type_id]==6){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% გრაფიკი - განუბაჟებელი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Schedule_nocustoms\', \'საკრედიტო ხელშეკრულება 3% გრაფიკი - განუბაჟებელი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 2 && $res[agreement_type_id]==7){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% გრაფიკი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Schedule\', \'საკრედიტო ხელშეკრულება 3% გრაფიკი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 1 && $res[agreement_type_id]==8){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% 70ლ 15% - განუბაჟებელი+მინდობილი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'agreement_nocustoms_trusted\', \'საკრედიტო ხელშეკრულება 3% 70ლ 15% - განუბაჟებელი+მინდობილი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 1 && $res[agreement_type_id]==9){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% 70ლ 15% - განუბაჟებელი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'agreement_nocustoms\', \'საკრედიტო ხელშეკრულება 3% 70ლ 15% - განუბაჟებელი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 1 && $res[agreement_type_id]==10){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% 70ლ 15% - საკომისიო 3% - შპს + განუბაჟებელი.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'agreement_fee_ltd_nocustoms\', \'საკრედიტო ხელშეკრულება 3% 70ლ 15% - საკომისიო 3% - შპს + განუბაჟებელი\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 1 && $res[agreement_type_id]==11){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% 70ლ 15%  - საკომისიო 3% - შპს + მინდობილობა.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'agreement_fee_ltd_trusted\', \'საკრედიტო ხელშეკრულება 3% 70ლ 15%  - საკომისიო 3% - შპს + მინდობილობა\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 1 && $res[agreement_type_id]==12){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% 70ლ 15% - საკომისიო 3% - მინდობილობა.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'agreement_fee_trusted\', \'საკრედიტო ხელშეკრულება 3% 70ლ 15% - საკომისიო 3% - მინდობილობა\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 1 && $res[agreement_type_id]==13){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% 70ლ 15% - საკომისიო 3%.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'agreement_fee_\', \'საკრედიტო ხელშეკრულება 3% 70ლ 15% - საკომისიო 3%\')">დეტალურად</div>';
                        }elseif ($res[loan_type_id] == 1 && $res[agreement_type_id]==14){
                            $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">საკრედიტო ხელშეკრულება 3% 70ლ 15%  - საკომისიო 3% - შპს.doc</div>
                                    <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'agreement_fee_ltd\', \'საკრედიტო ხელშეკრულება 3% 70ლ 15%  - საკომისიო 3% - შპს\')">დეტალურად</div>';
                        }
                        
                        $data.='<div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:80%;float:left;">გირავნობა.doc</div>
                                <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:17%; float:left;" onclick="show_document(\'Pledge\', \'გირავნობა\')">დეტალურად</div>
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
function show_car_picture($local_id){

    $picture_tbale = mysql_query(" SELECT   file.`name`,
                                            file.`rand_name`,
                                            file.`id`
                                    FROM    `car_picture`
                                    JOIN    file ON file.id = car_picture.file_id
                                    WHERE   car_picture.`client_id` = '$local_id' AND file.`actived` = 1");
    
    $str_file_picture = '';
    $check=mysql_num_rows($picture_tbale);
    
    if ($check>0) {
        $str_file_picture .= '<table style="width: 100%;">
                    				<tr style="width: 100%;">';
        $i=0;
        while ($res_picture = mysql_fetch_assoc($picture_tbale)){
            $i = $i+1;
            $str_file_picture .= ' <td style="width: 115px;">
            						  <table style="width: 115px;">
            							    <tr style="width: 115px;">
                							    <td style="width: 115px;">
                							         <img width="105" height="85" id="upload_img" src="media/uploads/file/'.$res_picture[rand_name].'" />
                							    </td>
            							    </tr>
            							    <tr style="width: 115px;">
                							    <td style="width: 115px;">
                							         <a style="margin-left: 20px;" href="#" onclick="view_image('.$res_picture[id].')" class="complate">View</a> | <a href="#" id="delete_image" image_id="'.$res_picture[id].'" class="delete">Delete</a>
                							    </td>
            							    </tr>
            						    </table>
            						</td>';
            if ($i%7 == 0) {
                $str_file_picture.='</tr><tr>';
            }
        }
        $col = $i%7;
        
        $str_file_picture.=' <td colspan="'.$col.'"></td>
                            </tr>
                            </table>';
    
    }
    return $str_file_picture;
}
?>