<?php
require_once('classes/core.php');

$user   = $_SESSION['USERID'];
$status = 0;

$result = mysql_query("SELECT    MIN(client_loan_schedule.id) AS schedule_id,
                                 client_loan_agreement.id AS agr_id,
                        		 client_loan_schedule.schedule_date,
                                 client.id,
                                 DATEDIFF(CURDATE(),client_loan_schedule.pay_date) AS datediff,
                                 client_loan_agreement.penalty_days,
                                 client_loan_agreement.penalty_percent,
                                 client_loan_agreement.penalty_additional_percent,
                                (SELECT    client_loan_schedule.remaining_root
                				 FROM 	   `client_loan_schedule`
                				 LEFT JOIN client_loan_agreement AS agr ON agr.id = client_loan_schedule.client_loan_agreement_id
                				 JOIN  	client ON client.id = agr.client_id
                				 WHERE     client_loan_schedule.actived = 1 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND client_loan_schedule.`status` = 1
                				 ORDER BY  pay_date DESC
                				 LIMIT 1) AS remaining_root,
                                 CONCAT(client.`name`,' ',client.lastname) AS `name`
                        FROM     client_loan_schedule
                        JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                        JOIN     client ON client.id = client_loan_agreement.client_id
                        WHERE    client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 0
                        AND      client_loan_schedule.schedule_date < CURDATE() AND DATEDIFF(CURDATE(),client_loan_schedule.pay_date)>=1
                        AND      client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 0
                        GROUP BY client.id");

while ($row = mysql_fetch_array($result)) {
     $remaining_root = $row[remaining_root];
    if ($remaining_root == '') {
        $res = mysql_fetch_array(mysql_query("SELECT     MAX(client_loan_schedule.remaining_root) AS remainig_root
                                              FROM 	    `client_loan_schedule`
                                              LEFT JOIN  client_loan_agreement AS agr ON agr.id = client_loan_schedule.client_loan_agreement_id
                                              JOIN       client ON client.id = agr.client_id
                                              WHERE      client_loan_schedule.actived = 1 AND client_loan_schedule.client_loan_agreement_id = '$row[agr_id]' AND client_loan_schedule.`status` = 0
                                              ORDER BY   pay_date DESC
                                              LIMIT 1"));
        
        $remaining_root = $res[remainig_root];
    }
    
    if ($row[datediff]>0 && $row[datediff]<=$row[penalty_days]) {
        $penalty = round(($remaining_root * ($row[penalty_percent]/100))*$row[datediff],2);
    }elseif ($row[datediff]>0 && $row[datediff]>$row[penalty_days]){
        $penalty = round((($remaining_root * ($row[penalty_percent]/100))*$row[penalty_days])+($remaining_root * ($row[penalty_additional_percent]/100))*($row[datediff]-$row[penalty_days]),2);
    }
    
    
    
    if ($penalty != '') {
        mysql_query("UPDATE `client_loan_schedule`
                        SET `penalty` = '$penalty'
                     WHERE  `id`      = '$row[schedule_id]'");
        $status = 1;
    }
}

echo $status;
?>