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
		
		$rResult = mysql_query("SELECT   client.id,
                        				 DATE(client_loan_agreement.datetime),
		                                 CASE 
                                            WHEN client.`name` != '' THEN CONCAT(client.`name`,' ',client.lastname, ' | ', client_car.car_marc, ' | ', client_car.registration_number)
                                            WHEN client.`name` = ''  THEN CONCAT(client.ltd_name, ' | ', client_car.car_marc, ' | ', client_car.registration_number)
                                         END AS `name`,
                        				 client_loan_agreement.oris_code,
                        				 IF(client.attachment_id = 0, 
	  	                                        IF(ISNULL(client.sub_client),
	  	                                        CONCAT('N',IF(client.id<286, client.exel_agreement_id, client_loan_agreement.id)),
	  	                                        CONCAT('N',IF(client.id<286, client.exel_agreement_id, client_loan_agreement.id),'/N',
  	                                           (SELECT IF(clt.id<286, clt.exel_agreement_id, client_loan_agreement.id) 
                                                FROM   client_loan_agreement 
		                                        join   client AS clt ON clt.id = client_loan_agreement.client_id
                                                WHERE  client_loan_agreement.client_id = client.sub_client))),
	  	                                        CONCAT('N',(SELECT IF(cl.id<286, cl.exel_agreement_id, client_loan_agreement.id) 
                                                            FROM   client_loan_agreement 
                                                            join client AS cl ON cl.id = client_loan_agreement.client_id
    	                                                    WHERE  client_loan_agreement.client_id = client.attachment_id),
	  	                                                    ' დ.',client_loan_agreement.attachment_number
	  	                                 )),
                        				 client_loan_schedule.remaining_root,
                        				 client_loan_schedule.pay_date,
                        				 client_loan_schedule.percent,
                        				 DATE((SELECT MAX(money_transactions_detail.pay_datetime) AS pay_date
                                               FROM  `money_transactions`
                                               JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                               WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id AND money_transactions_detail.`status` = 1)) AS pay_date_percent,
                        				 ROUND((SELECT SUM(money_transactions_detail.pay_percent) AS pay_percent
                                                FROM `money_transactions`
                                                JOIN  money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                                WHERE money_transactions.client_loan_schedule_id = client_loan_schedule.id AND money_transactions_detail.`status` = 1),2) AS pay_percent,
                        				 DATE((SELECT MAX(money_transactions_detail.pay_datetime) AS pay_date
                                               FROM  `money_transactions`
                                               JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                               WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id AND money_transactions_detail.`status` = 1)) AS pay_date_root,
                                         ROUND((SELECT SUM(money_transactions_detail.pay_root)AS pay_percent
                                                FROM  `money_transactions`
                                                JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                                WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id AND money_transactions_detail.`status` = 1),2) AS pay_root,
                                		 CASE 
                                            WHEN client_loan_agreement.loan_currency_id = 1 AND client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.remaining_root + client_loan_schedule.percent,2)
                                            WHEN client_loan_agreement.loan_currency_id = 1 AND client_loan_schedule.`status` = 1 THEN ROUND(client_loan_schedule.remaining_root,2)
                                            WHEN client_loan_agreement.loan_currency_id = 2 AND client_loan_schedule.`status` = 0 
                                            THEN ROUND((client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate) + 
								                       (client_loan_schedule.percent * (SELECT   cource 
																						FROM     cur_cource
																						WHERE    actived = 1
																						ORDER BY id DESC
																						LIMIT 1)),2)
                                            WHEN client_loan_agreement.loan_currency_id = 2 AND client_loan_schedule.`status` = 1 THEN ROUND(client_loan_schedule.remaining_root*client_loan_agreement.exchange_rate,2)
                                         END AS nashti
                                FROM     client_loan_schedule
                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN     client ON client.id = client_loan_agreement.client_id
		                        JOIN     client_car ON client.id = client_car.client_id
                                WHERE    client_loan_schedule.actived = 1 
                    		    AND      DATE_FORMAT(client_loan_schedule.pay_date,'%m') = $filt_month
                    		    AND      YEAR(client_loan_schedule.pay_date) = $filt_year
                                ORDER BY client_loan_schedule.pay_date, `name`");

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
