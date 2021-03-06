<?php
require_once 'classes/core.php';
require_once 'excel_reader2.php';
?>
<?php
/* ******************************
 *	File Upload aJax actions
 * ******************************
 */

$action = $_REQUEST['act'];
$error	= '';
$data	= '';

switch ($action) {
	case 'upload_file':
		$element		= 'choose_file';
		$file_name		= $_REQUEST['file_name'];
		$type			= $_REQUEST['type'];
		$path			= $_REQUEST['path'];
		$user_id		= $_SESSION['USERID'];
		$path			= $path . $file_name . '.' . $type;
		

		if (! empty ( $_FILES [$element] ['error'] )) {
			
			switch ($_FILES [$element] ['error']) {
				case '1' :
					
					 $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2' :
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3' :
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4' :
					
					$error = 'No file was uploaded.';
					break;
				case '6' :
					$error = 'Missing a temporary folder';
					break;
				case '7' :
					$error = 'Failed to write file to disk';
					break;
				case '8' :
					$error = 'File upload stopped by extension';
					break;
				case '999' :
				default :
					$error = 'No error code avaiable';
					
			 }
			 
			} elseif (empty ( $_FILES [$element] ['tmp_name'] ) || $_FILES [$element] ['tmp_name'] == 'none') {
				$error = 'No file was uploaded..';
			} else {
	
			    $filename = $_FILES [$element] ['tmp_name'];
			
				$data = new Spreadsheet_Excel_Reader($filename);
				$r    = $data->rowcount($sheet_index=0); 
				$i    = 2;
				
				$check = 0;
				
				while ($i<=$r){
					$status=1;
					if (!empty($data->val($i,'A')) || !empty($data->val($i,'C'))) {
					    
					    $afphone = '995'.$data->val($i,'AF');
					    $client_sms = 0;
					    if ($data->val($i,'AG') == 1) {
					        $client_sms = 1;
					    }
					    
					    if (!empty($data->val($i,'C'))) {
					        $type = "2";
					    }
    					//კლიენტი
    					$check_atachment = mysql_fetch_array(mysql_query("SELECT client.id 
                                                                          FROM   client 
                                                                          WHERE  client.exel_agreement_id = '".$data->val($i,'E')."' 
                                                                          LIMIT 1"));
    					
    					$client = mysql_query("INSERT INTO `client` 
                                                          (`user_id`, `exel_agreement_id`, `attachment_id`, `datetme`, `type`, `name`, `lastname`, `phone`,   `ltd_name`, `sms_sent`, `actived`) 
                                                    VALUES 
                                                          ('1', '".$data->val($i,'E')."', '$check_atachment[id]', NOW(), '$type', '".$data->val($i,'A')."', '".$data->val($i,'B')."', '$afphone', '".$data->val($i,'C')."', '$client_sms', '1')");
    					$client_id = mysql_insert_id();
    					
    					//საკონტქტო პირი
    					
    					if ($data->val($i,'AC') != '') {
    					    $ac_phone = '995'.$data->val($i,'AC');
        					$client_contact = mysql_query("INSERT INTO `client_person`
                                                					  (`user_id`, `client_id`, `datetime`, `phone`, `sms_sent`, `actived`)
                                                			    VALUES
                                                					  ('1', '$client_id', NOW(), '$ac_phone', '1', '1')");
    					}
    					
    					if ($data->val($i,'AD') != '') {
    					    $ad_phone = '995'.$data->val($i,'AD');
        					$client_contact = mysql_query("INSERT INTO `client_person`
                                                					  (`user_id`, `client_id`, `datetime`, `phone`, `sms_sent`, `actived`)
                                                			    VALUES
                                                					  ('1', '$client_id', NOW(), '$ad_phone', '1', '1')");
    					}
    					
    					if ($data->val($i,'AE') != '') {
    					    $ae_phone = '995'.$data->val($i,'AE');
        					$client_contact = mysql_query("INSERT INTO `client_person`
                                            					      (`user_id`, `client_id`, `datetime`, `phone`, `sms_sent`, `actived`)
                                            					VALUES
                                            					      ('1', '$client_id', NOW(), '$ae_phone', '1', '1')");
    					}
    					
    					// თავდები პირი
    					$guarantor_sms = 0;
    					if ($data->val($i,'AI') == 1) {
    					    $guarantor_sms = 1;
    					}
    					 
    					if ($data->val($i,'AH') != '') {
    					    $ah_phone = '995'.$data->val($i,'AH');
        					$client_guarantor = mysql_query("INSERT INTO `client_quarantors` 
                                                                        (`user_id`, `datetime`, `client_id`, `phone`, `sms_sent`, `actived`) 
                                                                  VALUES 
                                                                        ('1', NOW(), '$client_id', '$ah_phone', '$guarantor_sms', '1')");
    					}
    					
    					// კლიენტის მანქანა
    					
    				    $client_car = mysql_query("INSERT INTO `client_car` 
                                        					  (`user_id`, `datetime`, `client_id`, `car_marc`, `registration_number`, `actived`) 
                                        		        VALUES 
                                        					  ('1', NOW(), '$client_id', '".$data->val($i,'L')."', '".$data->val($i,'M')."', '1')");
    					
    					// მანქანის დაზღვევა
    					
    					if ($data->val($i,'AJ') != '' && $data->val($i,'AK') != '' && $data->val($i,'AN') && $data->val($i,'AO')) {
    					    
    					    $start = $data->val($i,'AN');
    					    $end = $data->val($i,'AO');
    					    
        					$car_insurance = mysql_query("INSERT INTO `car_insurance_info` 
                                                                     (`user_id`, `datetime`, `client_id`, `car_loan_amount`, `car_real_price`, `car_insurance_amount`, `ins_payy`, `car_insurance_start`, `car_insurance_end`, `status`, `actived`) 
                                                               VALUES 
                                                                     ('1', NOW(), '$client_id', '".$data->val($i,'AL')."', '".$data->val($i,'AJ')."',  '".$data->val($i,'AK')."', '".$data->val($i,'AL')."', '$start', '$end', '0', '1')");
    					}
    					
    					// ხელშეკრულება
    					$loan_date = $data->val($i,'H');
    					
    					$client_agreement = mysql_query("INSERT INTO `client_loan_agreement` 
                                                                    (`user_id`, `datetime`, `attachment_number`, `client_id`, `loan_type_id`, `loan_currency_id`, `oris_code`, `loan_amount`, `loan_months`, `percent`, `penalty_days`, `penalty_percent`, `loan_beforehand_percent`, `penalty_additional_percent`,  `proceed_fee`, `exchange_rate`, `status`, `canceled_status`, `actived`) 
                                                              VALUES 
                                                                    ('1', '$loan_date', '".$data->val($i,'F')."',  '$client_id', '".$data->val($i,'G')."', '".$data->val($i,'I')."', '".$data->val($i,'D')."', '".$data->val($i,'J')."', '".$data->val($i,'O')."', '".$data->val($i,'N')."',  '".$data->val($i,'Z')."', '".$data->val($i,'Y')."', '".$data->val($i,'P')."', '".$data->val($i,'AA')."', '".$data->val($i,'Q')."', '".$data->val($i,'K')."', '1',  '0', '1')");
    					
    					$client_loan_agreement_id = mysql_insert_id();
    					
    					
    					// გრაფიკი
    					$agreement_date      = $loan_date;
    					$loan_agreement_type = $data->val($i,'G');
    					$loan_amount         = $data->val($i,'J');
    					$month_percent       = $data->val($i,'N');
    					$loan_months         = $data->val($i,'O');
    					
    					$metoba_date   = $data->val($i,'W');
    					
    					$metoba_tanxa  = $data->val($i,'U')+$data->val($i,'V');
    					$metoba_cource = $data->val($i,'X');
    					$cource_id     = $data->val($i,'I');
    					
    					$darcheni_vali_date = $data->val($i,'S');
    					
    					$mont_pay = insert_shedule($client_loan_agreement_id, $loan_date, $loan_agreement_type, $loan_amount, $month_percent, $loan_months, $darcheni_vali_date, $metoba_tanxa, $metoba_date, $metoba_cource, $cource_id);
    					
    					mysqli_query("UPDATE `client_loan_agreement`
                                         SET `monthly_pay` = '$mont_pay'
                                      WHERE  `id`          = '$client_loan_agreement_id'");
    				}	
    					$i++;
    			}
				echo 1;
				if (file_exists($path)) {
					unlink($path);
				}
			}

		break;

}

function insert_shedule($client_loan_agreement_id, $agreement_date, $loan_agreement_type, $loan_amount, $month_percent, $loan_months, $darcheni_vali_date, $metoba_tanxa, $metoba_date, $metoba_cource, $cource_id){
        
        $date       = date_create($agreement_date);
        $month_id   = date_format($date, 'm');
        $day        = date_format($date, 'd');
        $year       = date_format($date, 'Y');
        $year_start = date_format($date, 'Y');

        $loan_type  = $loan_agreement_type;
        $PV         = $loan_amount;
        $r          = $month_percent/100;
        $n          = $loan_months;
        $year_month = $month_percent*12;
        
        if ($loan_type == 1) {
            $P           = $PV*$r;
            $ziri        = 0.00;
            $percent     = $P;
            $year_month  = $month_percent;
            $sum_percent = $n*$percent;
            $sum_P       = $sum_percent+$PV;
        }else {
            $P = ($PV*$r)/(1-(pow((1+$r),-$n)));
        }

        $m = $month_id;

        for ($i = 1 ; $i<=$n; $i++){

            $month = $m +1;
            if ($month==13) {
                $month = 1;
                $year  = $year +1;
                $date  = $year.'-0'.$month.'-'.$day;
                $m     = $month;
            }else{
                if ($month<10) {$month = '0'.$month;}
                $date = $year.'-'.$month.'-'.$day;
                $datemonth = $year.'-'.$month;
                if (date("t", strtotime($datemonth))<$day) {
                    $dayday = '01';
                    $mont = $month+1;
                    if ($mont<10) {
                        $mont = '0'.$mont;
                    }
                    $date  = $year.'-'.$mont.'-'.$dayday;
                }
                $m+=1;
            }

            if ($loan_type == 1 && $i == $n) {
                $P    = $P + $loan_amount;
                $ziri = $loan_amount;
                $PV   = 0.00;
            }elseif ($loan_type != 1){
                $percent = $PV / $n * $r * $n;
                $ziri    = $P - $percent;
                $PV      = $PV - $ziri;
            }
            
            $pay_date = $date;

            $date1 = date_create($pay_date);
            $pay_date = date_format($date1,"Y-m-d");

            $check_pay_date = mysql_query("SELECT id FROM   holidays WHERE  actived = 1 AND holidays.date ='$pay_date'" );

            while(mysql_num_rows($check_pay_date)>0){

                $date1 = date_create($pay_date);
                date_modify($date1,"+1 days");
                $pay_date = date_format($date1,"Y-m-d");

                $check_pay_date = mysql_query("SELECT id FROM   holidays WHERE  actived = 1 AND holidays.date ='$pay_date'");
            }
            
            $shedule_status = 0;
            $activ_status   = 0;
            $cur_date       = date('Y-m-d');
            
            if ($darcheni_vali_date == '') {
                $filt_date = '2017-07-01';
            }else{
                $filt_date = $darcheni_vali_date;
            }
            
            if ($date < $filt_date) {
                $shedule_status = 1;
                $activ_status   = 1;
            }
            mysql_query("INSERT INTO `client_loan_schedule`
                                    (`user_id`, `datetime`, `client_loan_agreement_id`, `number`, `pay_date`, `schedule_date`, `root`, `percent`, `pay_amount`, `remaining_root`, `actived`, `status`, activ_status)
                              VALUES
                                    ('$user_id', NOW(), '$client_loan_agreement_id', '$i', '$pay_date', '$date', '$ziri', '$percent', '$P', '$PV', 1, '$shedule_status', '$activ_status');");
        }
        
        if($metoba_tanxa != 0){
            $res_check_metoba = mysql_fetch_array(mysql_query(" SELECT MAX(id) AS shedule_id
                                                                FROM   client_loan_schedule
                                                                WHERE  client_loan_agreement_id = '$client_loan_agreement_id'
                                                                AND   `status` = 1
                                                                AND    actived = 1"));
            
            if ($res_check_metoba[shedule_id] == '') {
                $res_check_metoba = mysql_fetch_array(mysql_query(" SELECT MIN(id) AS shedule_id
                                                                    FROM   client_loan_schedule
                                                                    WHERE  client_loan_agreement_id = '$client_loan_agreement_id'
                                                                    AND   `status` = 0
                                                                    AND    actived = 1"));
            }
            
            mysql_query("INSERT INTO `money_transactions`
                                    (`datetime`, `user_id`, `client_loan_schedule_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '1', '$res_check_metoba[shedule_id]', '$metoba_date', '$metoba_tanxa', '$metoba_cource', '$cource_id', '$cource_id', '1', '1', '1')");
            
            $transaction_id = mysql_insert_id();
            
            mysql_query("INSERT INTO `money_transactions_detail`
                                    (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `type_id`, `status`, `actived`)
                              VALUES
                                    (NOW(), '1', '$transaction_id', '$metoba_date', '$metoba_tanxa', '$metoba_cource', '$cource_id', '$cource_id', '1', '3', '1')");
        }
        return $P;
}


?>