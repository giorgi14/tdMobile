<?php
require_once('classes/core.php');

$user   = $_SESSION['USERID'];
$status = 0;

$result = mysql_query("SELECT    client_loan_schedule.id AS schedule_id,
                				 client_loan_agreement.id AS agr_id,
                				 client_loan_schedule.schedule_date,
                				 client.id AS client_id,
                				 DATEDIFF(CURDATE(),client_loan_schedule.pay_date) AS datediff,
                				 client_loan_agreement.penalty_days,
                				 client_loan_agreement.penalty_percent,
                				 client_loan_agreement.penalty_additional_percent,
                				 client_loan_schedule.root + client_loan_schedule.remaining_root AS remaining_root,
                                 client_loan_schedule.pay_amount AS sched_pay_amount,
                                 IFNULL(client_loan_schedule.penalty,0) AS penalty,
                                 COUNT(*) AS penalty_mont_caunt
                        FROM     client_loan_schedule
                        JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                        JOIN     client ON client.id = client_loan_agreement.client_id
                        WHERE    client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 0
                        AND      client_loan_schedule.schedule_date < CURDATE() AND DATEDIFF(CURDATE(),client_loan_schedule.pay_date)>=1
                        AND      client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 0
                        GROUP BY client_loan_schedule.client_loan_agreement_id");

while ($row = mysql_fetch_array($result)) {
    $remaining_root = $row[remaining_root];
    
    if ($row[datediff]>0 && $row[datediff]<=$row[penalty_days]) {
        $penalty = round(($remaining_root * ($row[penalty_percent]/100))*$row[datediff],2);
    }elseif ($row[datediff]>0 && $row[datediff]>$row[penalty_days]){
        $penalty = round((($remaining_root * ($row[penalty_percent]/100))*$row[penalty_days])+($remaining_root * ($row[penalty_additional_percent]/100))*($row[datediff]-$row[penalty_days]),2);
    }
    
    
    if ($row[penalty_mont_caunt] = 1) {
        $check_pay = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(money_transactions.pay_amount),0) AS pay_amount
                                                    FROM   money_transactions 
                                                    WHERE  money_transactions.`status` = 0 
                                                    AND   (money_transactions.client_id = '$row[client_id]' OR money_transactions.agreement_id = '$row[agr_id]')"));
        
        $check_balance = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                                        FROM   money_transactions_detail
                                                        JOIN   money_transactions ON money_transactions.id = money_transactions_detail.transaction_id 
                                                        WHERE  money_transactions_detail.`status` = 0 
                                                        AND    money_transactions.client_loan_schedule_id = '$row[schedule_id]'"));
        
        $full_payed = $check_balance[pay_amount] + $check_pay[pay_amount];
        $full_fee   = $row[sched_pay_amount] + $row[penalty];
    }
    
    if ($penalty != '' AND $full_payed < $full_fee){
        mysql_query("UPDATE `client_loan_schedule`
                        SET `penalty` = '$penalty'
                     WHERE  `id`      = '$row[schedule_id]'");
        $status = 1;
    }
}

echo $status;
?>