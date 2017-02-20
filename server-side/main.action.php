<?php
require_once('../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
    case 'get_edit_page':
        $id	  = $_REQUEST['id'];
        $page = GetPage($id);
        $data = array('page' => $page);
    
        break;
    case 'gel_footer':
        $id	              = $_REQUEST['id'];
        
        $req = mysql_fetch_array(mysql_query("SELECT   client_loan_agreement.loan_currency_id,
                                                       client_loan_schedule.remaining_root,
                                        			   CASE 
                                        				   WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.remaining_root / client_loan_agreement.exchange_rate,2)
                                        				   WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate,2)
                                        			   END AS remaining_root_gel,
                                                       client_loan_agreement.insurance_fee
                                              FROM     client_loan_schedule
                                              JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                              WHERE    client_loan_agreement.client_id = $id AND client_loan_schedule.`status` = 0
                                              ORDER BY client_loan_schedule.pay_date asc
                                              LIMIT 1"));
        
        $data = array("remaining_root" => $req[remaining_root], "remaining_root_gel" => $req[remaining_root_gel], "insurance_fee" => $req[insurance_fee], "loan_currency_id" => $req[loan_currency_id]);
        
    
        break;
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
		                                    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT ROUND(SUM(client_loan_schedule.percent/client_loan_agreement.exchange_rate),2)
                    								  FROM   client_loan_agreement
                    								  JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                    								  WHERE  client_loan_agreement.client_id = client.id 
                    								  AND client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT ROUND(SUM(client_loan_schedule.percent),2)
                    								  FROM   client_loan_agreement
                    								  JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                    								  WHERE  client_loan_agreement.client_id = client.id 
                    								  AND client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 1) 
                    					    END AS daricxuli_dolari,
                    					    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT ROUND(SUM(client_loan_schedule.percent),2)
                    								  FROM   client_loan_agreement
                    								  JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                    								  WHERE  client_loan_agreement.client_id = client.id AND client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT ROUND(SUM(client_loan_schedule.percent*client_loan_agreement.exchange_rate),2)
                									  FROM   client_loan_agreement
                									  JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                									  WHERE  client_loan_agreement.client_id = client.id AND client_loan_schedule.actived = 1 AND client_loan_schedule.`status` = 1) 
                    					    END AS daricxuli_lari,
                        					CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root/client_loan_agreement.exchange_rate,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    					    END AS darchenili_vali_dolari,
		                                    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root*client_loan_agreement.exchange_rate,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    					    END AS darchenili_vali_lari,
                        					CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root/client_loan_agreement.exchange_rate,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    					    END AS darchenili_dziri_dolari,
		                                    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT   ROUND(client_loan_schedule.remaining_root*client_loan_agreement.exchange_rate,2)
                                                      FROM     client_loan_agreement
                                                      JOIN     client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE    client_loan_agreement.client_id = client.id AND client_loan_schedule.`status` = 1 AND client_loan_schedule.actived = 1
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    					    END AS darchenili_dziri_lari,
                        					'',
		                                    ''
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
	case 'get_list1' :
	    $count	          = $_REQUEST['count'];
	    $hidden	          = $_REQUEST['hidden'];
	    
	    $id	              = $_REQUEST['id'];
	    $loan_currency_id = $_REQUEST['loan_currency_id'];
	    if ($loan_currency_id == 1) {
	        $rResult = mysql_query(" SELECT   letter.number,
                                			 letter.number,
                                			 IF(letter.sort1=3,CONCAT('<div title=\"წინა თვის მეტობა\" style=\"background: #009688;\">', letter.date, '<div>'), IF(letter.sort1=4,CONCAT('<div title=\"', letter.loan_amount_gel,' დღის ჯარიმა\" style=\"background: #F44336;\">', letter.date, '<div>'), letter.date)),
    	                                     letter.exchange,
                                			 letter.loan_amount,
                                			 letter.loan_amount_gel,
    	                                     letter.percent,
                                			 letter.percent_gel,
                                			 letter.percent1,
                                			 letter.percent_gel1,
                                			 letter.pay_root,
                                			 letter.pay_root_gel
                                     FROM(SELECT client_loan_schedule.id AS `id`,
                                				 client_loan_schedule.pay_date AS sort,
                                                 '1' AS sort1,
                                				 client_loan_schedule.number,
                                				 DATE(client_loan_schedule.pay_date) AS `date`,
                                				 client_loan_agreement.exchange_rate AS `exchange`,
                                				 client_loan_agreement.loan_amount AS `loan_amount`,
                                				 CASE 
                                				    WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2)
                                				    WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2)
                                				 END AS `loan_amount_gel`,
                                				 ROUND(client_loan_schedule.percent,2) AS percent,
                                				 CASE 
                                				    WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.percent/client_loan_agreement.exchange_rate,2)
                                				    WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.percent*client_loan_agreement.exchange_rate,2)
                                				 END AS percent_gel,
                                				 '' AS percent1,
                                				 '' AS percent_gel1,
                                				 '' AS pay_root,
                                				 '' AS pay_root_gel
                                        FROM     client_loan_schedule
                                        JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        JOIN     money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                                        WHERE    client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                        GROUP BY money_transactions.client_loan_schedule_id
                                        UNION ALL 
                                        SELECT  client_loan_schedule.id AS `id`,
                                        				client_loan_schedule.pay_date AS sort,
                                                '2' AS sort1,
                                        				client_loan_schedule.number,
                                        				DATE(money_transactions.pay_datetime) AS `date`,
                                        				money_transactions.course AS `exchange`,
                                        				'' AS `loan_amount`,
                                        				'' AS `loan_amount_gel`,
                                        				'' AS percent,
                                        				'' AS percent_gel,
                                        				ROUND(money_transactions.pay_percent,2) AS percent1,
                                        				CASE 
                                        					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_percent/money_transactions.course,2)
                                        					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_percent*money_transactions.course,2)
                                        				END AS percent_gel1,
                                        				ROUND(money_transactions.pay_root,2) AS pay_root,
                                        				CASE 
                                        					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                                        					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                                        				END AS pay_root_gel
                                        FROM    money_transactions
                                        JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                        JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        WHERE   client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                        UNION ALL
                                        SELECT  client_loan_schedule.id AS `id`,
                                        		client_loan_schedule.pay_date AS sort,
                                                '3' AS sort1,
                            					client_loan_schedule.number,
                            					DATE(money_transactions.pay_datetime) AS `date`,
                            					money_transactions.course AS `exchange`,
                            					'' AS `loan_amount`,
                            					'' AS `loan_amount_gel`,
                            					ROUND(money_transactions.pay_amount,2) AS percent,
                            					CASE 
                            						WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                            						WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                            					END AS percent_gel,
                            					ROUND(money_transactions.pay_percent,2) AS percent1,
                                				CASE 
                                					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_percent/money_transactions.course,2)
                                					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_percent*money_transactions.course,2)
                                				END AS percent_gel1,
                                				ROUND(money_transactions.pay_root,2) AS pay_root,
                                				CASE 
                                					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                                					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                                				END AS pay_root_gel
                                        FROM   money_transactions
                                        JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                        JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 5
                                        UNION ALL
                                        SELECT  client_loan_schedule.id AS `id`,
                                        		client_loan_schedule.pay_date AS sort,
                                                '4' AS sort1,
                            					client_loan_schedule.number,
                            					DATE(money_transactions.pay_datetime) AS `date`,
                            					money_transactions.course AS `exchange`,
                            					'' AS `loan_amount`,
                            					DATEDIFF(money_transactions.datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
                            					ROUND(money_transactions.pay_penalty,2) AS percent,
                            					CASE 
                            						WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                            						WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                            					END AS percent_gel,
                            					'' AS percent1,
                            					'' AS percent_gel1,
                            					'' AS pay_root,
                            					'' AS pay_root_gel
                                        FROM   money_transactions
                                        JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                        JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 4)AS letter
                                 ORDER BY letter.number, letter.sort,  letter.sort1 ASC");
	    }else{	
    	    $rResult = mysql_query("SELECT   letter.number,
                                			 letter.number,
                                			 IF(letter.sort1=3,CONCAT('<div title=\"წინა თვის მეტობა\" style=\"background: #009688;\">', letter.date, '<div>'), IF(letter.sort1=4,CONCAT('<div title=\"', letter.loan_amount_gel,' დღის ჯარიმა\" style=\"background: #F44336;\">', letter.date, '<div>'), letter.date)),
    	                                     letter.exchange,
                                			 letter.loan_amount,
                                			 letter.loan_amount_gel,
    	                                     letter.percent,
                                			 letter.percent_gel,
                                			 letter.percent1,
                                			 letter.percent_gel1,
                                			 letter.pay_root,
                                			 letter.pay_root_gel
                                     FROM(SELECT client_loan_schedule.id AS `id`,
                                				 client_loan_schedule.pay_date AS sort,
                                                 '1' AS sort1,
                                				 client_loan_schedule.number,
                                				 DATE(client_loan_schedule.pay_date) AS `date`,
                                				 client_loan_agreement.exchange_rate AS `exchange`,
                                				 client_loan_agreement.loan_amount AS `loan_amount`,
                                				 CASE 
                                				    WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2)
                                				    WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2)
                                				 END AS `loan_amount_gel`,
                                				 ROUND(client_loan_schedule.percent,2) AS percent,
                                				 CASE 
                                				    WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.percent/client_loan_agreement.exchange_rate,2)
                                				    WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.percent*client_loan_agreement.exchange_rate,2)
                                				 END AS percent_gel,
                                				 '' AS percent1,
                                				 '' AS percent_gel1,
                                				 '' AS pay_root,
                                				 '' AS pay_root_gel
                                        FROM     client_loan_schedule
                                        JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        JOIN     money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                                        WHERE    client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                        GROUP BY money_transactions.client_loan_schedule_id
                                        UNION ALL 
                                        SELECT  client_loan_schedule.id AS `id`,
                                        				client_loan_schedule.pay_date AS sort,
                                                '2' AS sort1,
                                        				client_loan_schedule.number,
                                        				DATE(money_transactions.pay_datetime) AS `date`,
                                        				money_transactions.course AS `exchange`,
                                        				'' AS `loan_amount`,
                                        				'' AS `loan_amount_gel`,
                                        				'' AS percent,
                                        				'' AS percent_gel,
                                        				ROUND(money_transactions.pay_percent,2) AS percent1,
                                        				CASE 
                                        					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_percent/money_transactions.course,2)
                                        					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_percent*money_transactions.course,2)
                                        				END AS percent_gel1,
                                        				ROUND(money_transactions.pay_root,2) AS pay_root,
                                        				CASE 
                                        					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                                        					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                                        				END AS pay_root_gel
                                        FROM    money_transactions
                                        JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                        JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        WHERE   client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                        UNION ALL
                                        SELECT  client_loan_schedule.id AS `id`,
                                        		client_loan_schedule.pay_date AS sort,
                                                '3' AS sort1,
                            					client_loan_schedule.number,
                            					DATE(money_transactions.pay_datetime) AS `date`,
                            					money_transactions.course AS `exchange`,
                            					'' AS `loan_amount`,
                            					'' AS `loan_amount_gel`,
                            					ROUND(money_transactions.pay_amount,2) AS percent,
                            					CASE 
                            						WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                            						WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                            					END AS percent_gel,
                            					ROUND(money_transactions.pay_percent,2) AS percent1,
                                				CASE 
                                					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_percent/money_transactions.course,2)
                                					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_percent*money_transactions.course,2)
                                				END AS percent_gel1,
                                				ROUND(money_transactions.pay_root,2) AS pay_root,
                                				CASE 
                                					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                                					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                                				END AS pay_root_gel
                                        FROM   money_transactions
                                        JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                        JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 5
                                        UNION ALL
                                        SELECT  client_loan_schedule.id AS `id`,
                                        		client_loan_schedule.pay_date AS sort,
                                                '4' AS sort1,
                            					client_loan_schedule.number,
                            					DATE(money_transactions.pay_datetime) AS `date`,
                            					money_transactions.course AS `exchange`,
                            					'' AS `loan_amount`,
                            					DATEDIFF(money_transactions.datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
                            					ROUND(money_transactions.pay_penalty,2) AS percent,
                            					CASE 
                            						WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                            						WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                            					END AS percent_gel,
                            					'' AS percent1,
                            					'' AS percent_gel1,
                            					'' AS pay_root,
                            					'' AS pay_root_gel
                                        FROM   money_transactions
                                        JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                        JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                        WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.status = 4)AS letter
                                 ORDER BY letter.number, letter.sort,  letter.sort1 ASC");
	    }
	    
	    
	    $data = array("aaData"	=> array());
	    $j=1;
	    while ( $aRow = mysql_fetch_array( $rResult )){
	        
	        $row = array();
	        for ( $i = 0 ; $i < $count ; $i++ ){
	            if ($j>1 && $i>3 && $i<6) {
	                $row[] = '';
	            }else{
	               $row[] = $aRow[$i];
	            }
	        }
	        $data['aaData'][] = $row;
	        $j++;
	    }
	    break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function GetPage($id){
    
    $res=mysql_fetch_assoc(mysql_query("SELECT client_loan_agreement.loan_currency_id
                                        FROM   client_loan_agreement
                                        WHERE  client_id = $id"));
    if ($res[loan_currency_id] == 1) {
        $tables = '<thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 30px;">#</th>
                        <th style="width: 7%;">რიცხვი</th>
                        <th style="width: 6%;">კურსი</th>
                        <th style="width: 7%;">სესხის<br>გაცემა<br>ლარი</th>
                        <th style="width: 7%;">სესხის<br>გაცემა<br>დოლარი</th>
                        <th style="width: 8%;">დარიცხვა%<br>ლარი</th>
                        <th style="width: 8%;">დარიცხვა%<br>დოლარი</th>
                        <th style="width: 7%;">გადახდა%<br>ლარი</th>
                        <th style="width: 7%;">გადახდა%<br>დოლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>ლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>დოლარი</th>
                        <th style="width: 7%;">ვალდე-<br>ბულება<br>ლარი</th>
                        <th style="width: 7%;">ვალდე-<br>ბულება<br>დოლარი</th>
                        <th style="width: 7%;">კურსთა<br>შორისი<br>სხვაობა</th>
                        <th style="width: 7%;">დაზღვევა</th>
                    </tr>
                </thead>
                <thead>
                    <tr class="search_header">
                        <th class="colum_hidden">
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                       	<th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;"><p align="right">სულ</th>
                        <th id ="gacema_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="daricxva_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="procenti_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="dziri_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root_gel" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="delta_cource" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_fee" style="text-align: left; font-weight: bold;">&nbsp;</th>
                    </tr>
                </tfoot>';
    }else{
        $tables = '<thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 30px;">#</th>
                        <th style="width: 7%;">რიცხვი</th>
                        <th style="width: 6%;">კურსი</th>
                        <th style="width: 7%;">სესხის<br>გაცემა<br>დოლარი</th>
                        <th style="width: 7%;">სესხის<br>გაცემა<br>ლარი</th>
                        <th style="width: 8%;">დარიცხვა%<br>დოლარი</th>
                        <th style="width: 8%;">დარიცხვა%<br>ლარი</th>
                        <th style="width: 7%;">გადახდა%<br>დოლარი</th>
                        <th style="width: 7%;">გადახდა%<br>ლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>დოლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>ლარი</th>
                        <th style="width: 7%;">ვალდე-<br>ბულება<br>დოლარი</th>
                        <th style="width: 7%;">ვალდე-<br>ბულება<br>ლარი</th>
                        <th style="width: 7%;">კურსთა<br>შორისი<br>სხვაობა</th>
                        <th style="width: 7%;">დაზღვევა</th>
        
                    </tr>
                </thead>
                <thead>
                    <tr class="search_header">
                        <th class="colum_hidden">
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                       	<th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;"><p align="right">სულ</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="gacema_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="daricxva_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="procenti_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="dziri_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root_gel" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="delta_cource" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_fee" style="text-align: left; font-weight: bold;">&nbsp;</th>
                    </tr>
                </tfoot>';
    }
  
    $req = mysql_query(" SELECT client_loan_schedule.number,
                                client_loan_schedule.schedule_date,
                                client_loan_schedule.root,
                                client_loan_schedule.percent,
                                client_loan_schedule.pay_amount,
                                client_loan_schedule.remaining_root,
                                client_loan_schedule.status
                         FROM   client_loan_schedule
                         JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                         WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1");
    
    while ($row = mysql_fetch_assoc($req)){
        $sum_percent += $row[percent];
        $sum_P       += $row[pay_amount];
        
        $color        = "";
        
        if ($row[status] == 1) {
            $color = 'background: #4CAF50;';
        }
        
        $dat.='<tr style="width:100%; border: 1px solid #000; '.$color.'">
                    <td style="width:5%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[number].'<label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[schedule_date].'</label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[root].'</label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[percent].'<label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[pay_amount].'</label></td>
                    <td style="width:19%;"><label style="font-size: 12px; text-align:center;">'.$row[remaining_root].'</label></td>
                </tr>';
    }
    
  $res = mysql_fetch_assoc(mysql_query("SELECT  client_loan_agreement.loan_months,
                                				client_loan_agreement.loan_amount,
                                				client_loan_agreement.percent,
                                				DATE_FORMAT(client_loan_agreement.datetime,'%m') AS `month_id`,
                                				DATE_FORMAT(client_loan_agreement.datetime,'%Y') AS `year`,
                                				DATE_FORMAT(client_loan_agreement.datetime,'%d') AS `day`,
                                				CONCAT(client.`name`,' ',client.lastname) AS `name`,
                                                client_loan_agreement.loan_type_id,
                                               client_loan_agreement.loan_currency_id
                                        FROM `client_loan_agreement`
                                        JOIN  client ON client.id = client_loan_agreement.client_id
                                        WHERE client.actived = 1 AND client.id = '$id'"));
  
  $hint = 'წლ';
  $percent = $res[percent] * 12;
  
  if ($res[loan_type_id] == 1) {
     $hint = 'თვ';
     $percent = $res[percent] * $res[loan_months];
  }
  
  
  
 $data = '<div id="dialog-form" style="overflow-y: scroll; height: 550px;">
                <fieldset>
                    <legend>გრაფიკი</legend>
                    <div style="width:100%;">
                        <div style="width:99%; font-size: 16px; text-align:center;">სესხის დაფარვის გრაფიკი</div>
                        <div style="width:99%; font-size: 14px;">
                            <table style="width:100%; margin-top: 5px;">
                                <tr style="width:100%; border: 1px solid #000;">
                                    <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 14px;">კლიენტის სახელი:<label></td>
                                    <td style="width:80%;"><label style="font-size: 14px;">'.$res[name].'</label></td>
                                </tr>
                            </table> 
                        </div>
                        <div style="width:99%; margin-top: 5px;">
                            <table style="width:100%;">
                                <tr style="width:100%;border: 1px solid #000;">
                                    <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">სესხის მცულობა:<label></td>
                                    <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res[loan_amount].'</label></td>
                                    <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;"></label></td>
                                    <td colspan="2" style="width:40%;"><label style="font-size: 12px; text-align:center;">სესხის გაცემის თარიღი</label></td>
                                </tr>
                                <tr style="width:100%;border: 1px solid #000;">
                                    <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">საპროცემტო სარგ. ('.$hint.'):<label></td>
                                    <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.round($percent,2).'</label></td>
                                    <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                    <td style="width:10%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თვე</label></td>
                                    <td style="width:10%;"><label style="font-size: 12px; text-align:center;">'.$res[month_id].'</label></td>
                                </tr>
                                <tr style="width:100%;border: 1px solid #000;">
                                    <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">ვადა:<label></td>
                                    <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res[loan_months].'</label></td>
                                    <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                    <td style="width:10%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">რიცხვი</label></td>
                                    <td style="width:10%;"><label style="font-size: 12px; text-align:center;">'.$res[day].'</label></td>
                                </tr>
                                <tr style="width:100%;border: 1px solid #000;">
                                    <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">საშეღავათო პერიოდი:<label></td>
                                    <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                    <td style="width:45%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                    <td style="width:10%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">წელი</label></td>
                                    <td style="width:10%;"><label style="font-size: 12px; text-align:center;">'.$res[year].'</label></td>
                                </tr>
                                <tr style="width:100%;border: 1px solid #000;">
                                    <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">საკომისიო წინასწარ(%):<label></td>
                                    <td colspan="4" style="width:20%;"><label style="font-size: 12px;"><label></td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:99%; margin-top: 25px; border: 1px solid #000;">
                            <table style="width:100%;">
                                <tr style="width:100%;border: 1px solid #000;">
                                    <td colspan="2" style="width:5%;border-right: 1px solid #000;"><label style="font-size: 12px;">სულ პროცენტი<label></td>
                                    <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px;">სულ დასაფარი</label></td>
                                    <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($sum_percent,2).'</label></td>
                                    <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($sum_P, 2).'</label></td>
                                    <td style="width:19%;"><label style="font-size: 12px; text-align:center;">0</label></td>
                                </tr>
                                <tr style="width:100%; border: 1px solid #000; background: #e0e0e0;">
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
               </fieldset>
        	   <fieldset>
                    <legend>ბარათი</legend>
                   <table class="display" id="table_letter">
                       '.$tables.'
                   </table>
               </fieldset>
        <input type="hidden" id="id" value="' . $id . '" />
        <input type="hidden" id="loan_currency_id" value="' . $res[loan_currency_id] . '" />
    </div>
    ';
    return $data;
}
?>
