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
        
        $req = mysql_fetch_array(mysql_query("SELECT   client_loan_schedule.remaining_root,
                                        			   CASE 
                                        				   WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.remaining_root / client_loan_agreement.exchange_rate,2)
                                        				   WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate,2)
                                        			   END AS remaining_root_gel,
                                                       client_loan_agreement.insurance_fee
                                              FROM     client_loan_schedule
                                              JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                              WHERE    client_loan_agreement.client_id = $id AND client_loan_schedule.`status` = 1
                                              ORDER BY client_loan_schedule.pay_date DESC
                                              LIMIT 1"));
        
        $data = array("remaining_root" => $req[remaining_root], "remaining_root_gel" => $req[remaining_root_gel], "insurance_fee" => $req[insurance_fee]);
        
    
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
                    								  AND client_loan_schedule.pay_date <= CURDATE()) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT ROUND(SUM(client_loan_schedule.percent),2)
                    								  FROM   client_loan_agreement
                    								  JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                    								  WHERE  client_loan_agreement.client_id = client.id 
                    								  AND    client_loan_schedule.pay_date <= CURDATE()) 
                    					    END AS daricxuli_dolari,
                    					    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT ROUND(SUM(client_loan_schedule.percent),2)
                    								  FROM   client_loan_agreement
                    								  JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                    								  WHERE  client_loan_agreement.client_id = client.id AND client_loan_schedule.pay_date <= CURDATE()) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT ROUND(SUM(client_loan_schedule.percent*client_loan_agreement.exchange_rate),2)
                									  FROM   client_loan_agreement
                									  JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                									  WHERE  client_loan_agreement.client_id = client.id AND client_loan_schedule.pay_date <= CURDATE()) 
                    					    END AS daricxuli_lari,
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
	case 'get_list1' :
	    $count	          = $_REQUEST['count'];
	    $hidden	          = $_REQUEST['hidden'];
	    
	    $id	              = $_REQUEST['id'];
	    $loan_currency_id = $_REQUEST['loan_currency_id'];
	    if ($loan_currency_id == 1) {
	        $rResult = mysql_query(" SELECT letter.number,
                            	            letter.number,
                            	            letter.date,
                            	            letter.exchange,
	                                        letter.loan_amount_gel,
                            	            letter.loan_amount,
                            	            letter.percent_gel,
                            	            letter.percent,
                            	            letter.percent_gel1,
                            	            letter.percent1,
                            	            letter.pay_root_gel,
                            	            letter.pay_root
                            	      FROM(SELECT   client_loan_schedule.id AS `id`,
	                                                '1' AS sort,
                                    	            client_loan_schedule.number,
                                    	            DATE(client_loan_schedule.pay_date) AS `date`,
                                    	            client_loan_agreement.exchange_rate AS `exchange`,
                                    	            client_loan_agreement.loan_amount AS `loan_amount_gel`,
                                    	            ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2) AS `loan_amount`,
                                    	            ROUND(client_loan_schedule.percent,2) AS percent_gel,
                                    	            ROUND(client_loan_schedule.percent/client_loan_agreement.exchange_rate,2) AS percent,
	                                                '' AS percent_gel1,
                                    	            '' AS percent1,
                                    	            '' AS pay_root_gel,
                                    	            '' AS pay_root
                                    	    FROM   client_loan_schedule
                            	            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            	            WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.type_id = 1
                            	            UNION ALL
                            	            SELECT  client_loan_schedule.id AS `id`,
	                                                '2' AS sort,
                                    	            client_loan_schedule.number,
                                    	            DATE(money_transactions.pay_datetime) AS `date`,
                                    	            money_transactions.course AS `exchange`,
	                                                '' AS `loan_amount_gel`,
                                    	            '' AS `loan_amount`,
                                    	            '' AS percent_gel,
                                    	            '' AS percent,
                                    	            ROUND(money_transactions.pay_percent,2) AS percent_gel1,
                                    	            ROUND(money_transactions.pay_percent/money_transactions.course,2) AS percent1,
                                    	            ROUND(money_transactions.pay_root,2) AS pay_root_gel,
                                    	            ROUND(money_transactions.pay_root/money_transactions.course,2) AS pay_root
                            	            FROM   money_transactions
                            	            JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                            	            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            	            WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.type_id = 1
                            	            UNION ALL
                            	            SELECT  client_loan_schedule.id AS `id`,
                                    				'3' AS sort,
                                    				client_loan_schedule.number,
                                    				DATE(money_transactions.pay_datetime) AS `date`,
                                    				money_transactions.course AS `exchange`,
                                    			    '' AS `loan_amount_gel`,
                                    				'' AS `loan_amount`,
                                    				IFNULL(CONCAT('<div style=\"background: #f90404\">',(SELECT CASE
                            									 WHEN DATEDIFF(DATE(money_transactions.pay_datetime), clsh1.pay_date)>0 AND DATEDIFF(DATE(money_transactions.pay_datetime), clsh1.pay_date) <= client_loan_agreement.penalty_days THEN ROUND((clsh1.remaining_root*(client_loan_agreement.penalty_percent/100))*(DATEDIFF(money_transactions.pay_datetime, clsh1.pay_date)),2)
                            									 WHEN DATEDIFF(DATE(money_transactions.pay_datetime), clsh1.pay_date)>client_loan_agreement.penalty_days THEN ROUND((clsh1.remaining_root*(client_loan_agreement.penalty_additional_percent/100))*(DATEDIFF(money_transactions.pay_datetime, clsh1.pay_date)),2)
                                							 END AS penalty
                                    				FROM  `client_loan_schedule` AS clsh1
                                                    JOIN   client_loan_agreement ON client_loan_agreement.id = clsh1.client_loan_agreement_id
                                    				JOIN   money_transactions ON money_transactions.client_loan_schedule_id = clsh1.id 
                                    	            WHERE clsh1.id = client_loan_schedule.id
                                    	            ORDER BY money_transactions.pay_datetime DESC
                                    				LIMIT 1),'</div>'),'') AS percent_gel,
                                    				IFNULL(CONCAT('<div style=\"background: #f90404\">',ROUND((SELECT CASE
                													 WHEN DATEDIFF(DATE(money_transactions.pay_datetime), clsh.pay_date)>0 AND DATEDIFF(DATE(money_transactions.pay_datetime), clsh.pay_date) <= client_loan_agreement.penalty_days THEN ROUND((clsh.remaining_root*(client_loan_agreement.penalty_percent/100))*(DATEDIFF(money_transactions.pay_datetime, clsh.pay_date)),2)
                													 WHEN DATEDIFF(DATE(money_transactions.pay_datetime), clsh.pay_date)>client_loan_agreement.penalty_days THEN ROUND((clsh.remaining_root*(client_loan_agreement.penalty_additional_percent/100))*(DATEDIFF(money_transactions.pay_datetime, clsh.pay_date)),2)
                    											   END AS penalty
                            								FROM  `client_loan_schedule` AS clsh
                            								JOIN   client_loan_agreement ON client_loan_agreement.id = clsh.client_loan_agreement_id
                            								JOIN money_transactions ON money_transactions.client_loan_schedule_id = clsh.id
                            								WHERE clsh.id = client_loan_schedule.id
	                                                        ORDER BY money_transactions.pay_datetime DESC
                                    				        LIMIT 1)/money_transactions.course,2),'</div>'),'') AS percent,
                                    				'' AS percent_gel1,
                                    				'' AS percent1,
                                    				'' AS pay_root_gel,
                                    				'' AS pay_root
                                            FROM   money_transactions
                                            JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.type_id = 1
	                                        GROUP BY money_transactions.client_loan_schedule_id
	                                        HAVING percent_gel !=''
	                                        ) AS letter
                            	            ORDER BY letter.number, letter.sort ASC");
	    }else{	
    	    $rResult = mysql_query(" SELECT letter.number,
    	                                    letter.number,
    	                                    letter.date,
    	                                    letter.exchange,
                                	        letter.loan_amount,
                                	        letter.loan_amount_gel,
                                	        letter.percent,
                                	        letter.percent_gel,
                                	        letter.percent1,
                                	        letter.percent_gel1,
    	                                    letter.pay_root,
    	                                    letter.pay_root_gel
    	                             FROM(SELECT   client_loan_schedule.id AS `id`,
                                    			   client_loan_schedule.pay_date AS sort,
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
                                            FROM   client_loan_schedule
                                            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            JOIN   money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                                            WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.type_id = 1
    	                                    GROUP BY money_transactions.client_loan_schedule_id
                                            UNION ALL
                                            SELECT  client_loan_schedule.id AS `id`,
                                    				money_transactions.pay_datetime AS sort,
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
                                            WHERE   client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.type_id = 1
                                            UNION ALL
                                            SELECT   client_loan_schedule.id AS `id`,
                                    			     money_transactions.pay_datetime AS sort,
                                    			     client_loan_schedule.number,
                                    			     DATE(money_transactions.pay_datetime) AS `date`,
                                    			     money_transactions.course AS `exchange`,
                                                     '' AS `loan_amount`,
                                    			    '' AS `loan_amount_gel`,
    	                                             ROUND(money_transactions.pay_penalty,2) AS percent,
                                    				 CASE 
                                    					 WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_penalty/course,2) 
                                    					 WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_penalty*course,2) 
                                    			     END AS percent_gel,
                                    			     '' AS percent1,
                                                     '' AS percent_gel1,
                                    		         '' AS pay_root,
                                    		         '' AS pay_root_gel
                                            FROM   money_transactions
                                            JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                            WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1 AND money_transactions.type_id = 1
                                            AND money_transactions.pay_penalty>0) AS letter
                                            ORDER BY letter.number, letter.date ASC");
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
                        <th style="width: 5%;">კურსი</th>
                        <th style="width: 6%;">სესხის<br>გაცემა<br>ლარი</th>
                        <th style="width: 7%;">სესხის<br>გაცემა<br>დოლარი</th>
                        <th style="width: 7%;">დარიცხვა%<br>ლარი</th>
                        <th style="width: 7%;">დარიცხვა%<br>დოლარი</th>
                        <th style="width: 7%;">გადახდა%<br>ლარი</th>
                        <th style="width: 7%;">გადახდა%<br>დოლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>ლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>დოლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>ლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>დოლარი</th>
                        <th style="width: 7%;">კურსთა<br>შორისი<br>სხვაონა</th>
                        <th style="width: 7%;">დაზღვევა</th>
                        <th style="width: 6%;">გადაფო-<br>რმება</th>
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
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root_gel" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_fee" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                    </tr>
                </tfoot>';
    }else{
        $tables = '<thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 30px;">#</th>
                        <th style="width: 7%;">რიცხვი</th>
                        <th style="width: 5%;">კურსი</th>
                        <th style="width: 7%;">სესხის<br>გაცემა<br>დოლარი</th>
                        <th style="width: 6%;">სესხის<br>გაცემა<br>ლარი</th>
                        <th style="width: 7%;">დარიცხვა%<br>დოლარი</th>
                        <th style="width: 7%;">დარიცხვა%<br>ლარი</th>
                        <th style="width: 7%;">გადახდა%<br>დოლარი</th>
                        <th style="width: 7%;">გადახდა%<br>ლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>დოლარი</th>
                        <th style="width: 7%;">ძირის<br>გადახდა<br>ლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>დოლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>ლარი</th>
                        <th style="width: 7%;">კურსთა<br>შორისი<br>სხვაონა</th>
                        <th style="width: 7%;">დაზღვევა</th>
                        <th style="width: 6%;">გადაფო-<br>რმება</th>
        
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
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root_gel" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_fee" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                    </tr>
                </tfoot>';
    }
 $data = '
	<div id="dialog-form">
	    <fieldset>
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
