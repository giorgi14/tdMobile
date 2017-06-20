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
		$filt_month1 = $filt_month+1;
		$filt_month2 = $filt_month+2;
		$filt_day	= $_REQUEST['filt_day'];
		$today      = date("Y-m");
		$c_day        = date("d");
		if ($filt_month<10) {
		    $filt_month = '0'.$filt_month;
		}
		$filt_date  = $filt_year.'-'.$filt_month;
		
		if ($filt_date == $today) {$day = $c_day;}else{$day = 31;}
		
		$AND        = '';
		if ($filt_day > 0) {
		    $AND = "AND DAY(client_loan_agreement.datetime) ='$filt_day'";
		}
		
		$rResult = mysql_query("SELECT MAX(client_loan_schedule.id),
                            		   DATE_FORMAT(client_loan_agreement.datetime,'%d/%m/%Y') AS loan_date,
                                       IF(client_loan_agreement.no_standart = 1,CONCAT('<div title=\"არასტანდარტული ხელშეკრულება\" style=\"background: #009688;\">',IF(client.`name` = '',client.ltd_name,CONCAT(client.`name`,' ',client.lastname)), '</div>'),CONCAT(IF(client.`name` = '',client.ltd_name,CONCAT(client.`name`,' ',client.lastname)))) AS cl_name,
                            		   client_loan_agreement.oris_code,
                                       IF(client.attachment_id = 0, 
                            				IF(ISNULL(client.sub_client),
                            					CONCAT('N',IF(client.id<302, client.exel_agreement_id, client_loan_agreement.id)),
                            					CONCAT('N',IF(client.id<302, client.exel_agreement_id, client_loan_agreement.id),'/N',
                            				   (SELECT IF(clt.id<302, clt.exel_agreement_id, client_loan_agreement.id) 
                            					FROM   client_loan_agreement 
                            					join   client AS clt ON clt.id = client_loan_agreement.client_id
                            					WHERE  client_loan_agreement.client_id = client.sub_client))),
                            					CONCAT('N',(SELECT IF(cl.id<302, cl.exel_agreement_id, client_loan_agreement.id) 
                            								FROM   client_loan_agreement 
                            								join client AS cl ON cl.id = client_loan_agreement.client_id
                            								WHERE  client_loan_agreement.client_id = client.attachment_id),' დ.',client_loan_agreement.attachment_number
                            		   )) AS agreement_number,
                                	   CASE
                                		   WHEN client_loan_schedule.`status` = 1 AND client_loan_agreement.loan_currency_id = 1 THEN client_loan_schedule.remaining_root
                                           WHEN client_loan_schedule.`status` = 1 AND client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root*client_loan_agreement.exchange_rate,2)
                                           WHEN (SELECT cl_Sshe.status FROM client_loan_schedule AS cl_Sshe WHERE cl_Sshe.actived = 1 AND cl_Sshe.id = MAX(client_loan_schedule.id) LIMIT 1) = 0 AND client_loan_agreement.loan_currency_id = 1 THEN ROUND(MIN(client_loan_schedule.remaining_root) + MAX(client_loan_schedule.root) + (SELECT IFNULL(SUM(client_loan_schedule.percent),0) FROM client_loan_schedule WHERE client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 0 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND MONTH(client_loan_schedule.pay_date) <'$filt_month' AND YEAR(client_loan_schedule.pay_date)<='$filt_year'),2)
                                           WHEN (SELECT cl_Sshe.status FROM client_loan_schedule AS cl_Sshe WHERE cl_Sshe.actived = 1 AND cl_Sshe.id = MAX(client_loan_schedule.id) LIMIT 1) = 0 AND client_loan_agreement.loan_currency_id = 2 THEN ROUND((MIN(client_loan_schedule.remaining_root) + MAX(client_loan_schedule.root) + (SELECT IFNULL(SUM(client_loan_schedule.percent),0) FROM client_loan_schedule WHERE client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 0 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND MONTH(client_loan_schedule.pay_date) <'$filt_month' AND YEAR(client_loan_schedule.pay_date)<='$filt_year'))*client_loan_agreement.exchange_rate,2)
                                	   END AS darchenili_vali,
                                       DATE_FORMAT(MAX(client_loan_schedule.schedule_date),'%d/%m/%Y') AS daricxvis_tarigi,
		                               IFNULL(CASE
                                                 WHEN MONTH(MAX(client_loan_schedule.schedule_date)) = '$filt_month' AND DAY(MAX(client_loan_schedule.schedule_date)) <= $day AND client_loan_agreement.loan_currency_id = 1 THEN ROUND(MIN(client_loan_schedule.percent)+IF(MAX(client_loan_schedule.penalty)>0 AND (SELECT sshh.`status` FROM client_loan_schedule AS sshh WHERE sshh.id = MAX(client_loan_schedule.id) AND sshh.actived = 1) = 1,MAX(client_loan_schedule.penalty),0),2)
                                                 WHEN MONTH(MAX(client_loan_schedule.schedule_date)) = '$filt_month' AND DAY(MAX(client_loan_schedule.schedule_date)) <= $day AND client_loan_agreement.loan_currency_id = 2 THEN ROUND((MIN(client_loan_schedule.percent)+IF(MAX(client_loan_schedule.penalty)>0 AND (SELECT sshh.`status` FROM client_loan_schedule AS sshh WHERE sshh.id = MAX(client_loan_schedule.id) AND sshh.actived = 1) = 1,MAX(client_loan_schedule.penalty),0))*(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = MAX(client_loan_schedule.schedule_date) LIMIT 1),2)
                                              END,'0.00') AS percent,
                                      (SELECT DATE_FORMAT(money_transactions_detail.pay_datetime,'%d/%m/%Y')
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = MAX(client_loan_schedule.id) 
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` = 1
                                       AND    money_transactions_detail.type_id = 1
                                	   LIMIT 1) AS gadaxdis_tarigi,
                                      (SELECT ROUND(SUM(IF(client_loan_agreement.loan_currency_id = 1,money_transactions_detail.pay_percent+money_transactions_detail.pay_amount,(money_transactions_detail.pay_percent*money_transactions_detail.course)+(money_transactions_detail.pay_amount*money_transactions_detail.course))),2)
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = MAX(client_loan_schedule.id) 
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` IN(1,2)
                                       AND    money_transactions_detail.type_id = 1) AS gadaxdili_procenti,
                                      (SELECT DATE_FORMAT(money_transactions_detail.pay_datetime,'%d/%m/%Y')
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = MAX(client_loan_schedule.id)
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` = 1
                                       AND    money_transactions_detail.type_id = 1
                                	   LIMIT 1) AS gadaxdis_tarigi,
                                      (SELECT ROUND(SUM(IF(client_loan_agreement.loan_currency_id = 1,money_transactions_detail.pay_root,money_transactions_detail.pay_root*money_transactions_detail.course)),2)
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = MAX(client_loan_schedule.id) 
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` = 1
                                       AND    money_transactions_detail.type_id = 1) AS gadaxdili_dziri,
                                       (SELECT cl_Sshe.status FROM client_loan_schedule AS cl_Sshe WHERE cl_Sshe.actived = 1 AND cl_Sshe.id = MAX(client_loan_schedule.id) LIMIT 1) AS status
                                
                                FROM   client_loan_schedule
                                JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN   client ON client.id = client_loan_agreement.client_id
                                WHERE  client_loan_schedule.actived = 1
                                AND    (MONTH(client_loan_schedule.pay_date) = '$filt_month' OR date_format(client_loan_agreement.datetime, '%Y-%m') = '$filt_date' OR (client_loan_schedule.status = 0 AND MONTH(client_loan_schedule.pay_date) <='$filt_month') OR (MONTH(client_loan_schedule.pay_date) = '$filt_month1' AND DAY(client_loan_schedule.pay_date) = '1' AND DAY(client_loan_agreement.datetime) !='1') OR (MONTH(client_loan_schedule.pay_date) = '$filt_month2' AND DAY(client_loan_schedule.pay_date) = '1' AND DAY(client_loan_agreement.datetime) !='1'))
                                AND    YEAR(client_loan_schedule.pay_date) <= '$filt_year'
                                AND    client_loan_agreement.canceled_status = 0
                                AND    client.actived = 1 $AND
		                        GROUP BY client_loan_schedule.client_loan_agreement_id");

		$data = array("aaData"	=> array());

		while ($aRow = mysql_fetch_array($rResult)){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ ){
			    if($i==12){
			        $darechenili_dziri   = $aRow[darchenili_vali];
			        $daricxuli_procenti  = $aRow[percent];
			        $gadaxdili_procenti  = 0;
			        if ($aRow[gadaxdili_procenti] != '') {
			            $gadaxdili_procenti = $aRow[gadaxdili_procenti];
			        }
			        
			        if($aRow[status] == 0){
			             $row[] = round(($darechenili_dziri + $daricxuli_procenti - $gadaxdili_procenti),2);
			        }else{
			             $row[] = round($darechenili_dziri,2);
			        }
			    }else{
				   $row[] = $aRow[$i];
			    }
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
