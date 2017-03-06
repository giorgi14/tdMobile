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
                        				 CONCAT(client.`name`,' ',client.lastname) AS `name`,
                        				 client_loan_agreement.oris_code,
                        				 CONCAT('ს\ხ ', client_loan_agreement.id),
                        				 client_loan_schedule.remaining_root,
                        				 client_loan_schedule.pay_date,
                        				 client_loan_schedule.percent,
                        				 DATE((SELECT MAX(money_transactions.pay_datetime) AS pay_date
                					           FROM  `money_transactions`
                					           WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id)) AS pay_date_percent,
                        				 ROUND((SELECT SUM(money_transactions.pay_percent) AS pay_percent
                								FROM  `money_transactions`
                								WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id),2) AS pay_percent,
                        				 DATE((SELECT MAX(money_transactions.pay_datetime) AS pay_date
                    					       FROM  `money_transactions`
                    					       WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id)) AS pay_date_root,
                                         ROUND((SELECT SUM(money_transactions.pay_root)AS pay_percent
                								FROM  `money_transactions`
                								WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id),2) AS pay_root,
                                		 ROUND(client_loan_schedule.remaining_root)
                                FROM     client_loan_schedule
                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN     client ON client.id = client_loan_agreement.client_id
                                WHERE    client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1 
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
