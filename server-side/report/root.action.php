<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
   
    case 'get_list' :
		$count	    = $_REQUEST['count'];
		$hidden	    = $_REQUEST['hidden'];
		$filt_month	= $_REQUEST['filt_month'];
		$filt_year	= $_REQUEST['filt_year'];
		
		$rResult = mysql_query("SELECT   MAX(client_loan_schedule.id),
                        				 DATE_FORMAT(client_loan_agreement.datetime, '%d/%m%Y'),
                        				 CASE
                        					   WHEN client.id >= (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client_loan_agreement.id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        					   WHEN client.id < (SELECT old_client_id.number FROM `old_client_id` LIMIT 1) THEN CONCAT(client.`name`, ' ', client.lastname, ' / ს/ხ', client.exel_agreement_id, ' / ', client_car.car_marc, ' / ', client_car.registration_number)
                        				 END AS `name`,
                        				 client_loan_agreement.oris_code,
                        				 IF(client.attachment_id = 0, 
                    					 IF(ISNULL(client.sub_client),
                    					 CONCAT('N',IF(client.id<(SELECT old_client_id.number FROM `old_client_id` LIMIT 1), client.exel_agreement_id, client_loan_agreement.id)),
                    					 CONCAT('N',IF(client.id<(SELECT old_client_id.number FROM `old_client_id` LIMIT 1), client.exel_agreement_id, client_loan_agreement.id),'/N',
                        				 (SELECT IF(clt.id<(SELECT old_client_id.number FROM `old_client_id` LIMIT 1), clt.exel_agreement_id, client_loan_agreement.id) 
                    					  FROM   client_loan_agreement 
                    					  join   client AS clt ON clt.id = client_loan_agreement.client_id
                    					  WHERE  client_loan_agreement.client_id = client.sub_client))),
                    					  CONCAT('N',(SELECT IF(cl.id<(SELECT old_client_id.number FROM `old_client_id` LIMIT 1), cl.exel_agreement_id, client_loan_agreement.id) 
        											  FROM   client_loan_agreement 
        											  join   client AS cl ON cl.id = client_loan_agreement.client_id
        											  WHERE  client_loan_agreement.client_id = client.attachment_id),' დ.',client_loan_agreement.attachment_number
                        				 )) AS agreement_number,
                                		 CONCAT(client_loan_agreement.loan_amount,IF(client_loan_agreement.loan_currency_id = 1,' GEL',' USD')) AS loan_amount,
                                         (SELECT DATE_FORMAT(sched.schedule_date,'%d/%m/%Y') FROM client_loan_schedule AS sched WHERE sched.actived = 1 AND sched.id = MAX(client_loan_schedule.id)) AS `schedule_date`,
                                		 (SELECT DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y')
                                          FROM   money_transactions
                                          JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id 
                                          WHERE  money_transactions.client_loan_schedule_id = MAX(client_loan_schedule.id) AND money_transactions_detail.`status` = 1 LIMIT 1) AS pay_datetime,
                                         (SELECT SUM(money_transactions_detail.pay_root)
                                          FROM   money_transactions
                                          JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id 
                                          WHERE  money_transactions.client_loan_schedule_id = MAX(client_loan_schedule.id)) AS pay_root,
                                         (SELECT   ROUND(client_loan_schedule.remaining_root+client_loan_schedule.root,2)
                        				  FROM     client_loan_schedule 
                        				  WHERE    client_loan_schedule.`status` = 0 
                        				  AND      client_loan_schedule.actived = 1
                        				  AND      client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                        				  ORDER BY client_loan_schedule.number asc 
                        				  LIMIT 1 ) AS remaining_root
                                FROM     client_loan_schedule
                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN     client ON client.id = client_loan_agreement.client_id
                                JOIN     client_car ON client_car.client_id = client.id
                                WHERE    client_loan_schedule.actived = 1 
                                AND      MONTH(client_loan_schedule.schedule_date) = '$filt_month'
                                AND      YEAR(client_loan_schedule.pay_date) = '$filt_year'
                                GROUP BY client_loan_schedule.client_loan_agreement_id");

		$data = array("aaData"	=> array());

		while ( $aRow = mysql_fetch_array( $rResult )){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ ){
				$row[] = $aRow[$i];
			}
			$data['aaData'][] = $row;
		}
		
		break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

?>
