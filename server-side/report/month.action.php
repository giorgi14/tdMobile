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
		$filt_day	= $_REQUEST['filt_day'];
		$AND        = '';
		if ($filt_day > 0) {
		    $AND = "AND DAY(client_loan_agreement.datetime) ='$filt_day'";
		}
		
		$rResult = mysql_query("SELECT client_loan_schedule.id,
                            		   DATE_FORMAT(client_loan_agreement.datetime,'%d/%m/%Y') AS loan_date,
                                       IF(client_loan_agreement.no_standart = 1,CONCAT('<div title=\"არასტანდარტული ხელშეკრულება\" style=\"background: #009688;\">',IF(client.`name` = '',client.ltd_name,CONCAT(client.`name`,' ',client.lastname)), '</div>'),CONCAT(IF(client.`name` = '',client.ltd_name,CONCAT(client.`name`,' ',client.lastname)))) AS cl_name,
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
                            								WHERE  client_loan_agreement.client_id = client.attachment_id),' დ.',client_loan_agreement.attachment_number
                            		   )) AS agreement_number,
                                	   CASE
                                		   WHEN client_loan_schedule.`status` = 1 AND client_loan_agreement.loan_currency_id = 1 THEN client_loan_schedule.remaining_root
                                           WHEN client_loan_schedule.`status` = 1 AND client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root*client_loan_agreement.exchange_rate,2)
                                           WHEN client_loan_schedule.`status` = 0 AND client_loan_agreement.loan_currency_id = 1 THEN client_loan_schedule.remaining_root+client_loan_schedule.root
                                           WHEN client_loan_schedule.`status` = 0 AND client_loan_agreement.loan_currency_id = 2 THEN ROUND((client_loan_schedule.remaining_root+client_loan_schedule.root)*client_loan_agreement.exchange_rate,2)
                                	   END AS darchenili_vali,
                                       DATE_FORMAT(client_loan_schedule.schedule_date,'%d/%m/%Y') AS daricxvis_tarigi,
                                	   IF(client_loan_schedule.schedule_date <= CURDATE(), IF(client_loan_agreement.loan_currency_id = 1, ROUND(client_loan_schedule.percent+IF(ISNULL(client_loan_schedule.penalty),0,client_loan_schedule.penalty),2),ROUND((client_loan_schedule.percent*client_loan_agreement.exchange_rate)+IF(ISNULL(client_loan_schedule.penalty),0,client_loan_schedule.penalty*client_loan_agreement.exchange_rate),2)),'0.00') AS percent,
                                      (SELECT DATE_FORMAT(money_transactions_detail.pay_datetime,'%d/%m/%Y')
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id 
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` = 1
                                       AND    money_transactions_detail.type_id = 1
                                			 LIMIT 1) AS gadaxdis_tarigi,
                                      (SELECT ROUND(SUM(IF(client_loan_agreement.loan_currency_id = 1,money_transactions_detail.pay_percent+money_transactions_detail.pay_amount,(money_transactions_detail.pay_percent*money_transactions_detail.course)+(money_transactions_detail.pay_amount*money_transactions_detail.course))),2)
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id 
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` IN(1,2)
                                       AND    money_transactions_detail.type_id = 1) AS gadaxdili_procenti,
                                      (SELECT DATE_FORMAT(money_transactions_detail.pay_datetime,'%d/%m/%Y')
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id 
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` = 1
                                       AND    money_transactions_detail.type_id = 1
                                			 LIMIT 1) AS gadaxdis_tarigi,
                                      (SELECT ROUND(SUM(IF(client_loan_agreement.loan_currency_id = 1,money_transactions_detail.pay_root,money_transactions_detail.pay_root*money_transactions_detail.course)),2)
                                       FROM   money_transactions
                                       JOIN   money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                       WHERE  money_transactions.client_loan_schedule_id = client_loan_schedule.id 
                                       AND    money_transactions_detail.actived = 1
                                       AND    money_transactions_detail.`status` = 1
                                       AND    money_transactions_detail.type_id = 1) AS gadaxdili_dziri,
                                       client_loan_schedule.status AS status
                                
                                FROM   client_loan_schedule
                                JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN   client ON client.id = client_loan_agreement.client_id
                                WHERE  client_loan_schedule.actived = 1
                                AND    MONTH(client_loan_schedule.pay_date) = '$filt_month'
                                AND    YEAR(client_loan_schedule.pay_date) = '$filt_year'
                                AND    client_loan_agreement.canceled_status = 0
                                AND    client.actived = 1 $AND");

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
			             $row[] = round($darechenili_dziri + $daricxuli_procenti - $gadaxdili_procenti,2);
			        }else{
			             $row[] = $darechenili_dziri;
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
