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
                                                       ROUND(client_loan_schedule.remaining_root,2) AS remaining_root,
                                        			   CASE 
                                        				   WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.remaining_root / client_loan_agreement.exchange_rate,2)
                                        				   WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate,2)
                                        			   END AS remaining_root_gel,
                                                       ROUND(client_loan_agreement.insurance_fee,2) AS insurance_fee
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
                                    WHERE    `client`.actived = 1 AND client_loan_agreement.status = 1 
		                            ORDER BY client.name");

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
	    $sub_client = check_sub_client($id);
	    
	    if ($sub_client>0) {
	        $query = "";
	    }else{
	        $query = "SELECT    client_loan_agreement.client_id,
                    			client_loan_agreement.id AS `id`,
                    			client_loan_agreement.datetime AS sort,
                    			'0' AS sort1,
            				    '' AS number,
            				    DATE(client_loan_agreement.datetime) AS `date`,
            					client_loan_agreement.exchange_rate AS `exchange`,
            					client_loan_agreement.loan_amount AS `loan_amount`,
            					CASE 
            					   WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2)
            					   WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2)
            					END AS `loan_amount_gel`,
            					'' AS percent,
            					'' AS percent_gel,
            					'' AS percent1,
            					'' AS percent_gel1,
            					'' AS pay_root,
            					'' AS pay_root_gel
                		FROM    client_loan_agreement
                		WHERE   client_loan_agreement.client_id = '$id'
	                    UNION ALL";
	    }
	    
	    $loan_currency_id = $_REQUEST['loan_currency_id'];
	    if ($loan_currency_id == 1) {
	        $rResult = mysql_query("SELECT   letter.client_id,
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
                            				 letter.pay_root_gel,
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 letter.sort1,
                            				 letter.loan_amount_gel
                                    FROM(   $query 
                                    		SELECT   client_loan_agreement.client_id,
                            						 client_loan_schedule.id AS `id`,
                            						 client_loan_schedule.pay_date AS sort,
                            						 '1' AS sort1,
                            						 client_loan_schedule.number,
                            						 DATE(client_loan_schedule.pay_date) AS `date`,
                            						 client_loan_agreement.exchange_rate AS `exchange`,
                            						 '' AS `loan_amount`,
                            						 '' AS `loan_amount_gel`,
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
                                    		WHERE    client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                    		GROUP BY money_transactions.client_loan_schedule_id
                                    		UNION ALL 
                                    		SELECT  client_loan_agreement.client_id,
                            						client_loan_schedule.id AS `id`,
                            						client_loan_schedule.pay_date AS sort,
                            						'3' AS sort1,
                            						client_loan_schedule.number,
                            						DATE(money_transactions.pay_datetime) AS `date`,
                            						money_transactions.course AS `exchange`,
                            						'' AS `loan_amount`,
                            						'' AS `loan_amount_gel`,
                            						'' AS percent,
                            						'' AS percent_gel,
                            						ROUND(SUM(money_transactions.pay_percent),2) AS percent1,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_percent)/money_transactions.course,2)
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_percent)*money_transactions.course,2)
                            						END AS percent_gel1,
                            						ROUND(SUM(money_transactions.pay_root),2) AS pay_root,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_root)/money_transactions.course,2)
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_root)*money_transactions.course,2)
                            						END AS pay_root_gel
                                    		FROM    money_transactions
                                    		JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                    		JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    		WHERE   client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 
                                    		AND     money_transactions.status IN (1,2,3) AND money_transactions.pay_percent != '0.00'
                                    		GROUP BY money_transactions.client_loan_schedule_id
                                    		UNION ALL
                                    		SELECT  client_loan_agreement.client_id,
                            						client_loan_schedule.id AS `id`,
                            						client_loan_schedule.pay_date AS sort,
                            						'4' AS sort1,
                            						client_loan_schedule.number,
                            						DATE(money_transactions.pay_datetime) AS `date`,
                            						money_transactions.course AS `exchange`,
                            						'' AS `loan_amount`,
                            						'' AS `loan_amount_gel`,
                            						'' AS percent,
                            						'' AS percent_gel,
                            						ROUND(money_transactions.pay_amount,2) AS percent1,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                            						END AS percent_gel1,
                            						ROUND(money_transactions.pay_root,2) AS pay_root,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                            						END AS pay_root_gel
                            				FROM   money_transactions
                            				JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                            				JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            				WHERE  client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 AND money_transactions.status = 3 AND money_transactions.actived = 1 AND money_transactions.pay_amount > 1
                            				UNION ALL
                            				SELECT  client_loan_agreement.client_id,
                    								client_loan_schedule.id AS `id`,
                    								client_loan_schedule.pay_date AS sort,
                    								'2' AS sort1,
                    								client_loan_schedule.number,
                    								DATE(money_transactions.pay_datetime) AS `date`,
                    								money_transactions.course AS `exchange`,
                    								'' AS `loan_amount`,
                    								DATEDIFF(money_transactions.datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
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
                            				WHERE  client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 AND money_transactions.status = 2
                                            UNION ALL
	                                        SELECT  client_loan_agreement.client_id,
                                        			client_loan_agreement.id AS `id`,
                                        			client_loan_agreement.datetime AS sort,
                                        			'0' AS sort1,
                                				    '' AS number,
                                				    DATE(client_loan_agreement.datetime) AS `date`,
                                					client_loan_agreement.exchange_rate AS `exchange`,
                                					client_loan_agreement.loan_amount AS `loan_amount`,
                                					CASE 
                                					   WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2)
                                					   WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2)
                                					END AS `loan_amount_gel`,
                                					'' AS percent,
                                					'' AS percent_gel,
                                					'' AS percent1,
                                					'' AS percent_gel1,
                                					'' AS pay_root,
                                					'' AS pay_root_gel
                                    		FROM    client_loan_agreement
                                    		WHERE   client_loan_agreement.client_id = '$sub_client'
	                                        UNION ALL
                                            SELECT client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'1' AS sort1,
                        							 client_loan_schedule.number,
                        							 DATE(client_loan_schedule.pay_date) AS `date`,
                        							 client_loan_agreement.exchange_rate AS `exchange`,
                        							 '' AS `loan_amount`,
                        							 '' AS `loan_amount_gel`,
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
                                			WHERE    client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                			GROUP BY money_transactions.client_loan_schedule_id
                                			UNION ALL 
                                			SELECT  client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'3' AS sort1,
                        							client_loan_schedule.number,
                        							DATE(money_transactions.pay_datetime) AS `date`,
                        							money_transactions.course AS `exchange`,
                        							'' AS `loan_amount`,
                        							'' AS `loan_amount_gel`,
                        							'' AS percent,
                        							'' AS percent_gel,
                        							ROUND(SUM(money_transactions.pay_percent),2) AS percent1,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_percent)/money_transactions.course,2)
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_percent)*money_transactions.course,2)
                        							END AS percent_gel1,
                        							ROUND(SUM(money_transactions.pay_root),2) AS pay_root,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_root)/money_transactions.course,2)
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_root)*money_transactions.course,2)
                        							END AS pay_root_gel
                                			FROM    money_transactions
                                			JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                			JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                			WHERE   client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 
                                			AND     money_transactions.status IN (1,2,3) AND money_transactions.pay_percent != '0.00'
                                			GROUP BY money_transactions.client_loan_schedule_id
                                			UNION ALL
                                			SELECT  client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'4' AS sort1,
                        							client_loan_schedule.number,
                        							DATE(money_transactions.pay_datetime) AS `date`,
                        							money_transactions.course AS `exchange`,
                        							'' AS `loan_amount`,
                        							'' AS `loan_amount_gel`,
                        							'' AS percent,
                        							'' AS percent_gel,
                        							ROUND(money_transactions.pay_amount,2) AS percent1,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                        							END AS percent_gel1,
                        							ROUND(money_transactions.pay_root,2) AS pay_root,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                        							END AS pay_root_gel
                                			FROM   money_transactions
                                			JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                			JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                			WHERE  client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 AND money_transactions.status = 3 AND money_transactions.actived = 1 AND money_transactions.pay_amount > 1
                                			UNION ALL
                                			SELECT  client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'2' AS sort1,
                        							client_loan_schedule.number,
                        							DATE(money_transactions.pay_datetime) AS `date`,
                        							money_transactions.course AS `exchange`,
                        							'' AS `loan_amount`,
                        							DATEDIFF(money_transactions.datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
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
                                			WHERE  client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 AND money_transactions.status = 2)AS letter
                                            ORDER BY letter.number, letter.sort,  letter.sort1 ASC ");
	    }else{	
    	    $rResult = mysql_query("SELECT   letter.client_id,
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
                            				 letter.pay_root_gel,
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 '',
                            				 letter.sort1,
                            				 letter.loan_amount_gel
                                    FROM(   $query 
                                    		SELECT   client_loan_agreement.client_id,
                            						 client_loan_schedule.id AS `id`,
                            						 client_loan_schedule.pay_date AS sort,
                            						 '1' AS sort1,
                            						 client_loan_schedule.number,
                            						 DATE(client_loan_schedule.pay_date) AS `date`,
                            						 client_loan_agreement.exchange_rate AS `exchange`,
                            						 '' AS `loan_amount`,
                            						 '' AS `loan_amount_gel`,
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
                                    		WHERE    client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                    		GROUP BY money_transactions.client_loan_schedule_id
                                    		UNION ALL 
                                    		SELECT  client_loan_agreement.client_id,
                            						client_loan_schedule.id AS `id`,
                            						client_loan_schedule.pay_date AS sort,
                            						'3' AS sort1,
                            						client_loan_schedule.number,
                            						DATE(money_transactions.pay_datetime) AS `date`,
                            						money_transactions.course AS `exchange`,
                            						'' AS `loan_amount`,
                            						'' AS `loan_amount_gel`,
                            						'' AS percent,
                            						'' AS percent_gel,
                            						ROUND(SUM(money_transactions.pay_percent),2) AS percent1,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_percent)/money_transactions.course,2)
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_percent)*money_transactions.course,2)
                            						END AS percent_gel1,
                            						ROUND(SUM(money_transactions.pay_root),2) AS pay_root,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_root)/money_transactions.course,2)
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_root)*money_transactions.course,2)
                            						END AS pay_root_gel
                                    		FROM    money_transactions
                                    		JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                    		JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    		WHERE   client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 
                                    		AND     money_transactions.status IN (1,2,3) AND money_transactions.pay_percent != '0.00'
                                    		GROUP BY money_transactions.client_loan_schedule_id
                                    		UNION ALL
                                    		SELECT  client_loan_agreement.client_id,
                            						client_loan_schedule.id AS `id`,
                            						client_loan_schedule.pay_date AS sort,
                            						'4' AS sort1,
                            						client_loan_schedule.number,
                            						DATE(money_transactions.pay_datetime) AS `date`,
                            						money_transactions.course AS `exchange`,
                            						'' AS `loan_amount`,
                            						'' AS `loan_amount_gel`,
                            						'' AS percent,
                            						'' AS percent_gel,
                            						ROUND(money_transactions.pay_amount,2) AS percent1,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                            						END AS percent_gel1,
                            						ROUND(money_transactions.pay_root,2) AS pay_root,
                            						CASE 
                            							WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                            							WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                            						END AS pay_root_gel
                            				FROM   money_transactions
                            				JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                            				JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            				WHERE  client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 AND money_transactions.status = 3 AND money_transactions.actived = 1 AND money_transactions.pay_amount > 1
                            				UNION ALL
                            				SELECT  client_loan_agreement.client_id,
                    								client_loan_schedule.id AS `id`,
                    								client_loan_schedule.pay_date AS sort,
                    								'2' AS sort1,
                    								client_loan_schedule.number,
                    								DATE(money_transactions.pay_datetime) AS `date`,
                    								money_transactions.course AS `exchange`,
                    								'' AS `loan_amount`,
                    								DATEDIFF(money_transactions.datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
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
                            				WHERE  client_loan_agreement.client_id = '$id' AND client_loan_schedule.actived=1 AND money_transactions.status = 2
                                            UNION ALL
	                                        SELECT  client_loan_agreement.client_id,
                                        			client_loan_agreement.id AS `id`,
                                        			client_loan_agreement.datetime AS sort,
                                        			'0' AS sort1,
                                				    '' AS number,
                                				    DATE(client_loan_agreement.datetime) AS `date`,
                                					client_loan_agreement.exchange_rate AS `exchange`,
                                					client_loan_agreement.loan_amount AS `loan_amount`,
                                					CASE 
                                					   WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2)
                                					   WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2)
                                					END AS `loan_amount_gel`,
                                					'' AS percent,
                                					'' AS percent_gel,
                                					'' AS percent1,
                                					'' AS percent_gel1,
                                					'' AS pay_root,
                                					'' AS pay_root_gel
                                    		FROM    client_loan_agreement
                                    		WHERE   client_loan_agreement.client_id = '$sub_client'
	                                        UNION ALL
                                            SELECT client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'1' AS sort1,
                        							 client_loan_schedule.number,
                        							 DATE(client_loan_schedule.pay_date) AS `date`,
                        							 client_loan_agreement.exchange_rate AS `exchange`,
                        							 '' AS `loan_amount`,
                        							 '' AS `loan_amount_gel`,
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
                                			WHERE    client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 AND money_transactions.status = 1
                                			GROUP BY money_transactions.client_loan_schedule_id
                                			UNION ALL 
                                			SELECT  client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'3' AS sort1,
                        							client_loan_schedule.number,
                        							DATE(money_transactions.pay_datetime) AS `date`,
                        							money_transactions.course AS `exchange`,
                        							'' AS `loan_amount`,
                        							'' AS `loan_amount_gel`,
                        							'' AS percent,
                        							'' AS percent_gel,
                        							ROUND(SUM(money_transactions.pay_percent),2) AS percent1,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_percent)/money_transactions.course,2)
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_percent)*money_transactions.course,2)
                        							END AS percent_gel1,
                        							ROUND(SUM(money_transactions.pay_root),2) AS pay_root,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(SUM(money_transactions.pay_root)/money_transactions.course,2)
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(SUM(money_transactions.pay_root)*money_transactions.course,2)
                        							END AS pay_root_gel
                                			FROM    money_transactions
                                			JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                			JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                			WHERE   client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 
                                			AND     money_transactions.status IN (1,2,3) AND money_transactions.pay_percent != '0.00'
                                			GROUP BY money_transactions.client_loan_schedule_id
                                			UNION ALL
                                			SELECT  client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'4' AS sort1,
                        							client_loan_schedule.number,
                        							DATE(money_transactions.pay_datetime) AS `date`,
                        							money_transactions.course AS `exchange`,
                        							'' AS `loan_amount`,
                        							'' AS `loan_amount_gel`,
                        							'' AS percent,
                        							'' AS percent_gel,
                        							ROUND(money_transactions.pay_amount,2) AS percent1,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_amount/course,2) 
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_amount*course,2) 
                        							END AS percent_gel1,
                        							ROUND(money_transactions.pay_root,2) AS pay_root,
                        							CASE 
                        								WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(money_transactions.pay_root/money_transactions.course,2)
                        								WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(money_transactions.pay_root*money_transactions.course,2)
                        							END AS pay_root_gel
                                			FROM   money_transactions
                                			JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                			JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                			WHERE  client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 AND money_transactions.status = 3 AND money_transactions.actived = 1 AND money_transactions.pay_amount > 1
                                			UNION ALL
                                			SELECT  client_loan_agreement.client_id,
                        							client_loan_schedule.id AS `id`,
                        							client_loan_schedule.pay_date AS sort,
                        							'2' AS sort1,
                        							client_loan_schedule.number,
                        							DATE(money_transactions.pay_datetime) AS `date`,
                        							money_transactions.course AS `exchange`,
                        							'' AS `loan_amount`,
                        							DATEDIFF(money_transactions.datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
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
                                			WHERE  client_loan_agreement.client_id = '$sub_client' AND client_loan_schedule.actived=1 AND money_transactions.status = 2)AS letter
                                            ORDER BY letter.number, letter.sort,  letter.sort1 ASC ");
	    }
	    
	    $sumpercent  = 0;
	    $sumpercent1 = 0;
	    $sumpercent2 = 0;
	    $sumpercent3 = 0;
	    $sumpercent4 = 0;
	    $sumpercent5 = 0;
	    $data = array("aaData"	=> array(), "aaData1"	=> '', "aaData2"	=> '');
	    $j=1;
	    while ( $aRow = mysql_fetch_array( $rResult )){
	        
	        $row = array();
	        for ( $i = 0 ; $i < $count ; $i++ ){
	            if ($j>1 && $i>3 && $i<6) {
	                $row[] = '';
	            }else{
	               if ($i == 6) {
	                   $sumpercent+=$aRow[$i];
	                   if($aRow[sort1]==2){
	                       $row[] = '<div title="'.$aRow[loan_amount_gel].' დღის ჯარიმა" style="background: #009688;  color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
    	                   $row[] = $aRow[$i];
    	               }
	               }else if ($i == 7){
	                   $sumpercent1+=$aRow[$i];
	                   if($aRow[sort1]==2){
	                       $row[] = '<div title="'.$aRow[loan_amount_gel].' დღის ჯარიმა" style="background: #009688; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 8){
	                   $sumpercent2+=$aRow[$i];
	                   if($aRow[sort1]==4){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   } 
	               }elseif ($i == 9){
	                   $sumpercent3+=$aRow[$i];
	                   if($aRow[sort1]==4){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 10){
	                   $sumpercent4+=$aRow[$i];
	                   if($aRow[sort1]==4){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 11){
	                   $sumpercent5+=$aRow[$i];
	                   if($aRow[sort1]==4){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }else{
	                   $row[] = $aRow[$i];
	               }
	            }
	        }
	        $data['aaData'][] = $row;
	        $j++;
	    }
	    
	    $data['aaData1'][] = $sumpercent;
	    $data['aaData2'][] = $sumpercent1;
	    $data['aaData3'][] = $sumpercent2;
	    $data['aaData4'][] = $sumpercent3;
	    $data['aaData5'][] = $sumpercent4;
	    $data['aaData6'][] = $sumpercent5;
	    
	    break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);
function check_sub_client($id){
    $res=mysql_fetch_assoc(mysql_query("SELECT IF(ISNULL(sub_client),0,sub_client) AS `sub_client`
                                        FROM  `client` 
                                        WHERE  id = '$id';"));
    return $res[sub_client];
}
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
                        <th id ="daricxva_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="procenti_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="procenti_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="dziri_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="dziri_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
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
                        <th id ="gacema_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="daricxva_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="daricxva_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="procenti_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="procenti_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="dziri_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id ="dziri_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
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
    
  $res = mysql_fetch_assoc(mysql_query("SELECT  IF(ISNULL(client.sub_client) OR ISNULL(client.sub_client),0,client.sub_client) AS sub_client,
                                                client_loan_agreement.loan_months,
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
  if ($res[sub_client] > 0) {
      $dis = '';
      $res1 = mysql_fetch_assoc(mysql_query(" SELECT client_loan_agreement.loan_months,
                                                     client_loan_agreement.loan_amount,
                                                     client_loan_agreement.percent,
                                                     DATE_FORMAT(client_loan_agreement.datetime,'%m') AS `month_id`,
                                                     DATE_FORMAT(client_loan_agreement.datetime,'%Y') AS `year`,
                                                     DATE_FORMAT(client_loan_agreement.datetime,'%d') AS `day`,
                                                     CONCAT(client.`name`,' ',client.lastname) AS `name`,
                                                     client_loan_agreement.loan_type_id,
                                                     client_loan_agreement.loan_currency_id
                                              FROM  `client_loan_agreement`
                                              JOIN   client ON client.id = client_loan_agreement.client_id
                                              WHERE  client.actived = 1 AND client.id = '$res[sub_client]'"));
      
      $req1 = mysql_query("SELECT client_loan_schedule.number,
                                  client_loan_schedule.schedule_date,
                                  client_loan_schedule.root,
                                  client_loan_schedule.percent,
                                  client_loan_schedule.pay_amount,
                                  client_loan_schedule.remaining_root,
                                  client_loan_schedule.status
                           FROM   client_loan_schedule
                           JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                           WHERE  client_loan_agreement.client_id = '$res[sub_client]' AND client_loan_schedule.actived=1");
      
      while ($row1 = mysql_fetch_assoc($req1)){
          $sum_percent1 += $row1[percent];
          $sum_P1       += $row1[pay_amount];
      
          $color1        = "";
      
          if ($row1[status] == 1) {
              $color1 = 'background: #4CAF50;';
          }
      
          $dat1.='<tr style="width:100%; border: 1px solid #000; '.$color1.'">
                        <td style="width:5%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[number].'<label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[schedule_date].'</label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[root].'</label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[percent].'<label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[pay_amount].'</label></td>
                        <td style="width:19%;"><label style="font-size: 12px; text-align:center;">'.$row1[remaining_root].'</label></td>
                    </tr>';
      }
      $hint1 = 'წლ';
      $percent1 = $res1[percent] * 12;
      
      if ($res1[loan_type_id] == 1) {
          $hint1 = 'თვ';
          $percent1 = $res1[percent] * $res1[loan_months];
      }
  }else{
      $dis='display:none';
  }
  
  $hint = 'წლ';
  $percent = $res[percent] * 12;
  
  if ($res[loan_type_id] == 1) {
     $hint = 'თვ';
     $percent = $res[percent] * $res[loan_months];
  }
  

  
  
 $data = '<div id="dialog-form" style="overflow-y: scroll; height: 550px;">
                <fieldset>
                    <legend>გრაფიკი</legend>
                    <div style="width:100%; font-size: 14px;">
                        <table style="width:100%;">
                            <tr style="width:100%;">
                                <td style="width:12%;"><label style="font-size: 14px;">კლიენტის სახელი:<label></td>
                                <td style="width:88%;"><label style="font-size: 14px;">'.$res[name].'</label></td>
                            </tr>
                        </table> 
                    </div>
                    <div style="width:100%;">
                        <table style="width:100%;">
                             <tr style="width:100%;">
                                 <td  style="width:49%; '.$dis.'">
                                    <div style="width:100%; margin-top: 5px;">
                                        <table style="width:100%;">
                                            <tr style="width:100%;border: 1px solid #000;">
                                                <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">სესხის მცულობა:<label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res1[loan_amount].'</label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;"></label></td>
                                                <td colspan="2" style="width:30%;"><label style="font-size: 12px; text-align:center;">სესხის გაცემის თარიღი</label></td>
                                            </tr>
                                            <tr style="width:100%;border: 1px solid #000;">
                                                <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">საპროცემტო სარგ. ('.$hint1.'):<label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.round($percent1,2).'</label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                                <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თვე</label></td>
                                                <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res1[month_id].'</label></td>
                                            </tr>
                                            <tr style="width:100%;border: 1px solid #000;">
                                                <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">ვადა:<label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res1[loan_months].'</label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                                <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">რიცხვი</label></td>
                                                <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res1[day].'</label></td>
                                            </tr>
                                            <tr style="width:100%;border: 1px solid #000;">
                                                <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">საშეღავათო პერიოდი:<label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                                <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">წელი</label></td>
                                                <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res1[year].'</label></td>
                                            </tr>
                                            <tr style="width:100%;border: 1px solid #000;">
                                                <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">საკომისიო წინასწარ(%):<label></td>
                                                <td colspan="4" style="width:20%;"><label style="font-size: 12px;"><label></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div style="width:100%; margin-top: 5px; border: 1px solid #000;">
                                        <table style="width:100%;">
                                            <tr style="width:100%;border: 1px solid #000;">
                                                <td colspan="2" style="width:5%;border-right: 1px solid #000;"><label style="font-size: 12px;">სულ პროცენტი<label></td>
                                                <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px;">სულ დასაფარი</label></td>
                                                <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($sum_percent1,2).'</label></td>
                                                <td style="width:19%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.round($sum_P1, 2).'</label></td>
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
                                $data.=$dat1;
                                $data.='
                                    </table>
                                 </div>
                            </td>
                            <td style="width:2%; '.$dis.'"></td>
                            <td style="width:49%;">
                                <div style="width:100%; margin-top: 5px; float:right">
                                    <table style="width:100%;">
                                        <tr style="width:100%;border: 1px solid #000;">
                                            <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">სესხის მცულობა:<label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res[loan_amount].'</label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;"></label></td>
                                            <td colspan="2" style="width:30%;"><label style="font-size: 12px; text-align:center;">სესხის გაცემის თარიღი</label></td>
                                        </tr>
                                        <tr style="width:100%;border: 1px solid #000;">
                                            <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">საპროცემტო სარგ. ('.$hint.'):<label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.round($percent,2).'</label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თვე</label></td>
                                            <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res[month_id].'</label></td>
                                        </tr>
                                        <tr style="width:100%;border: 1px solid #000;">
                                            <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">ვადა:<label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res[loan_months].'</label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">რიცხვი</label></td>
                                            <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res[day].'</label></td>
                                        </tr>
                                        <tr style="width:100%;border: 1px solid #000;">
                                            <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">საშეღავათო პერიოდი:<label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">წელი</label></td>
                                            <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res[year].'</label></td>
                                        </tr>
                                        <tr style="width:100%;border: 1px solid #000;">
                                            <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">საკომისიო წინასწარ(%):<label></td>
                                            <td colspan="4" style="width:20%;"><label style="font-size: 12px;"><label></td>
                                        </tr>
                                    </table>
                                </div>
                                <div style="width:100%; margin-top: 5px; border: 1px solid #000; float:right">
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
                            $data.='
                                </table>
                            </div> 
                      </td>
                  </tr>
                </table>
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
