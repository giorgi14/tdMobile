<?php
require_once('../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("  SELECT    client.id,
                        					DATE_FORMAT(client_loan_agreement.datetime,'%d/%m/%Y'),
                        					client_car.model,
                        					client_loan_agreement.oris_code,
                        					CONCAT('ს/ხ ',client_loan_agreement.id),
                        					IF(client_loan_agreement.loan_type_id =2,'გრაფიკი',client_loan_agreement.percent),
                        					ROUND(client_loan_agreement.loan_amount,2),
                        					client_loan_agreement.exchange_rate,
                        					ROUND(client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate,2),
		                                    '?',
                        					'?',
                        					'?',
		                                    '?',
                        					'?',
                        					'?',
		                                    '?',
                        					'?'
                                    FROM     `client`
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = `client`.id 
                                    LEFT JOIN client_car ON client_car.client_id = `client`.id 
                                    WHERE    `client`.actived = 1 ");

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
