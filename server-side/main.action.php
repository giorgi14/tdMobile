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
        
        $id	= $_REQUEST['id'];
 
        $req = mysql_fetch_array(mysql_query("SELECT IFNULL(MAX(client_loan_schedule.remaining_root+client_loan_schedule.root),0.00) AS remaining_root,
                                                     CASE 
                                        				  WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(MAX(client_loan_schedule.remaining_root + client_loan_schedule.root) / client_loan_agreement.exchange_rate,2)
                                        				  WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(MAX(client_loan_schedule.remaining_root + client_loan_schedule.root)* client_loan_agreement.exchange_rate,2)
                                                     END AS remaining_root_gel
                                              FROM   client_loan_schedule
                                              JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                              WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.`status` = 0 AND client_loan_schedule.actived = 1
                                              LIMIT 1"));
        
        $data = array('delta' => $req[remaining_root], 'remaining_root_gel' => $req[remaining_root_gel]);
        
    
        break;
    case 'save_comment':
        
        $id	            = $_REQUEST['hidde_id'];
        $letter_comment	= $_REQUEST['letter_comment'];
    
        mysql_query("UPDATE client
                        SET letter_comment = '$letter_comment'
                     WHERE  id = '$id'");
    
        break;
    case 'reregistering_ltd':
    
        $local_id	                = $_REQUEST['local_id'];
        $reregistering_date	        = $_REQUEST['reregistering_date'];
        $reregistering_root_fee	    = $_REQUEST['reregistering_root_fee'];
        $reregistering_percent_fee	= $_REQUEST['reregistering_percent_fee'];
        $reregistering_penalty_fee	= $_REQUEST['reregistering_penalty_fee'];
        $reregistering_deal_fee  	= $_REQUEST['reregistering_deal_fee'];
        $reregistering_sakomiso	    = $_REQUEST['reregistering_sakomiso'];
        $reregistering_nasargeblebi	= $_REQUEST['reregistering_nasargeblebi'];
        $reregistering_fee	        = $_REQUEST['reregistering_fee'];
        $reregistering_avans    	= $_REQUEST['reregistering_avans'];
        $reregistering_pledge	    = $_REQUEST['reregistering_pledge'];
        $reregistering_other	    = $_REQUEST['reregistering_other'];
    
        $reregistering_fee = round($reregistering_fee+$reregistering_avans,2);
        
        $reg_cource = mysql_fetch_array(mysql_query("SELECT cource
                                                     FROM   cur_cource
                                                     WHERE  actived = 1 
                                                     AND    DATE(cur_cource.datetime) = '$reregistering_date' 
                                                     LIMIT 1"));
        
        $agr_info = mysql_fetch_array(mysql_query("  SELECT    client_loan_agreement.id,
                                                               client_loan_agreement.loan_currency_id,
                                                               client_loan_schedule.id AS sh_id 
                                                     FROM      client_loan_agreement 
                                                     JOIN      client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                     WHERE     client_loan_agreement.client_id = '$local_id' 
                                                     AND       client_loan_schedule.schedule_date <= '$reregistering_date'
                                                     AND       client_loan_schedule.actived = 1
                                                     AND       client_loan_schedule.activ_status = 0
                                                     ORDER BY  pay_date ASC
                                                     LIMIT 1"));
        if ($reregistering_fee>0) {
            mysql_query("INSERT INTO `money_transactions`
                                    (`datetime`, `user_id`, `client_loan_schedule_id`, `agreement_id`, `client_id`, `pay_datetime`, `pay_amount`, `extra_fee`, `course`, `currency_id`, `received_currency_id`, `month_fee_trasaction`, `type_id`, `status`, `reg_ltd`, `actived`)
                              VALUES
                                    (NOW(), '$user_id', '$agr_info[sh_id]', '$agr_info[id]', '$local_id', '$reregistering_date', '$reregistering_fee', '$reregistering_fee', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '', '1', '1', '1', '1')");
            
            $tr_id = mysql_insert_id();
            
            $res_avans = mysql_query("SELECT  money_transactions_detail.id AS id
                                      FROM    money_transactions_detail
                                      JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                      JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                      WHERE   client_loan_agreement.client_id = '$local_id'
                                      AND     money_transactions_detail.`status` = 3
                                      AND     money_transactions_detail.actived = 1");
            
            while ($arow_avans = mysql_fetch_array($res_avans)) {
                
                mysql_query("UPDATE money_transactions_detail
                                SET ltd_regist_tr_id = '$tr_id'
                             WHERE  id               = '$arow_avans[id]'");
            }
            
            mysql_query("UPDATE client_loan_schedule
                            SET activ_status = 1
                         WHERE  client_loan_schedule.client_loan_agreement_id = '$agr_info[id]'
                         AND    client_loan_schedule.actived = 1
                         AND    client_loan_schedule.schedule_date > '$reregistering_date'");
             
            mysql_query("UPDATE client_loan_agreement
                            SET canceled_status = 1,
                                canseled_date   = '$reregistering_date'
                         WHERE  client_loan_agreement.id = '$agr_info[id]'");
            
            if ($reregistering_other > 0) {
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$reregistering_date', '$reregistering_other', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '', '', '3', '11', 1)");
            }
            if ($reregistering_pledge > 0) {
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$reregistering_date', '$reregistering_pledge', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '', '', '2', '8', 1)");
            }
            
            if ($reregistering_root_fee>0 || $reregistering_percent_fee>0) {
                 
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$reregistering_date', '', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '$reregistering_root_fee', '$reregistering_percent_fee', '1', 1, 1)");
            }
            
            if ($reregistering_penalty_fee>0){
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$reregistering_date', '$reregistering_penalty_fee', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '', '', '1', '2', 1)");
            }
            
            if ($reregistering_deal_fee>0){
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$reregistering_date', '$reregistering_deal_fee', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '', '', '1', '13', 1)");
                
                mysql_query("UPDATE `deals_detail`
                             JOIN    client_loan_schedule_deal ON client_loan_schedule_deal.id = deals_detail.deals_id
                             JOIN    client_loan_schedule ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                             JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                SET `status` = '1'
                             WHERE   client_loan_agreement.id = $agr_info[id]");
            }
            
            if ($reregistering_sakomiso>0) {
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$reregistering_date', '$reregistering_sakomiso', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '', '', '1', 5, 1)");
            }
             
            if ($reregistering_nasargeblebi>0) {
                mysql_query("INSERT INTO `money_transactions_detail`
                                        (`datetime`, `user_id`, `transaction_id`, `pay_datetime`, `pay_amount`, `course`, `currency_id`, `received_currency_id`, `pay_root`, `pay_percent`, `type_id`, `status`, `actived`)
                                  VALUES
                                        (NOW(), '$user_id', '$tr_id', '$reregistering_date', '$reregistering_nasargeblebi', '$reg_cource[cource]', '$agr_info[loan_currency_id]', '$agr_info[loan_currency_id]', '', '', '1', 6, 1)");
            }
        }
        
        
    
        break;
	case 'get_list' :
		$count	   = $_REQUEST['count'];
		$hidden	   = $_REQUEST['hidden'];
		$filt_year = $_REQUEST['filt_year'];
		
		 
		$rResult = mysql_query("  SELECT    client.id,
                        					DATE_FORMAT(client_loan_agreement.datetime,'%d/%m/%Y'),
                        					concat(client_car.car_marc, ' / ', client_car.registration_number, ' / ', client.name, ' / ', client.lastname),
                        					client_loan_agreement.oris_code,
		                                    CASE
                        						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number),'',' დ.'), IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number), '', client_loan_agreement.attachment_number))
                        						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
                                                 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
                                                 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
                            			    END AS agreement_id,
                        					IF(client_loan_agreement.loan_type_id =2,'გრაფიკი','ჩვეულებრივი'),
                        					ROUND(IF(client_loan_agreement.loan_currency_id = 1, client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate, client_loan_agreement.loan_amount),2),
                        					client_loan_agreement.exchange_rate,
                        					ROUND(IF(client_loan_agreement.loan_currency_id = 1, client_loan_agreement.loan_amount,client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2),
		                                    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT ROUND(client_loan_schedule.percent/(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2)
                                                      FROM   client_loan_agreement
                                                      JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE  client_loan_agreement.client_id = client.id
                                                      AND    client_loan_schedule.actived = 1
                                                      AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                      AND    MONTH(client_loan_schedule.schedule_date) = MONTH(NOW()) 
                                                      AND    client_loan_schedule.schedule_date <= DATE(NOW()) LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT ROUND(client_loan_schedule.percent,2)
                                                      FROM   client_loan_agreement
                                                      JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE  client_loan_agreement.client_id = client.id
                                                      AND    client_loan_schedule.actived = 1
                                                      AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                      AND    MONTH(client_loan_schedule.schedule_date) = MONTH(NOW()) 
                                                      AND    client_loan_schedule.schedule_date <= DATE(NOW()) LIMIT 1) 
                    					    END AS daricxuli_dolari,
                    					    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT ROUND(client_loan_schedule.percent,2)
                                                      FROM   client_loan_agreement
                                                      JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE  client_loan_agreement.client_id = client.id
                                                      AND    client_loan_schedule.actived = 1
                                                      AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                      AND    MONTH(client_loan_schedule.schedule_date) = MONTH(NOW()) 
                                                      AND    client_loan_schedule.schedule_date <= DATE(NOW()) LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT ROUND(client_loan_schedule.percent*(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2)
                                                      FROM   client_loan_agreement
                                                      JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE  client_loan_agreement.client_id = client.id
                                                      AND    client_loan_schedule.actived = 1
                                                      AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                      AND    MONTH(client_loan_schedule.schedule_date) = MONTH(NOW()) 
                                                      AND    client_loan_schedule.schedule_date <= DATE(NOW()) LIMIT 1) 
                    					    END AS daricxuli_lari,
                        					CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN '0.00'
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT CASE
                                                        		 WHEN client_loan_schedule.`status` = 0 THEN client_loan_schedule.remaining_root + client_loan_schedule.root+(SELECT IFNULL(SUM(client_loan_schedule.percent),0) FROM client_loan_schedule WHERE client_loan_schedule.actived = 1 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND client_loan_schedule.`status` = 0 AND YEAR(client_loan_schedule.schedule_date) <= '$filt_year' AND MONTH(client_loan_schedule.schedule_date) < MONTH(NOW()) AND client_loan_schedule.schedule_date < DATE(NOW()))
                                                                 WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root + (SELECT IFNULL(SUM(client_loan_schedule.percent),0) FROM client_loan_schedule WHERE client_loan_schedule.actived = 1 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND client_loan_schedule.`status` = 0 AND YEAR(client_loan_schedule.schedule_date) <= '$filt_year' AND MONTH(client_loan_schedule.schedule_date) < MONTH(NOW()) AND client_loan_schedule.schedule_date < DATE(NOW()))
                                                              END  AS rem
                                                       FROM   client_loan_agreement
                                                       JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                       WHERE  client_loan_agreement.client_id = client.id
                                                       AND    client_loan_schedule.actived = 1
                                                       AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                       AND    MONTH(client_loan_schedule.schedule_date) <= MONTH(NOW()) 
                                                       AND    client_loan_schedule.schedule_date <= DATE(NOW())
                                                       ORDER BY client_loan_schedule.id DESC
                                                       LIMIT 1) 
                    					    END AS darchenili_vali_dolari,
		                                    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN '0.00' 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN (SELECT CASE
                                                        		  WHEN client_loan_schedule.`status` = 0 THEN client_loan_schedule.remaining_root + client_loan_schedule.root
                                                                  WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root
                                                             END  AS rem
                                                      FROM   client_loan_agreement
                                                      JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE  client_loan_agreement.client_id = client.id
                                                      AND    client_loan_schedule.actived = 1
                                                      AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                      AND    MONTH(client_loan_schedule.schedule_date) <= MONTH(NOW()) 
                                                      AND    client_loan_schedule.schedule_date <= DATE(NOW())
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    					    END AS darchenili_dziri_dolari,
		                                    CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT CASE
                                                        		 WHEN client_loan_schedule.`status` = 0 THEN client_loan_schedule.remaining_root + client_loan_schedule.root+(SELECT IFNULL(SUM(client_loan_schedule.percent),0) FROM client_loan_schedule WHERE client_loan_schedule.actived = 1 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND client_loan_schedule.`status` = 0 AND YEAR(client_loan_schedule.schedule_date) <= '$filt_year' AND MONTH(client_loan_schedule.schedule_date) < MONTH(NOW()) AND client_loan_schedule.schedule_date < DATE(NOW()))
                                                                 WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root + (SELECT IFNULL(SUM(client_loan_schedule.percent),0) FROM client_loan_schedule WHERE client_loan_schedule.actived = 1 AND client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id AND client_loan_schedule.`status` = 0 AND YEAR(client_loan_schedule.schedule_date) <= '$filt_year' AND MONTH(client_loan_schedule.schedule_date) < MONTH(NOW()) AND client_loan_schedule.schedule_date < DATE(NOW()))
                                                              END  AS rem
                                                       FROM   client_loan_agreement
                                                       JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                       WHERE  client_loan_agreement.client_id = client.id
                                                       AND    client_loan_schedule.actived = 1
                                                       AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                       AND    MONTH(client_loan_schedule.schedule_date) <= MONTH(NOW()) 
                                                       AND    client_loan_schedule.schedule_date <= DATE(NOW())
                                                       ORDER BY client_loan_schedule.id DESC
                                                       LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN '0.00' 
                    					    END AS darchenili_vali_lari,
                        					CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 
                    							THEN (SELECT CASE
                                                        		  WHEN client_loan_schedule.`status` = 0 THEN client_loan_schedule.remaining_root + client_loan_schedule.root
                                                                  WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root
                                                             END  AS rem
                                                      FROM   client_loan_agreement
                                                      JOIN   client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                      WHERE  client_loan_agreement.client_id = client.id
                                                      AND    client_loan_schedule.actived = 1
                                                      AND    YEAR(client_loan_schedule.schedule_date) = '$filt_year'
                                                      AND    MONTH(client_loan_schedule.schedule_date) <= MONTH(NOW()) 
                                                      AND    client_loan_schedule.schedule_date <= DATE(NOW())
                                                      ORDER BY client_loan_schedule.id DESC
                                                      LIMIT 1) 
                    							WHEN client_loan_agreement.loan_currency_id = 2 
                    							THEN '0.00' 
                    					    END AS darchenili_dziri_lari,
                        					'',
		                                    ROUND((SELECT SUM(difference) 
                                                   FROM   difference_cource
                                                   WHERE  difference_cource.client_id = `client`.id 
		                                           AND    YEAR(difference_cource.datetime) = '$filt_year'),2) AS dsf
                                    FROM     `client`
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = `client`.id 
                                    LEFT JOIN client_car ON client_car.client_id = `client`.id 
                                    WHERE    `client`.actived = 1 AND client_loan_agreement.status = 1 AND client.attachmets_status = 0
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
	    
	    //$sub_client = check_sub_client($id);
	    $cl_id = $id;
	    $check = 1;
	    $i = 0;
	    $qvr = '';
	    $loan_currency_id = $_REQUEST['loan_currency_id'];
	    if ($loan_currency_id == 1) {
    	    while ($check == 1) {
    	        $i++;
    	        $sub_client_id = mysql_fetch_array(mysql_query("SELECT IFNULL(sub_client,0) AS sub_client,
    	                                                               id
                                                	            FROM   client
                                                	            WHERE  client.id = $cl_id"));
    	        $cl_id = $sub_client_id[sub_client];
    	        $sub   = $sub_client_id[id];
    	        
    	        if ($sub_client_id[sub_client] == 0){
    	            $check = 2;
    	                  $qvr .= " SELECT  client_loan_agreement.client_id,
                                			client_loan_agreement.id AS `id`,
                                			'' AS number1,
                                			'0' AS sort3,
                                			client_loan_agreement.datetime AS sort,
                                			'0' AS sort1,
                        				    '' AS number,
                        				    DATE_FORMAT(client_loan_agreement.datetime, '%d/%m/%Y') AS `date`,
                        					client_loan_agreement.exchange_rate AS `exchange`,
                        					CONCAT(client_loan_agreement.loan_amount,if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS `loan_amount`,
                        					CASE 
                        					   WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2),' USD')
                        					   WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2),' GEL')
                        					END AS `loan_amount_gel`,
                        					'' AS `delta`,
            	                            '' AS `delta1`,
                        					'' AS percent,
                        					'' AS percent_gel,
                        					'' AS percent1,
                        					'' AS percent_gel1,
                        					'' AS pay_root,
                        					'' AS pay_root_gel,
                        					'' AS jh,
                                            '' AS kj,
                                            '' AS difference,
                                            '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as pledge_payed,
                                            '' as pledge_payed1,
                                			'' AS pledge_delta,
                                            '' as other,
                                			'' as other1,
                                			'' as other_delta
                            		FROM    client_loan_agreement
                            		WHERE   client_loan_agreement.client_id = '$sub'
            	                    UNION ALL
    	                            SELECT  client_loan_agreement.client_id,
                            			    client_loan_agreement.id AS `id`,
                                            '' AS number1,
                                            '0' AS sort3,
                            			    DATE(client_loan_agreement.datetime) AS sort,
                            			    '1' AS sort1,
                            			    '' AS number,
                                            '01/07/2017' AS `date`,
                            				client_loan_agreement.exchange_rate AS `exchange`,
                            			    '' AS `loan_amount`,
                            				''AS `loan_amount_gel`,
                                            CONCAT(ROUND(client_loan_schedule.remaining_root,2),' GEL') AS delta,
                                            CONCAT( CASE 
                                    					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.remaining_root / client_loan_agreement.exchange_rate,2)
                                    					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate,2)
                                    			    END,' USD') AS delta1,
                                			'' AS percent,
                                			'' AS percent_gel,
                                			'' AS percent1,
                                			'' AS percent_gel1,
                                			'' AS pay_root,
                                			'' AS pay_root_gel,
                                			'' AS jh,
                                			'' AS kj,
                                			'' AS difference,
                                			'' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                                    FROM    client_loan_agreement
                                    JOIN    client_loan_schedule ON client_loan_agreement.old_schedule_id = client_loan_schedule.id
                                    WHERE   client_loan_agreement.actived = 1 AND client_loan_agreement.client_id = '$sub'
                            	    UNION ALL
                            		SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 client_loan_schedule.schedule_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 DATE_FORMAT(client_loan_schedule.schedule_date, '%d/%m/%Y') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT(ROUND(client_loan_schedule.percent,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(client_loan_schedule.percent/(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' USD')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(client_loan_schedule.percent*(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' GEL')
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            		LEFT JOIN money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		GROUP BY client_loan_schedule.id
                            		UNION ALL
                            		SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 client_loan_schedule.schedule_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(client_loan_schedule_deal.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule_deal.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos*client_loan_schedule_deal.cource,2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule_deal.unda_daericxos>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		GROUP BY client_loan_schedule.id
                                    UNION ALL
                                    SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 deals_daricxva.pay_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(deals_daricxva.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_daricxva.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount*client_loan_schedule_deal.cource,2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN     client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                                    JOIN     deals_daricxva ON deals_daricxva.deals_id = client_loan_schedule_deal.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND deals_daricxva.actived = 1 AND client_loan_schedule_deal.actived = 1 AND deals_daricxva.amount>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		UNION ALL
                            		SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 DATE(deals_detail.penalty_date) AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',DATE_FORMAT(deals_detail.penalty_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_detail.penalty_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div  title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',deals_detail.penalty,if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',ROUND(deals_detail.penalty/(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',ROUND(deals_detail.penalty*(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		JOIN deals_detail ON deals_detail.deals_id = client_loan_schedule_deal.id
                                    WHERE    client_loan_agreement.client_id = '$sub' AND deals_detail.penalty > 0  AND client_loan_schedule_deal.unda_daericxos>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE() AND deals_detail.actived = 1
                            		GROUP BY deals_detail.id
                            		UNION ALL
                            		SELECT  client_loan_agreement.client_id,
                            				'' AS `id`,
                            				'' AS number1,
                            				'5' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				'' AS number,
                            				CONCAT('<div title=\"შეთანხმების გადახდა\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y'),'</div>') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                            				'' AS `delta`,
                            				'' AS `delta1`,
                            				'' AS  percent,
                            				'' AS  percent_gel,
                            				CONCAT(money_transactions_detail.pay_amount, ' GEL') AS percent1,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                            				END AS percent_gel1,
                            				CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
                            				END AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                            				'' AS pledge_fee1,
                            				'' as pledge_payed,
                            				'' as pledge_payed1,
                            				'' AS pledge_delta,
                            				'' as other,
                            				'' as other1,
                            				'' as other_delta
                                    FROM   money_transactions
                                    JOIN   		money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.`status` = 13 AND money_transactions_detail.actived = 1 AND (money_transactions_detail.pay_amount > 0 || money_transactions_detail.pay_root>0)
                                    GROUP BY money_transactions.id
                            		UNION ALL
                                    SELECT   client.id,
                            				 '' AS `id`,
                            				 '' AS number1,
                            				 '7' AS sort3,
                            				 DATE(money_transactions.pay_datetime) AS sort,
                            				 '2' AS sort1,
                            				 '' AS number,
                            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                            				 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                            				 '' AS `loan_amount`,
                            				 '' AS `loan_amount_gel`,
                            				 '' AS `delta`,
                            				 '' AS `delta1`,
                            				 '' AS percent,
                            				 '' AS percent_gel,
                            				 '' AS percent1,
                            				 '' AS percent_gel1,
                            				 '' AS pay_root,
                            				 '' AS pay_root_gel,
                            				 '' AS jh,
                            				 '' AS kj,
                            				 '' AS difference,
                            				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL') AS pledge_fee,
                                             CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2), ' USD') AS pledge_fee1,
                                             '' as  pledge_payed,
                                             '' as  pledge_payed1,
                            				 '' AS  pledge_delta,
                                             '' as  other,
                            				 '' as  other1,
                            				 '' as  other_delta
                     				FROM     money_transactions
                    				JOIN     client ON client.id = money_transactions.client_id
                    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                    				WHERE    client_id = '$sub' AND money_transactions.type_id = 2 AND money_transactions_detail.`status` = 7 
                                    AND      money_transactions_detail.actived = 1 
                                    AND      money_transactions.actived = 1
                                    GROUP BY money_transactions.id
                                    UNION ALL
                                    SELECT   client.id,
                            				 '' AS `id`,
                            				 '' AS number1,
                            				 '8' AS sort3,
                            				 DATE(money_transactions.pay_datetime) AS sort,
                            				 '2' AS sort1,
                            				 '' AS number,
                            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                            				 money_transactions_detail.course AS `exchange`,
                            				 '' AS `loan_amount`,
                            				 '' AS `loan_amount_gel`,
                            				 '' AS `delta`,
                            				 '' AS `delta1`,
                            				 '' AS percent,
                            				 '' AS percent_gel,
                            				 '' AS percent1,
                            				 '' AS percent_gel1,
                            				 '' AS pay_root,
                            				 '' AS pay_root_gel,
                            				 '' AS jh,
                            				 '' AS kj,
                            				 '' AS difference,
                            				 '' AS pledge_fee,
                                             '' AS pledge_fee1,
                                             CASE 
                                    			WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions_detail.course),2), ' GEL')
                                                WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL')
                                             END as  pledge_payed,
                                             CASE 
                                    			WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' USD')
                                                WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2), ' USD')
                                             END as  pledge_payed1,
                            				 '' AS  pledge_delta,
                                             '' as  other,
                            				 '' as  other1,
                            				 '' as  other_delta
                     				FROM     money_transactions
                    				JOIN     client ON client.id = money_transactions.client_id
                    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                    				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 8 
                                    AND      money_transactions_detail.actived = 1 
                                    AND      money_transactions.actived = 1
                                    GROUP BY money_transactions.id
                                    UNION ALL
                                    SELECT   client.id,
                            				 '' AS `id`,
                            				 '3' AS number1,
                            				 '8' AS sort3,
                            				 DATE(money_transactions.pay_datetime) AS sort,
                            				 '2' AS sort1,
                            				 '' AS number,
                            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                            				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                            				 '' AS `loan_amount`,
                            				 '' AS `loan_amount_gel`,
                            				 '' AS `delta`,
                            				 '' AS `delta1`,
                            				 '' AS percent,
                            				 '' AS percent_gel,
                            				 '' AS percent1,
                            				 '' AS percent_gel1,
                            				 '' AS pay_root,
                            				 '' AS pay_root_gel,
                            				 '' AS jh,
                            				 '' AS kj,
                            				 '' AS difference,
                            				 '' AS pledge_fee,
                                             '' AS pledge_fee1,
                                             CASE 
                                    			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions.course),2), ' GEL')
                                                WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL')
                                             END as  pledge_payed,
                                             CASE 
                                    			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' USD')
                                                WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions.course),2), ' USD')
                                             END as  pledge_payed1,
                            				 '' AS  pledge_delta,
                                             '' as  other,
                            				 '' as  other1,
                            				 '' as  other_delta
                     				FROM     money_transactions
                    				JOIN     client ON client.id = money_transactions.client_id
                    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                    				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 9 
                                    AND      money_transactions_detail.actived = 1 
                                    AND      money_transactions.actived = 1
                                    GROUP BY money_transactions.id
                                    UNION ALL
                                    SELECT   client.id,
                            				 '' AS `id`,
                            				 '' AS number1,
                            				 '10' AS sort3,
                            				 DATE(money_transactions.pay_datetime) AS sort,
                            				 '2' AS sort1,
                            				 '' AS number,
                            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                            				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                            				 '' AS `loan_amount`,
                            				 '' AS `loan_amount_gel`,
                            				 '' AS `delta`,
                            				 '' AS `delta1`,
                            				 '' AS percent,
                            				 '' AS percent_gel,
                            				 '' AS percent1,
                            				 '' AS percent_gel1,
                            				 '' AS pay_root,
                            				 '' AS pay_root_gel,
                            				 '' AS jh,
                            				 '' AS kj,
                            				 '' AS difference,
                            				 '' AS pledge_fee,
                                             '' AS pledge_fee1,
                                             '' as  pledge_payed,
                                             '' as  pledge_payed1,
                            				 '' AS  pledge_delta,
                                             CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as other,
                            				 '' as  other1,
                            				 '' as  other_delta
                     				FROM     money_transactions
                    				JOIN     client ON client.id = money_transactions.client_id
                    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                    				WHERE    client_id = '$sub' AND money_transactions.type_id = 3 AND money_transactions_detail.`status` = 10 
                                    AND      money_transactions_detail.actived = 1 
                                    AND      money_transactions.actived = 1
                                    GROUP BY money_transactions.id
                                    UNION ALL
                                    SELECT   client.id,
                            				 '' AS `id`,
                            				 '' AS number1,
                            				 '11' AS sort3,
                            				 DATE(money_transactions.pay_datetime) AS sort,
                            				 '2' AS sort1,
                            				 '' AS number,
                            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                            				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                            				 '' AS `loan_amount`,
                            				 '' AS `loan_amount_gel`,
                            				 '' AS `delta`,
                            				 '' AS `delta1`,
                            				 '' AS percent,
                            				 '' AS percent_gel,
                            				 '' AS percent1,
                            				 '' AS percent_gel1,
                            				 '' AS pay_root,
                            				 '' AS pay_root_gel,
                            				 '' AS jh,
                            				 '' AS kj,
                            				 '' AS difference,
                            				 '' AS pledge_fee,
                                             '' AS pledge_fee1,
                                             '' as  pledge_payed,
                                             '' as  pledge_payed1,
                            				 '' AS  pledge_delta,
                                             '' as  other,
                            				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as  other1,
                            				 '' as  other_delta
                     				FROM     money_transactions
                    				JOIN     client ON client.id = money_transactions.client_id
                    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                    				WHERE    client_id = '$sub' AND money_transactions_detail.type_id = 3 AND money_transactions_detail.`status` = 11
                                    AND      money_transactions_detail.actived = 1 
                                    AND      money_transactions.actived = 1
                                    GROUP BY money_transactions.id
                                    UNION ALL
                                    SELECT  client_loan_agreement.client_id,
                            				client_loan_schedule.id AS `id`,
                                            '' AS number1,
                                            '5' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				client_loan_schedule.number,
                            				DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                                            '' AS `delta`,
                                            '' AS `delta1`,
                            				'' AS percent,
                            				'' AS percent_gel,
                            				CONCAT(ROUND(SUM(money_transactions_detail.pay_percent+money_transactions_detail.pay_amount)-
                            	            IFNULL((SELECT CASE
                                                              WHEN cl_agr.loan_currency_id = 1 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                                              WHEN cl_agr.loan_currency_id = 2 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                                           END AS jigari
                                                    FROM   money_transactions_detail AS mon_tr_det
                                                    JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
	                                                JOIN   client_loan_agreement AS cl_agr ON mon_tr.agreement_id = cl_agr.id
                                                    WHERE  mon_tr_det.actived = 1 
                                                    AND    mon_tr_det.`status` = 3
                                                    AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                                    AND    mon_tr.client_id = money_transactions.client_id),0),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent1,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount) -
                                    	            IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                                             FROM   money_transactions_detail AS mon_tr_det
                                                             JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                                             WHERE  mon_tr_det.actived = 1 
                                                             AND    mon_tr_det.`status` = 3
                                                             AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = mon_tr.id
                                                             AND    mon_tr.client_id = money_transactions.client_id),0))/money_transactions_detail.course,2), ' USD')
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                                    IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                                            FROM   money_transactions_detail AS mon_tr_det
                                                            JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                                            WHERE  mon_tr_det.actived = 1 
                                                            AND    mon_tr_det.`status` = 3
                                                            AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                                            AND    mon_tr.client_id = money_transactions.client_id),0))*money_transactions_detail.course,2), ' GEL')
                            				END AS percent_gel1,
                            				CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
                            				END AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                                    FROM    money_transactions
                                    JOIN    money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                    JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE   client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 
                                    AND     money_transactions_detail.status IN (1,2,5,6) AND (money_transactions_detail.pay_percent != '0.00' OR money_transactions_detail.pay_root!='0.00' OR money_transactions_detail.pay_amount!='0.00')
                                    GROUP BY money_transactions.client_loan_schedule_id,money_transactions_detail.transaction_id
                            		UNION ALL
                            		SELECT  client_loan_agreement.client_id,
                            				'' AS `id`,
                                            '5' AS number1,
                                            '6' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				'' AS number,
                            				DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                                            '' AS `delta`,
                                            '' AS `delta1`,
                            				'' AS percent,
                            				'' AS percent_gel,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 1 THEN CONCAT(money_transactions_detail.pay_amount, ' GEL') 
                            				    WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL')
                            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 2 THEN CONCAT(money_transactions_detail.pay_amount, ' USD') 
                            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' USD')
                            				END AS percent1,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD')
                            				    WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 2 THEN CONCAT(money_transactions_detail.pay_amount, ' USD') 
                            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL')
                            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 1 THEN CONCAT(money_transactions_detail.pay_amount, ' GEL') 
                            				END AS percent_gel1,
                            				'' AS pay_root,
                            				'' AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as pledge_payed,
                                            '' as pledge_payed1,
                                			'' AS pledge_delta,
                                            '' as other,
                                			'' as other1,
                                			'' as other_delta
                                    FROM   money_transactions
                                    JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.`status` = 3 AND money_transactions_detail.actived = 1 AND money_transactions_detail.pay_amount > 0
                                    UNION ALL
                                    SELECT  client_loan_agreement.client_id,
                            				client_loan_schedule.id AS `id`,
                            				'8' AS number1,
                            				'3' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				client_loan_schedule.number,
                            				DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                            				'' AS `delta`,
                            				'' AS `delta1`,
                            				CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
                            				CASE 
                                				WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                                				WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                            				END AS percent_gel,
                            				'' AS percent1,
                            				'' AS percent_gel1,
                            				'' AS pay_root,
                            				'' AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                                    FROM   money_transactions
                                    JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                    JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 5 AND money_transactions_detail.actived = 1 AND money_transactions_detail.pay_amount > 0
                    				UNION ALL
                                    SELECT  client_loan_agreement.client_id,
                            				client_loan_schedule.id AS `id`,
                            				'9' AS number1,
                            				'4' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				client_loan_schedule.number,
                            				DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                            				'' AS `delta`,
                            				'' AS `delta1`,
                            				CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
                            				CASE 
                                				WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                                				WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                            				END AS percent_gel,
                            				'' AS percent1,
                            				'' AS percent_gel1,
                            				'' AS pay_root,
                            				'' AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                                    FROM   money_transactions
                                    JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                    JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 6 AND money_transactions_detail.actived = 1 AND money_transactions_detail.pay_amount > 0
                    				UNION ALL
                    				SELECT  client_loan_agreement.client_id,
                            				client_loan_schedule.id AS `id`,
                                            '7' AS number1,
                                            '2' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				client_loan_schedule.number,
                            				DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				DATEDIFF(money_transactions_detail.pay_datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
                                            '' AS `delta`,
                                            '' AS `delta1`,
                            				CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
                            				CASE 
                            				WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                            				WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                            				END AS percent_gel,
                            				'' AS percent1,
                            				'' AS percent_gel1,
                            				'' AS pay_root,
                            				'' AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                                    FROM   money_transactions
                                    JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                                    JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.actived=1 AND money_transactions_detail.`status` = 2";
    	        }else{
    	            $qvr.=" SELECT  client_loan_agreement.client_id,
                    			    client_loan_agreement.id AS `id`,
                                    '' AS number1,
                                    '0' AS sort3,
                    			    DATE(client_loan_agreement.datetime) AS sort,
                    			    '1' AS sort1,
                    			    '' AS number,
                    			    '01/06/2017' AS `date`,
                    				client_loan_agreement.exchange_rate AS `exchange`,
                    			    '' AS `loan_amount`,
                    				''AS `loan_amount_gel`,
                                    CONCAT(ROUND(client_loan_schedule.remaining_root,2), ' GEL') AS delta,
                                    CONCAT( CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.remaining_root / client_loan_agreement.exchange_rate,2)
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate,2)
                            			    END, ' USD') AS delta1,
                        			'' AS percent,
                        			'' AS percent_gel,
                        			'' AS percent1,
                        			'' AS percent_gel1,
                        			'' AS pay_root,
                        			'' AS pay_root_gel,
                        			'' AS jh,
                        			'' AS kj,
                        			'' AS difference,
                        			'' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM    client_loan_agreement
                            JOIN    client_loan_schedule ON client_loan_agreement.old_schedule_id = client_loan_schedule.id
                            WHERE   client_loan_agreement.actived = 1 AND client_loan_agreement.client_id = '$sub'
                            UNION ALL
                            SELECT client_loan_agreement.client_id,
        							client_loan_schedule.id AS `id`,
                                    '' AS number1,
                                    '1' AS sort3,
        							client_loan_schedule.schedule_date AS sort,
        							'2' AS sort1,
        							 client_loan_schedule.number,
        							 DATE_FORMAT(client_loan_schedule.schedule_date, '%d/%m/%Y') AS `date`,
        							 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1) AS `exchange`,
        							 '' AS `loan_amount`,
        							 '' AS `loan_amount_gel`,
                                     '' AS `delta`,
                                     '' AS `delta1`,
        							 CONCAT(ROUND(client_loan_schedule.percent,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
        							 CASE 
    									WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(client_loan_schedule.percent/(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' USD')
    									WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(client_loan_schedule.percent*(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' GEL')
        							 END AS percent_gel,
                                     
        							 '' AS percent1,
        							 '' AS percent_gel1,
        							 '' AS pay_root,
        							 '' AS pay_root_gel,
                                     '' AS jh,
                                     '' AS kj,
                                     '' AS difference,
                                     '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                			FROM     client_loan_schedule
                			JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                			LEFT JOIN     money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                			WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                			GROUP BY client_loan_schedule.id
                			UNION ALL
                			SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 client_loan_schedule.schedule_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(client_loan_schedule_deal.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule_deal.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos*client_loan_schedule_deal.cource,2), ' GEL', '</div>')
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule_deal.unda_daericxos > 0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		GROUP BY client_loan_schedule.id
                                    UNION ALL
                                    SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 deals_daricxva.pay_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(deals_daricxva.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_daricxva.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount*client_loan_schedule_deal.cource,2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN     client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                                    JOIN     deals_daricxva ON deals_daricxva.deals_id = client_loan_schedule_deal.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND deals_daricxva.actived = 1 AND client_loan_schedule_deal.actived = 1 AND deals_daricxva.amount>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		UNION ALL
                            		SELECT  client_loan_agreement.client_id,
                            				'' AS `id`,
                            				'' AS number1,
                            				'5' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				'' AS number,
                            				CONCAT('<div title=\"შეთანხმების გადახდა\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y'),'</div>') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                            				'' AS `delta`,
                            				'' AS `delta1`,
                            				'' AS  percent,
                            				'' AS  percent_gel,
                            				CONCAT(money_transactions_detail.pay_amount, ' GEL') AS percent1,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                            				END AS percent_gel1,
                            				CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
                        					CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
                        					END AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                            				'' AS pledge_fee1,
                            				'' as pledge_payed,
                            				'' as pledge_payed1,
                            				'' AS pledge_delta,
                            				'' as other,
                            				'' as other1,
                            				'' as other_delta
                                    FROM   money_transactions
                                    JOIN   		money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.`status` = 13 AND money_transactions_detail.actived = 1 AND (money_transactions_detail.pay_amount > 0 || money_transactions_detail.pay_root>0)
                                    GROUP BY money_transactions.id
                            		UNION ALL
                            		SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 DATE(deals_detail.penalty_date) AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',DATE_FORMAT(deals_detail.penalty_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_detail.penalty_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div  title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',deals_detail.penalty,if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',ROUND(deals_detail.penalty/(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',ROUND(deals_detail.penalty*(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		JOIN deals_detail ON deals_detail.deals_id = client_loan_schedule_deal.id
                                    WHERE    client_loan_agreement.client_id = '$sub' AND deals_detail.penalty > 0  AND client_loan_schedule_deal.unda_daericxos>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE() AND deals_detail.actived = 1
                            		GROUP BY deals_detail.id
                            		UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '7' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL') AS pledge_fee,
                                     CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2), ' USD') AS pledge_fee1,
                                     '' as  pledge_payed,
                                     '' as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions.type_id = 2 AND money_transactions_detail.`status` = 7 
                            AND      money_transactions_detail.actived = 1 
                            AND    money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '8' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				 money_transactions_detail.course AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     CASE 
                            			WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions_detail.course),2), ' GEL')
                                        WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL')
                                     END as  pledge_payed,
                                     CASE 
                            			WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' USD')
                                        WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2), ' USD')
                                     END as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 8 
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                           UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '3' AS number1,
                    				 '8' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     CASE 
                            			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions.course),2), 'GEL')
                                        WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), 'GEL')
                                     END as pledge_payed,
                                     CASE 
                            			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2),' USD')
                                        WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions.course),2), ' USD')
                                     END as pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 9 
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '10' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     '' as  pledge_payed,
                                     '' as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions.type_id = 3 AND money_transactions_detail.`status` = 10 
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '11' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     '' as  pledge_payed,
                                     '' as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions_detail.type_id = 3 AND money_transactions_detail.`status` = 11
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                			SELECT  client_loan_agreement.client_id,
                					client_loan_schedule.id AS `id`,
                                    '' AS number1,
                                    '5' AS sort3,
                					DATE(money_transactions_detail.pay_datetime) AS sort,
                					'2' AS sort1,
                					client_loan_schedule.number,
                					DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                					money_transactions_detail.course AS `exchange`,
                					'' AS `loan_amount`,
                					'' AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
                					'' AS percent,
                					'' AS percent_gel,
                					CONCAT(ROUND(SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                    IFNULL((SELECT CASE
                                                      WHEN cl_agr.loan_currency_id = 1 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                                      WHEN cl_agr.loan_currency_id = 2 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                                   END AS jigari
                                            FROM   money_transactions_detail AS mon_tr_det
                                            JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                            JOIN   client_loan_agreement AS cl_agr ON mon_tr.agreement_id = cl_agr.id
                                            WHERE  mon_tr_det.actived = 1 
                                            AND    mon_tr_det.`status` = 3
                                            AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                            AND    mon_tr.client_id = money_transactions.client_id),0),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent1,
                					CASE 
                    					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                            IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                                     FROM   money_transactions_detail AS mon_tr_det
                                                     JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                                     WHERE  mon_tr_det.actived = 1 
                                                     AND    mon_tr_det.`status` = 3
                                                     AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                                     AND    mon_tr.client_id = money_transactions.client_id),2))/money_transactions_detail.course,2), ' USD')
                    					 WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                           IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                                    FROM   money_transactions_detail AS mon_tr_det
                                                    JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                                    WHERE  mon_tr_det.actived = 1 
                                                    AND    mon_tr_det.`status` = 3
                                                    AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                                    AND    mon_tr.client_id = money_transactions.client_id),0))*money_transactions_detail.course,2), ' GEL')
                					END AS percent_gel1,
                					CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
                					CASE 
                    					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
                    					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
                					END AS pay_root_gel,
                					'' AS jh,
                				    '' AS kj,
                				    '' AS difference,
                				    '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM     money_transactions
                            JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id 
                            JOIN     client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                            JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 
                            AND      money_transactions_detail.`status` IN (1,2,5,6) AND (money_transactions_detail.pay_percent != '0.00' OR money_transactions_detail.pay_root!='0.00' OR money_transactions_detail.pay_amount!='0.00')
                            GROUP BY money_transactions.client_loan_schedule_id, money_transactions_detail.transaction_id
                			UNION ALL
                            SELECT  client_loan_agreement.client_id,
                    				client_loan_schedule.id AS `id`,
                    				'8' AS number1,
                    				'2' AS sort3,
                    				DATE(money_transactions_detail.pay_datetime) AS sort,
                    				'2' AS sort1,
                    				client_loan_schedule.number,
                    				DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                    				money_transactions_detail.course AS `exchange`,
                    				'' AS `loan_amount`,
                    				'' AS `loan_amount_gel`,
                    				'' AS `delta`,
                    				'' AS `delta1`,
                    				CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
                    				CASE 
                        				WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                        				WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                    				END AS percent_gel,
                    				'' AS percent1,
                    				'' AS percent_gel1,
                    				'' AS pay_root,
                    				'' AS pay_root_gel,
                    				'' AS jh,
                    				'' AS kj,
                    				'' AS difference,
                    				'' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM   money_transactions
                            JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                            JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 5 AND money_transactions_detail.actived = 1 AND money_transactions_detail.pay_amount > 0
            				UNION ALL
                            SELECT  client_loan_agreement.client_id,
                    				client_loan_schedule.id AS `id`,
                    				'9' AS number1,
                    				'3' AS sort3,
                    				DATE(money_transactions_detail.pay_datetime) AS sort,
                    				'2' AS sort1,
                    				client_loan_schedule.number,
                    				DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
                    				money_transactions_detail.course AS `exchange`,
                    				'' AS `loan_amount`,
                    				'' AS `loan_amount_gel`,
                    				'' AS `delta`,
                    				'' AS `delta1`,
                    				CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
                    				CASE 
                        				WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                        				WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                    				END AS percent_gel,
                    				'' AS percent1,
                    				'' AS percent_gel1,
                    				'' AS pay_root,
                    				'' AS pay_root_gel,
                    				'' AS jh,
                    				'' AS kj,
                    				'' AS difference,
                    				'' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM   money_transactions
                            JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                            JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                            JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                            WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 6 AND money_transactions_detail.actived = 1 AND money_transactions_detail.pay_amount > 0
            				UNION ALL
                			SELECT  client_loan_agreement.client_id,
        							'' AS `id`,
                                    '5' AS number1,
                                    '6' AS sort3,
        							DATE(money_transactions_detail.pay_datetime) AS sort,
        							'2' AS sort1,
        							'' AS number,
        							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
        							money_transactions_detail.course AS `exchange`,
        							'' AS `loan_amount`,
        							'' AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
        							'' AS percent,
        							'' AS percent_gel,
        							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent1,
        							CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
        							END AS percent_gel1,
        							'' AS pay_root,
        							'' AS pay_root_gel,
                                    '' AS jh,
                                    '' AS kj,
                                    '' AS difference,
                                    '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                			FROM   money_transactions
							JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                			LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                			WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.actived=1 AND money_transactions_detail.`status` = 3
                			UNION ALL
                            SELECT  difference_cource.client_id,
                    				client_loan_schedule.id AS `id`,
                                    '' AS number1,
                                   '5' AS sort3,
                    				DATE(difference_cource.datetime) AS sort,
                    				'2' AS sort1,
                    				client_loan_schedule.number,
                    				DATE_FORMAT(difference_cource.datetime, '%d/%m/%Y') AS `date`,
                    				difference_cource.end_cource AS `exchange`,
                    				'' AS `loan_amount`,
                    				'' AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
                    				'' AS percent,
                    				'' AS percent_gel,
                    				'' AS percent1,
                    				'' AS percent_gel1,
                    				'' AS pay_root,
                    				'' AS pay_root_gel,
                    				'' AS jh,
                    				'' AS kj,
                    				ROUND(difference_cource.difference,2) AS difference,
                    				'' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM    difference_cource
                            JOIN    client_loan_schedule ON client_loan_schedule.id = difference_cource.cliet_loan_schedule_id
                            WHERE   difference_cource.client_id = '$sub' AND client_loan_schedule.actived = 1
                            UNION ALL
                			SELECT  client_loan_agreement.client_id,
        							client_loan_schedule.id AS `id`,
                                    '7' AS number1,
                                    '2' AS sort3,
        							client_loan_schedule.schedule_date AS sort,
        							'2' AS sort1,
        							client_loan_schedule.number,
        							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
        							money_transactions_detail.course AS `exchange`,
        							'' AS `loan_amount`,
        							DATEDIFF(money_transactions_detail.pay_datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
        							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
        							CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
        							END AS percent_gel,
        							'' AS percent1,
        							'' AS percent_gel1,
        							'' AS pay_root,
        							'' AS pay_root_gel,
                                    '' AS jh,
                                    '' AS kj,
                                    '' AS difference,
                                    '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                			FROM    money_transactions
                            JOIN    money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                			JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                			JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                			WHERE   client_loan_agreement.client_id = '$sub' AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 2
                            UNION ALL";
    	        }
    	        
    	    }
    	    
    	    $rResult = mysql_query("SELECT  letter.client_id,
                                	        letter.number,
                                	        letter.date,
                                	        ROUND(letter.exchange,4),
                                	        letter.loan_amount,
                                	        letter.loan_amount_gel,
                                	        letter.delta AS delta,
                                	        letter.delta1 AS delta1,
                                	        letter.percent,
                                	        letter.percent_gel,
                                	        letter.percent1,
                                	        letter.percent_gel1,
                                	        letter.pay_root,
                                	        letter.pay_root_gel,
                                	        '' as `g`,
                                	        '' as `gd`,
                                	        letter.difference AS difference,
                                	        letter.pledge_fee,
                                	        letter.pledge_fee1,
                                	        letter.pledge_payed,
                                	        letter.pledge_payed1,
                                	        '' as  pledge_delta,
                                	        '' as  pledge_delta1,
                                	        letter.other,
                                	        letter.other1,
                                	        '' as  other_delta,
                                	        letter.sort1,
                                	        letter.loan_amount_gel,
                                	        letter.number1
                        	         FROM($qvr)AS letter
                        	         ORDER BY letter.client_id, letter.sort1,  letter.sort, letter.sort3 ASC ");
        }else {
            while ($check == 1) {
                $i++;
                $sub_client_id = mysql_fetch_array(mysql_query("SELECT IFNULL(sub_client,0) AS sub_client,
                                                                       id
                                                                FROM   client
                                                                WHERE  client.id = $cl_id"));
                $cl_id = $sub_client_id[sub_client];
                $sub   = $sub_client_id[id];
                 
                if ($sub_client_id[sub_client] == 0){
                    $check = 2;
                	    
                    $qvr .= " SELECT    client_loan_agreement.client_id,
                                        client_loan_agreement.id AS `id`,
                                        '' AS number1,
                                        '0' AS sort3,
                                        client_loan_agreement.datetime AS sort,
                                        '0' AS sort1,
                                        '' AS number,
                                        DATE_FORMAT(client_loan_agreement.datetime, '%d/%m/%Y') AS `date`,
                                        client_loan_agreement.exchange_rate AS `exchange`,
                                        CONCAT(client_loan_agreement.loan_amount,if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS `loan_amount`,
                                        CASE
                                        WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2),' USD')
                                        WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2),' GEL')
                                        END AS `loan_amount_gel`,
                                        '' AS `delta`,
                                        '' AS `delta1`,
                                        '' AS percent,
                                        '' AS percent_gel,
                                        '' AS percent1,
                                        '' AS percent_gel1,
                                        '' AS pay_root,
                                        '' AS pay_root_gel,
                                        '' AS jh,
                                        '' AS kj,
                                        '' AS difference,
                                        '' AS pledge_fee,
                                        '' AS pledge_fee1,
                                        '' as pledge_payed,
                                        '' as pledge_payed1,
                                        '' AS pledge_delta,
                                        '' as other,
                                        '' as other1,
                                        '' as other_delta
                                FROM    client_loan_agreement
                                WHERE   client_loan_agreement.client_id = '$sub'
                    UNION ALL
                    SELECT  client_loan_agreement.client_id,
            			    client_loan_agreement.id AS `id`,
                            '' AS number1,
                            '0' AS sort3,
            			    client_loan_agreement.datetime AS sort,
            			    '1' AS sort1,
            			    '' AS number,
            			    '01/07/2017' AS `date`,
            				client_loan_agreement.exchange_rate AS `exchange`,
            			    '' AS `loan_amount`,
            				''AS `loan_amount_gel`,
                            CONCAT(ROUND(client_loan_schedule.remaining_root,2), ' USD') AS delta,
                            CONCAT( CASE 
                    					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.remaining_root / client_loan_agreement.exchange_rate,2)
                    					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate,2)
                    			    END, ' GEL') AS delta1,
                			'' AS percent,
                			'' AS percent_gel,
                			'' AS percent1,
                			'' AS percent_gel1,
                			'' AS pay_root,
                			'' AS pay_root_gel,
                			'' AS jh,
                			'' AS kj,
                			'' AS difference,
                			'' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
                    FROM    client_loan_agreement
                    JOIN    client_loan_schedule ON client_loan_agreement.old_schedule_id = client_loan_schedule.id
                    WHERE   client_loan_agreement.actived = 1 AND client_loan_agreement.client_id = '$sub'
                    UNION ALL
            		SELECT   client_loan_agreement.client_id,
    						 client_loan_schedule.id AS `id`,
                             '' AS number1,
                             '1' AS sort3,
    						 client_loan_schedule.schedule_date AS sort,
    						 '2' AS sort1,
    						 client_loan_schedule.number,
    						 DATE_FORMAT(client_loan_schedule.schedule_date, '%d/%m/%Y') AS `date`,
    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1) AS `exchange`,
    						 '' AS `loan_amount`,
    						 '' AS `loan_amount_gel`,
                             '' AS `delta`,
                             '' AS `delta1`,
    						 CONCAT(ROUND(client_loan_schedule.percent,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
    						 CASE 
    							 WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(client_loan_schedule.percent/(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' USD')
    							 WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(client_loan_schedule.percent*(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' GEL')
    						 END AS percent_gel,
    						 '' AS percent1,
    						 '' AS percent_gel1,
    						 '' AS pay_root,
    						 '' AS pay_root_gel,
                             '' AS jh,
                             '' AS kj,
                             '' AS difference,
                             '' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
            		FROM     client_loan_schedule
            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
            		LEFT JOIN money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
            		WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
            		GROUP BY client_loan_schedule.id
            		UNION ALL
            		SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 client_loan_schedule.schedule_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(client_loan_schedule_deal.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule_deal.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos*client_loan_schedule_deal.cource,2), ' GEL', '</div>')
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule_deal.unda_daericxos>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		GROUP BY client_loan_schedule.id
                            		UNION ALL
                                    SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 deals_daricxva.pay_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(deals_daricxva.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_daricxva.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount*client_loan_schedule_deal.cource,2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN     client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                                    JOIN     deals_daricxva ON deals_daricxva.deals_id = client_loan_schedule_deal.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND deals_daricxva.actived = 1 AND client_loan_schedule_deal.actived = 1 AND deals_daricxva.amount>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		UNION ALL
                            		SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 DATE(deals_detail.penalty_date) AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',DATE_FORMAT(deals_detail.penalty_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_detail.penalty_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div  title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',deals_detail.penalty,if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000\">',ROUND(deals_detail.penalty/(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000\">',ROUND(deals_detail.penalty*(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		JOIN deals_detail ON deals_detail.deals_id = client_loan_schedule_deal.id
                                    WHERE    client_loan_agreement.client_id = '$sub' AND deals_detail.penalty > 0 AND client_loan_schedule_deal.unda_daericxos>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE() AND deals_detail.actived = 1
                            		GROUP BY deals_detail.id
                            		UNION ALL
                            		SELECT  client_loan_agreement.client_id,
                            				'' AS `id`,
                            				'' AS number1,
                            				'5' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				'' AS number,
                            				CONCAT('<div title=\"შეთანხმების გადახდა\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y'),'</div>') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                            				'' AS `delta`,
                            				'' AS `delta1`,
                            				'' AS  percent,
                            				'' AS  percent_gel,
                            				CONCAT(money_transactions_detail.pay_amount, ' USD') AS percent1,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                            				END AS percent_gel1,
                            				CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
                    						CASE 
                    							WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
                    							WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
                    						END AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                            				'' AS pledge_fee1,
                            				'' as pledge_payed,
                            				'' as pledge_payed1,
                            				'' AS pledge_delta,
                            				'' as other,
                            				'' as other1,
                            				'' as other_delta
                                    FROM   money_transactions
                                    JOIN   		money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.`status` = 13 AND money_transactions_detail.actived = 1 AND (money_transactions_detail.pay_amount > 0 || money_transactions_detail.pay_root>0)
                                    GROUP BY money_transactions.id
                            		UNION ALL
                    SELECT   client.id,
            				 '' AS `id`,
            				 '' AS number1,
            				 '7' AS sort3,
            				 DATE(money_transactions.pay_datetime) AS sort,
            				 '2' AS sort1,
            				 '' AS number,
            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
            				 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
            				 '' AS `loan_amount`,
            				 '' AS `loan_amount_gel`,
            				 '' AS `delta`,
            				 '' AS `delta1`,
            				 '' AS percent,
            				 '' AS percent_gel,
            				 '' AS percent1,
            				 '' AS percent_gel1,
            				 '' AS pay_root,
            				 '' AS pay_root_gel,
            				 '' AS jh,
            				 '' AS kj,
            				 '' AS difference,
            				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2),' USD') AS pledge_fee,
                             CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL') AS pledge_fee1,
                             '' as  pledge_payed,
                             '' as  pledge_payed1,
            				 '' AS  pledge_delta,
                             '' as  other,
            				 '' as  other1,
            				 '' as  other_delta
     				FROM     money_transactions
    				JOIN     client ON client.id = money_transactions.client_id
    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
    				WHERE    client_id = '$sub' AND money_transactions.type_id = 2 AND money_transactions_detail.`status` = 7 
                    AND      money_transactions_detail.actived = 1 
                    AND    money_transactions.actived = 1
                    GROUP BY money_transactions.id
                    UNION ALL
                    SELECT   client.id,
            				 '' AS `id`,
            				 '' AS number1,
            				 '8' AS sort3,
            				 DATE(money_transactions.pay_datetime) AS sort,
            				 '2' AS sort1,
            				 '' AS number,
            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
            				 money_transactions_detail.course AS `exchange`,
            				 '' AS `loan_amount`,
            				 '' AS `loan_amount_gel`,
            				 '' AS `delta`,
            				 '' AS `delta1`,
            				 '' AS percent,
            				 '' AS percent_gel,
            				 '' AS percent1,
            				 '' AS percent_gel1,
            				 '' AS pay_root,
            				 '' AS pay_root_gel,
            				 '' AS jh,
            				 '' AS kj,
            				 '' AS difference,
            				 '' AS pledge_fee,
                             '' AS pledge_fee1,
                             CASE
                                WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' USD')
                                WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2), ' USD')
                    		 END as  pledge_payed,
                             CASE 
                    			WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions_detail.course),2), ' GEL')
                                WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL')
                             END as  pledge_payed1,
            				 '' AS  pledge_delta,
                             '' as  other,
            				 '' as  other1,
            				 '' as  other_delta
     				FROM     money_transactions
    				JOIN     client ON client.id = money_transactions.client_id
    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
    				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 8 
                    AND      money_transactions_detail.actived = 1 
                    AND      money_transactions.actived = 1
                    GROUP BY money_transactions.id
                    UNION ALL
                    SELECT   client.id,
            				 '' AS `id`,
            				 '3' AS number1,
            				 '8' AS sort3,
            				 DATE(money_transactions.pay_datetime) AS sort,
            				 '2' AS sort1,
            				 '' AS number,
            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
            				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
            				 '' AS `loan_amount`,
            				 '' AS `loan_amount_gel`,
            				 '' AS `delta`,
            				 '' AS `delta1`,
            				 '' AS percent,
            				 '' AS percent_gel,
            				 '' AS percent1,
            				 '' AS percent_gel1,
            				 '' AS pay_root,
            				 '' AS pay_root_gel,
            				 '' AS jh,
            				 '' AS kj,
            				 '' AS difference,
            				 '' AS pledge_fee,
                             '' AS pledge_fee1,
                             CASE 
                    			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2),' USD')
                                WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions.course),2), ' USD')
                             END as  pledge_payed,
                             CASE 
                    			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions.course),2),' GEL')
                                WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2),' GEL')
                             END as  pledge_payed1,
            				 '' AS  pledge_delta,
                             '' as  other,
            				 '' as  other1,
            				 '' as  other_delta
     				FROM     money_transactions
    				JOIN     client ON client.id = money_transactions.client_id
    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
    				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 9 
                    AND      money_transactions_detail.actived = 1 
                    AND      money_transactions.actived = 1
                    GROUP BY money_transactions.id
                    UNION ALL
                    SELECT   client.id,
            				 '' AS `id`,
            				 '' AS number1,
            				 '10' AS sort3,
            				 DATE(money_transactions.pay_datetime) AS sort,
            				 '2' AS sort1,
            				 '' AS number,
            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
            				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
            				 '' AS `loan_amount`,
            				 '' AS `loan_amount_gel`,
            				 '' AS `delta`,
            				 '' AS `delta1`,
            				 '' AS percent,
            				 '' AS percent_gel,
            				 '' AS percent1,
            				 '' AS percent_gel1,
            				 '' AS pay_root,
            				 '' AS pay_root_gel,
            				 '' AS jh,
            				 '' AS kj,
            				 '' AS difference,
            				 '' AS pledge_fee,
                             '' AS pledge_fee1,
                             '' as  pledge_payed,
                             '' as  pledge_payed1,
            				 '' AS  pledge_delta,
                             CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as  other,
            				 '' as  other1,
            				 '' as  other_delta
     				FROM     money_transactions
    				JOIN     client ON client.id = money_transactions.client_id
    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
    				WHERE    client_id = '$sub' AND money_transactions.type_id = 3 AND money_transactions_detail.`status` = 10 
                    AND      money_transactions_detail.actived = 1 
                    AND      money_transactions.actived = 1
                    GROUP BY money_transactions.id
                    UNION ALL
                    SELECT   client.id,
            				 '' AS `id`,
            				 '' AS number1,
            				 '11' AS sort3,
            				 DATE(money_transactions.pay_datetime) AS sort,
            				 '2' AS sort1,
            				 '' AS number,
            				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
            				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
            				 '' AS `loan_amount`,
            				 '' AS `loan_amount_gel`,
            				 '' AS `delta`,
            				 '' AS `delta1`,
            				 '' AS percent,
            				 '' AS percent_gel,
            				 '' AS percent1,
            				 '' AS percent_gel1,
            				 '' AS pay_root,
            				 '' AS pay_root_gel,
            				 '' AS jh,
            				 '' AS kj,
            				 '' AS difference,
            				 '' AS pledge_fee,
                             '' AS pledge_fee1,
                             '' as  pledge_payed,
                             '' as  pledge_payed1,
            				 '' AS  pledge_delta,
                             '' as  other,
            				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as  other1,
            				 '' as  other_delta
     				FROM     money_transactions
    				JOIN     client ON client.id = money_transactions.client_id
    				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
    				WHERE    client_id = '$sub' AND money_transactions_detail.type_id = 3 AND money_transactions_detail.`status` = 11
                    AND      money_transactions_detail.actived = 1 
                    AND      money_transactions.actived = 1
                    GROUP BY money_transactions.id
                    UNION ALL
            		SELECT  client_loan_agreement.client_id,
    						client_loan_schedule.id AS `id`,
                            '' AS number1,
                            '5' AS sort3,
    						DATE(money_transactions_detail.pay_datetime) AS sort,
    						'2' AS sort1,
    						client_loan_schedule.number,
    						DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
    						money_transactions_detail.course AS `exchange`,
    						'' AS `loan_amount`,
    						'' AS `loan_amount_gel`,
                            '' AS `delta`,
                            '' AS `delta1`,
    						'' AS percent,
    						'' AS percent_gel,
    						CONCAT(ROUND(SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                            IFNULL((SELECT CASE
                                              WHEN cl_agr.loan_currency_id = 1 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                              WHEN cl_agr.loan_currency_id = 2 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                            END AS jigari
                                    FROM   money_transactions_detail AS mon_tr_det
                                    JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                    JOIN   client_loan_agreement AS cl_agr ON mon_tr.agreement_id = cl_agr.id
                                    WHERE  mon_tr_det.actived = 1 
                                    AND    mon_tr_det.`status` = 3
                                    AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                    AND    mon_tr.client_id = money_transactions.client_id),0),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent1,
    						CASE 
    							WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                    IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                            FROM   money_transactions_detail AS mon_tr_det
                                            JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                            JOIN   client_loan_agreement ON client_loan_agreement.id = mon_tr.agreement_id
                                            WHERE  mon_tr_det.actived = 1 
                                            AND    mon_tr_det.`status` = 3
                                            AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                            AND    mon_tr.client_id = money_transactions.client_id),0))/money_transactions_detail.course,2), ' USD')
    							WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                    IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                            FROM   money_transactions_detail AS mon_tr_det
                                            JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                            WHERE  mon_tr_det.actived = 1 
                                            AND    mon_tr_det.`status` = 3
                                            AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                            AND    mon_tr.client_id = money_transactions.client_id),2))*money_transactions_detail.course,2), ' GEL')
    						END AS percent_gel1,
    						CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
    						CASE 
    							WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
    							WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
    						END AS pay_root_gel,
                            '' AS jh,
                            '' AS kj,
                            '' AS difference,
                            '' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
            		FROM    money_transactions
                    JOIN    money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
            		JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
            		JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
            		WHERE   client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 
            		AND     money_transactions_detail.`status` IN (1,2,5,6) AND (money_transactions_detail.pay_percent != '0.00' OR money_transactions_detail.pay_root!='0.00'  OR money_transactions_detail.pay_amount!='0.00')
            		GROUP BY money_transactions.client_loan_schedule_id, money_transactions_detail.transaction_id
            		UNION ALL
            		SELECT  client_loan_agreement.client_id,
    						'' AS `id`,
                            '5' AS number1,
                            '6' AS sort3,
    						DATE(money_transactions_detail.pay_datetime) AS sort,
                            '2' AS sort1,
    						'' AS number,
    						DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
    						money_transactions_detail.course AS `exchange`,
    						'' AS `loan_amount`,
    						'' AS `loan_amount_gel`,
                            '' AS `delta`,
                            '' AS `delta1`,
    						'' AS percent,
    						'' AS percent_gel,
    						CASE 
            					WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 1 THEN CONCAT(money_transactions_detail.pay_amount, ' GEL') 
            				    WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL')
            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 2 THEN CONCAT(money_transactions_detail.pay_amount, ' USD') 
            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD')
            				END AS percent1,
            				CASE 
            					WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD')
            				    WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 2 THEN CONCAT(money_transactions_detail.pay_amount, ' USD') 
            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL')
            					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 1 THEN CONCAT(money_transactions_detail.pay_amount, ' GEL') 
            				END AS percent_gel1,
            				'' AS pay_root,
    						'' AS pay_root_gel,
                            '' AS jh,
                            '' AS kj,
                            '' AS difference,
                            '' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
    				FROM   money_transactions
                    JOIN money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
    				LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
    				WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.`status` = 3 AND money_transactions_detail.actived = 1
    				UNION ALL
                    SELECT  client_loan_agreement.client_id,
							client_loan_schedule.id AS `id`,
                            '8' AS number1,
                            '3' AS sort3,
							DATE(money_transactions_detail.pay_datetime) AS sort,
							'2' AS sort1,
							client_loan_schedule.number,
							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
							money_transactions_detail.course AS `exchange`,
							'' AS `loan_amount`,
							'' AS `loan_amount_gel`,
                            '' AS `delta`,
                            '' AS `delta1`,
							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
							CASE 
								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
							END AS percent_gel,
							'' AS percent1,
							'' AS percent_gel1,
							'' AS pay_root,
							''AS pay_root_gel,
                            '' AS jh,
                            '' AS kj,
                            '' AS difference,
                            '' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
                    FROM   money_transactions
                    JOIN money_transactions_detail on money_transactions_detail.transaction_id = money_transactions.id
        			JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
        			JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
        			WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 5 AND money_transactions_detail.actived = 1
                    UNION ALL
                    SELECT  client_loan_agreement.client_id,
							client_loan_schedule.id AS `id`,
                            '9' AS number1,
                            '4' AS sort3,
							DATE(money_transactions_detail.pay_datetime) AS sort,
							'2' AS sort1,
							client_loan_schedule.number,
							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
							money_transactions_detail.course AS `exchange`,
							'' AS `loan_amount`,
							'' AS `loan_amount_gel`,
                            '' AS `delta`,
                            '' AS `delta1`,
							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
							CASE 
								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
							END AS percent_gel,
							'' AS percent1,
							'' AS percent_gel1,
							'' AS pay_root,
							''AS pay_root_gel,
                            '' AS jh,
                            '' AS kj,
                            '' AS difference,
                            '' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
                    FROM   money_transactions
                    JOIN money_transactions_detail on money_transactions_detail.transaction_id = money_transactions.id
        			JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
        			JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
        			WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 6 AND money_transactions_detail.actived = 1
                    UNION ALL
                    SELECT  difference_cource.client_id,
            				client_loan_schedule.id AS `id`,
                            '' AS number1,
                            '5' AS sort3,
            				DATE(difference_cource.datetime) AS sort,
            				'2' AS sort1,
            				client_loan_schedule.number,
            				DATE_FORMAT(difference_cource.datetime, '%d/%m/%Y') AS `date`,
            				difference_cource.end_cource AS `exchange`,
            				'' AS `loan_amount`,
            				'' AS `loan_amount_gel`,
                            '' AS `delta`,
                            '' AS `delta1`,
            				'' AS percent,
            				'' AS percent_gel,
            				'' AS percent1,
            				'' AS percent_gel1,
            				'' AS pay_root,
            				'' AS pay_root_gel,
            				'' AS jh,
            				'' AS kj,
            				ROUND(difference_cource.difference,2) AS difference,
            				'' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
                    FROM    difference_cource
                    JOIN    client_loan_schedule ON client_loan_schedule.id = difference_cource.cliet_loan_schedule_id
                    WHERE   difference_cource.client_id = '$sub' AND client_loan_schedule.actived=1 AND client_loan_schedule.actived = 1
                    UNION ALL
    				SELECT  client_loan_agreement.client_id,
							client_loan_schedule.id AS `id`,
                           '7' AS number1,
                            '2' AS sort3,
							DATE(money_transactions_detail.pay_datetime) AS sort,
							'2' AS sort1,
							client_loan_schedule.number,
							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
							money_transactions_detail.course AS `exchange`,
							'' AS `loan_amount`,
							DATEDIFF(money_transactions_detail.pay_datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
                            '' AS `delta`,
                            '' AS `delta1`,
							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
							CASE 
								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
							END AS percent_gel,
							'' AS percent1,
							'' AS percent_gel1,
							'' AS pay_root,
							'' AS pay_root_gel,
                            '' AS jh,
                            '' AS kj,
                            '' AS difference,
                            '' AS pledge_fee,
                            '' AS pledge_fee1,
                            '' as  pledge_payed,
                            '' as  pledge_payed1,
                			'' AS  pledge_delta,
                            '' as  other,
                			'' as  other1,
                			'' as  other_delta
    				FROM   money_transactions
                    JOIN money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
    				JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
    				JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
    				WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.actived=1 AND money_transactions_detail.`status` = 2";
                }else{
                    $qvr.=" SELECT  client_loan_agreement.client_id,
                        			client_loan_agreement.id AS `id`,
                                   '' AS number1, 
                                    '0' AS sort3,
                        			DATE(client_loan_agreement.datetime) AS sort,
                        			'0' AS sort1,
                				    '' AS number,
                				    DATE_FORMAT(client_loan_agreement.datetime, '%d/%m/%Y') AS `date`,
                					client_loan_agreement.exchange_rate AS `exchange`,
                					CONCAT(client_loan_agreement.loan_amount, if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS `loan_amount`,
                					CASE 
                					   WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND((client_loan_agreement.loan_amount/client_loan_agreement.exchange_rate),2), ' USD')
                					   WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND((client_loan_agreement.loan_amount*client_loan_agreement.exchange_rate),2), ' GEL')
                					END AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
                					'' AS percent,
                					'' AS percent_gel,
                					'' AS percent1,
                					'' AS percent_gel1,
                					'' AS pay_root,
                					'' AS pay_root_gel,
                                    '' AS jh,
                                     '' AS kj,
                                     '' AS difference,
                                     '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                    		FROM    client_loan_agreement
                    		WHERE   client_loan_agreement.client_id = '$sub'
                            UNION ALL
                            SELECT  client_loan_agreement.client_id,
                    			    client_loan_agreement.id AS `id`,
                                    '' AS number1,
                                    '0' AS sort3,
                    			    DATE(client_loan_agreement.datetime) AS sort,
                    			    '1' AS sort1,
                    			    '' AS number,
                    			    '01/06/2017' AS `date`,
                    				client_loan_agreement.exchange_rate AS `exchange`,
                    			    '' AS `loan_amount`,
                    				''AS `loan_amount_gel`,
                                    CONCAT(ROUND(client_loan_schedule.remaining_root,2), ' USD') AS delta,
                                    CONCAT(CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(client_loan_schedule.remaining_root / client_loan_agreement.exchange_rate,2)
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(client_loan_schedule.remaining_root * client_loan_agreement.exchange_rate,2)
                            			    END, ' GEL') AS delta1,
                        			'' AS percent,
                        			'' AS percent_gel,
                        			'' AS percent1,
                        			'' AS percent_gel1,
                        			'' AS pay_root,
                        			'' AS pay_root_gel,
                        			'' AS jh,
                        			'' AS kj,
                        			'' AS difference,
                        			'' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM    client_loan_agreement
                            JOIN    client_loan_schedule ON client_loan_agreement.old_schedule_id = client_loan_schedule.id
                            WHERE   client_loan_agreement.actived = 1 AND client_loan_agreement.client_id = '$sub'
                            UNION ALL
                            SELECT client_loan_agreement.client_id,
        							client_loan_schedule.id AS `id`,
                                    '' AS number1,
                                    '1' AS sort3,
        							DATE(client_loan_schedule.schedule_date) AS sort,
        							'2' AS sort1,
        							 client_loan_schedule.number,
        							 DATE_FORMAT(client_loan_schedule.schedule_date, '%d/%m/%Y') AS `date`,
        							 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1) AS `exchange`,
        							 '' AS `loan_amount`,
        							 '' AS `loan_amount_gel`,
                                     '' AS `delta`,
                                     '' AS `delta1`,
        							 CONCAT(ROUND(client_loan_schedule.percent,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
        							 CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(client_loan_schedule.percent/(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' USD')
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(client_loan_schedule.percent*(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule.schedule_date) LIMIT 1),2), ' GEL')
        							 END AS percent_gel,
        							 '' AS percent1,
        							 '' AS percent_gel1,
        							 '' AS pay_root,
        							 '' AS pay_root_gel,
                                     '' AS jh,
                                     '' AS kj,
                                     '' AS difference,
                                     '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                			FROM     client_loan_schedule
                			JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                			LEFT JOIN     money_transactions ON money_transactions.client_loan_schedule_id = client_loan_schedule.id
                			WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                			GROUP BY client_loan_schedule.id
                			UNION ALL 
                			SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 client_loan_schedule.schedule_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(client_loan_schedule_deal.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(client_loan_schedule_deal.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(client_loan_schedule_deal.unda_daericxos*client_loan_schedule_deal.cource,2), ' GEL', '</div>')
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND client_loan_schedule_deal.unda_daericxos>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		GROUP BY client_loan_schedule.id
                            		UNION ALL
                                    SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 deals_daricxva.pay_date AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(deals_daricxva.pay_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_daricxva.pay_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount,2),if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount/client_loan_schedule_deal.cource,2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"შეთანხმება\" style=\"background: #009688; color: #fff;\">',ROUND(deals_daricxva.amount*client_loan_schedule_deal.cource,2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN     client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                                    JOIN     deals_daricxva ON deals_daricxva.deals_id = client_loan_schedule_deal.id
                            		WHERE    client_loan_agreement.client_id = '$sub' AND deals_daricxva.actived = 1 AND client_loan_schedule_deal.actived = 1 AND deals_daricxva.amount>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE()
                            		UNION ALL
                            		SELECT   client_loan_agreement.client_id,
                    						 client_loan_schedule.id AS `id`,
                                             '' AS number1,
                                             '1' AS sort3,
                    						 DATE(deals_detail.penalty_date) AS sort,
                    						 '2' AS sort1,
                    						 client_loan_schedule.number,
                    						 CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',DATE_FORMAT(deals_detail.penalty_date, '%d/%m/%Y'),'</div>') AS `date`,
                    						 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(deals_detail.penalty_date) LIMIT 1) AS `exchange`,
                    						 '' AS `loan_amount`,
                    						 '' AS `loan_amount_gel`,
                                             '' AS `delta`,
                                             '' AS `delta1`,
                    						 CONCAT('<div  title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',deals_detail.penalty,if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD'),'</div>') AS percent,
                    						 CASE 
                    								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',ROUND(deals_detail.penalty/(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' USD','</div>')
                    								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT('<div title=\"ჯარიმა\" style=\"background: #ffec04; color: #000;\">',ROUND(deals_detail.penalty*(SELECT cur_cource.cource FROM cur_cource WHERE actived = 1 and DATE(cur_cource.datetime)=DATE(deals_detail.penalty_date)),2), ' GEL', '</div>') 
                    						 END AS percent_gel,
                    						 '' AS percent1,
                    						 '' AS percent_gel1,
                    						 '' AS pay_root,
                    						 '' AS pay_root_gel,
                                             '' AS jh,
                                             '' AS kj,
                                             '' AS difference,
                                             '' AS pledge_fee,
                                            '' AS pledge_fee1,
                                            '' as  pledge_payed,
                                            '' as  pledge_payed1,
                                			'' AS  pledge_delta,
                                            '' as  other,
                                			'' as  other1,
                                			'' as  other_delta
                            		FROM     client_loan_schedule
                            		JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    JOIN client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                            		JOIN deals_detail ON deals_detail.deals_id = client_loan_schedule_deal.id
                                    WHERE    client_loan_agreement.client_id = '$sub' AND deals_detail.penalty > 0  AND client_loan_schedule_deal.unda_daericxos>0 AND client_loan_schedule_deal.actived = 1 AND client_loan_schedule.activ_status = 0 AND client_loan_schedule.actived=1 AND client_loan_schedule.schedule_date <= CURDATE() AND deals_detail.actived = 1
                            		GROUP BY deals_detail.id
                            		UNION ALL
                            		SELECT  client_loan_agreement.client_id,
                            				'' AS `id`,
                            				'' AS number1,
                            				'5' AS sort3,
                            				DATE(money_transactions_detail.pay_datetime) AS sort,
                            				'2' AS sort1,
                            				'' AS number,
                            				CONCAT('<div title=\"შეთანხმების გადახდა\" style=\"background: #ffec04; color: #121111;\">',DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y'),'</div>') AS `date`,
                            				money_transactions_detail.course AS `exchange`,
                            				'' AS `loan_amount`,
                            				'' AS `loan_amount_gel`,
                            				'' AS `delta`,
                            				'' AS `delta1`,
                            				'' AS  percent,
                            				'' AS  percent_gel,
                            				CONCAT(money_transactions_detail.pay_amount, ' USD') AS percent1,
                            				CASE 
                            					WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
                            					WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
                            				END AS percent_gel1,
                            				CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
                							CASE 
                								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
                								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
                							END AS pay_root_gel,
                            				'' AS jh,
                            				'' AS kj,
                            				'' AS difference,
                            				'' AS pledge_fee,
                            				'' AS pledge_fee1,
                            				'' as pledge_payed,
                            				'' as pledge_payed1,
                            				'' AS pledge_delta,
                            				'' as other,
                            				'' as other1,
                            				'' as other_delta
                                    FROM   money_transactions
                                    JOIN   		money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                                    LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                    WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.`status` = 13 AND money_transactions_detail.actived = 1 AND (money_transactions_detail.pay_amount > 0 || money_transactions_detail.pay_root>0)
                                    GROUP BY money_transactions.id
                            		UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '7' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				 (SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2), ' USD') AS pledge_fee,
                                     CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL') AS pledge_fee1,
                                     '' as  pledge_payed,
                                     '' as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions.type_id = 2 AND money_transactions_detail.`status` = 7 
                            AND      money_transactions_detail.actived = 1 
                            AND    money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '8' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				 money_transactions_detail.course AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     CASE
                                        WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' USD')
                                        WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions_detail.course),2), ' USD')
                            		 END as  pledge_payed,
                                     CASE 
                            			WHEN money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions_detail.course),2), ' GEL')
                                        WHEN money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2), ' GEL')
                                     END as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 8 
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '3' AS number1,
                    				 '8' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     CASE 
                            			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2),' USD')
                                        WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount/money_transactions.course),2),' USD')
                                     END as  pledge_payed,
                                     CASE 
                            			WHEN money_transactions.currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount*money_transactions.course),2),' GEL')
                                        WHEN money_transactions.currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_amount),2),' GEL')
                                     END as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions_detail.`status` = 9 
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '10' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     '' as  pledge_payed,
                                     '' as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as  other,
                    				 '' as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions.type_id = 3 AND money_transactions_detail.`status` = 10 
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                            SELECT   client.id,
                    				 '' AS `id`,
                    				 '' AS number1,
                    				 '11' AS sort3,
                    				 DATE(money_transactions.pay_datetime) AS sort,
                    				 '2' AS sort1,
                    				 '' AS number,
                    				 DATE_FORMAT(money_transactions.pay_datetime, '%d/%m/%Y') AS `date`,
                    				(SELECT cur_cource.cource FROM cur_cource WHERE cur_cource.actived = 1 AND DATE(cur_cource.datetime) = DATE(money_transactions.pay_datetime) LIMIT 1) AS `exchange`,
                    				 '' AS `loan_amount`,
                    				 '' AS `loan_amount_gel`,
                    				 '' AS `delta`,
                    				 '' AS `delta1`,
                    				 '' AS percent,
                    				 '' AS percent_gel,
                    				 '' AS percent1,
                    				 '' AS percent_gel1,
                    				 '' AS pay_root,
                    				 '' AS pay_root_gel,
                    				 '' AS jh,
                    				 '' AS kj,
                    				 '' AS difference,
                    				 '' AS pledge_fee,
                                     '' AS pledge_fee1,
                                     '' as  pledge_payed,
                                     '' as  pledge_payed1,
                    				 '' AS  pledge_delta,
                                     '' as  other,
                    				 CONCAT(ROUND(SUM(money_transactions_detail.pay_amount), 2), ' GEL') as  other1,
                    				 '' as  other_delta
             				FROM     money_transactions
            				JOIN     client ON client.id = money_transactions.client_id
            				JOIN     money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
            				WHERE    client_id = '$sub' AND money_transactions_detail.type_id = 3 AND money_transactions_detail.`status` = 11
                            AND      money_transactions_detail.actived = 1 
                            AND      money_transactions.actived = 1
                            GROUP BY money_transactions.id
                            UNION ALL
                			SELECT  client_loan_agreement.client_id,
        							client_loan_schedule.id AS `id`,
                                    '' AS number1,
                                    '5' AS sort3,
        							DATE(money_transactions_detail.pay_datetime) AS sort,
        							'2' AS sort1,
        							client_loan_schedule.number,
        							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
        							money_transactions_detail.course AS `exchange`,
        							'' AS `loan_amount`,
        							'' AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
        							'' AS percent,
        							'' AS percent_gel,
        							CONCAT(ROUND(SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                    IFNULL((SELECT CASE
                                                      WHEN cl_agr.loan_currency_id = 1 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                                      WHEN cl_agr.loan_currency_id = 2 THEN IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                                    END AS jigari
                                            FROM   money_transactions_detail AS mon_tr_det
                                            JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                            JOIN   client_loan_agreement AS cl_agr ON mon_tr.agreement_id = cl_agr.id
                                            WHERE  mon_tr_det.actived = 1 
                                            AND    mon_tr_det.`status` = 3
                                            AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                            AND    mon_tr.client_id = money_transactions.client_id),0),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent1,
        							CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                           IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount,mon_tr_det.pay_amount*mon_tr_det.course)),0) 
                                                    FROM   money_transactions_detail AS mon_tr_det
                                                    JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                                    JOIN   client_loan_agreement ON client_loan_agreement.id = mon_tr.agreement_id
                                                    WHERE  mon_tr_det.actived = 1 
                                                    AND    mon_tr_det.`status` = 3
                                                    AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                                    AND    mon_tr.client_id = money_transactions.client_id),2))/money_transactions_detail.course,2), ' USD')
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND((SUM(money_transactions_detail.pay_percent + money_transactions_detail.pay_amount)-
                                             IFNULL((SELECT IFNULL(SUM(IF(mon_tr_det.currency_id = 1,mon_tr_det.pay_amount/mon_tr_det.course,mon_tr_det.pay_amount)),0) 
                                                      FROM   money_transactions_detail AS mon_tr_det
                                                      JOIN   money_transactions AS mon_tr ON mon_tr.id = mon_tr_det.transaction_id
                                                      WHERE  mon_tr_det.actived = 1 
                                                      AND    mon_tr_det.`status` = 3
                                                      AND    mon_tr.actived = 1 AND mon_tr_det.ltd_regist_tr_id = money_transactions_detail.transaction_id
                                                      AND    mon_tr.client_id = money_transactions.client_id),0))*money_transactions_detail.course,2), ' GEL')
        							END AS percent_gel1,
        							CONCAT(ROUND(SUM(money_transactions_detail.pay_root),2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS pay_root,
        							CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)/money_transactions_detail.course,2), ' USD')
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(SUM(money_transactions_detail.pay_root)*money_transactions_detail.course,2), ' GEL')
        							END AS pay_root_gel,
                                    '' AS jh,
                                     '' AS kj,
                                     '' AS difference,
                                     '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                			FROM    money_transactions
                            JOIN money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                			JOIN    client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                			JOIN    client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                			WHERE   client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 
                			AND     money_transactions_detail.`status` IN (1,2,5,6) AND (money_transactions_detail.pay_percent != '0.00' OR money_transactions_detail.pay_root!='0.00' OR money_transactions_detail.pay_amount!='0.00')
                			GROUP BY money_transactions.client_loan_schedule_id, money_transactions_detail.transaction_id
                			UNION ALL
                			SELECT  client_loan_agreement.client_id,
        							'' AS `id`,
                                    '5' AS number1,
                                    '6' AS sort3,
        							DATE(money_transactions_detail.pay_datetime) AS sort,
        							'2' AS sort1,
        							'' AS number,
        							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
        							money_transactions_detail.course AS `exchange`,
        							'' AS `loan_amount`,
        							'' AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
        							'' AS percent,
        							'' AS percent_gel,
        							CASE 
                    					WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 1 THEN CONCAT(money_transactions_detail.pay_amount, ' GEL') 
                    				    WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL')
                    					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 2 THEN CONCAT(money_transactions_detail.pay_amount, ' USD') 
                    					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD')
                    				END AS percent1,
                    				CASE 
                    					WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD')
                    				    WHEN client_loan_agreement.loan_currency_id = 1 AND money_transactions_detail.currency_id = 2 THEN CONCAT(money_transactions_detail.pay_amount, ' USD') 
                    					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL')
                    					WHEN client_loan_agreement.loan_currency_id = 2 AND money_transactions_detail.currency_id = 1 THEN CONCAT(money_transactions_detail.pay_amount, ' GEL') 
                    				END AS percent_gel1,
        							'' AS pay_root,
        							''AS pay_root_gel,
                                    '' AS jh,
                                    '' AS kj,
                                    '' AS difference,
                                    '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM   money_transactions
                            JOIN money_transactions_detail on money_transactions_detail.transaction_id = money_transactions.id
                			LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                			WHERE  client_loan_agreement.client_id = '$sub' AND money_transactions_detail.`status` = 3 AND money_transactions_detail.actived = 1
                			UNION ALL
                            SELECT  client_loan_agreement.client_id,
        							client_loan_schedule.id AS `id`,
                                    '8' AS number1,
                                    '3' AS sort3,
        							DATE(money_transactions_detail.pay_datetime) AS sort,
        							'2' AS sort1,
        							client_loan_schedule.number,
        							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
        							money_transactions_detail.course AS `exchange`,
        							'' AS `loan_amount`,
        							'' AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
        							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
        							CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
        							END AS percent_gel,
        							'' AS percent1,
        							'' AS percent_gel1,
        							'' AS pay_root,
        							''AS pay_root_gel,
                                    '' AS jh,
                                    '' AS kj,
                                    '' AS difference,
                                    '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM   money_transactions
                            JOIN money_transactions_detail on money_transactions_detail.transaction_id = money_transactions.id
                			JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                			JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                			WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 5 AND money_transactions_detail.actived = 1
                            UNION ALL
                            SELECT  client_loan_agreement.client_id,
        							client_loan_schedule.id AS `id`,
                                    '9' AS number1,
                                    '4' AS sort3,
        							DATE(money_transactions_detail.pay_datetime) AS sort,
        							'2' AS sort1,
        							client_loan_schedule.number,
        							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
        							money_transactions_detail.course AS `exchange`,
        							'' AS `loan_amount`,
        							'' AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
        							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
        							CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
        							END AS percent_gel,
        							'' AS percent1,
        							'' AS percent_gel1,
        							'' AS pay_root,
        							''AS pay_root_gel,
                                    '' AS jh,
                                    '' AS kj,
                                    '' AS difference,
                                    '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                            FROM   money_transactions
                            JOIN money_transactions_detail on money_transactions_detail.transaction_id = money_transactions.id
                			JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                			JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                			WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 6 AND money_transactions_detail.actived = 1
                            UNION ALL
                			SELECT  client_loan_agreement.client_id,
        							client_loan_schedule.id AS `id`,
                                    '7' AS number1,
                                    '2' AS sort3,
        							DATE(client_loan_schedule.schedule_date) AS sort,
                                    '2' AS sort1,
        							client_loan_schedule.number,
        							DATE_FORMAT(money_transactions_detail.pay_datetime, '%d/%m/%Y') AS `date`,
        							money_transactions_detail.course AS `exchange`,
        							'' AS `loan_amount`,
        							DATEDIFF(money_transactions_detail.pay_datetime, client_loan_schedule.pay_date) AS `loan_amount_gel`,
                                    '' AS `delta`,
                                    '' AS `delta1`,
        							CONCAT(ROUND(money_transactions_detail.pay_amount,2), if(client_loan_agreement.loan_currency_id = 1, ' GEL', ' USD')) AS percent,
        							CASE 
        								WHEN client_loan_agreement.loan_currency_id = 1 THEN CONCAT(ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2), ' USD') 
        								WHEN client_loan_agreement.loan_currency_id = 2 THEN CONCAT(ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2), ' GEL') 
        							END AS percent_gel,
        							'' AS percent1,
        							'' AS percent_gel1,
        							'' AS pay_root,
        							'' AS pay_root_gel,
                                    '' AS jh,
                                    '' AS kj,
                                    '' AS difference,
                                    '' AS pledge_fee,
                                    '' AS pledge_fee1,
                                    '' as  pledge_payed,
                                    '' as  pledge_payed1,
                        			'' AS  pledge_delta,
                                    '' as  other,
                        			'' as  other1,
                        			'' as  other_delta
                			FROM   money_transactions
                            JOIN money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
                			JOIN   client_loan_schedule ON client_loan_schedule.id = money_transactions.client_loan_schedule_id
                			JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                			WHERE  client_loan_agreement.client_id = '$sub' AND client_loan_schedule.actived=1 AND money_transactions_detail.`status` = 2
                            UNION ALL";
                	    
                }
                 
            }
            	
            $rResult = mysql_query("SELECT   letter.client_id,
                            				 letter.number,
                            				 letter.date,
                            				 ROUND(letter.exchange,4),
                            				 letter.loan_amount,
                            				 letter.loan_amount_gel,
    	                                     letter.delta AS delta,
    	                                     letter.delta1 AS delta1,
                            				 letter.percent,
                            				 letter.percent_gel,
                            				 letter.percent1,
                            				 letter.percent_gel1,
                            				 letter.pay_root,
                            				 letter.pay_root_gel,
                            				 '' as `g`,
                            				 '' as `gd`,
                            				 letter.difference AS difference,
                            				 letter.pledge_fee1,
                            				 letter.pledge_fee,
                            	             letter.pledge_payed1,
                            	             letter.pledge_payed,
	                                         '' as  pledge_delta1,
                            	             '' as  pledge_delta,
	                                         letter.other,
                            	             letter.other1,
	                                         '' as  other_delta,
                            				 letter.sort1,
                            				 letter.loan_amount_gel,
    	                                     letter.number1
                                    FROM($qvr)AS letter
                                    ORDER BY letter.client_id, letter.sort1,  letter.sort, letter.sort3 ASC ");
            
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
	               if ($i == 6 || $i == 7) {
	                   $row[] = $aRow[$i];
	               }else if ($i == 8) {
	                   $sumpercent+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==7){
	                       $row[] = '<div title="'.$aRow[loan_amount_gel].' დღის ჯარიმა" style="background: #009688;  color: #fff;">'.$aRow[$i].'</div>';
	                   }else if($aRow[number1]==8){
	                       $row[] = '<div title="საკომისიო" style="background: #8BC34A; color: #fff;">'.$aRow[$i].'</div>';
	                   }else if($aRow[number1]==9){
	                       $row[] = '<div title="ნასარგებლები დღეები" style="background: #FF9800; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
    	                   $row[] = $aRow[$i];
    	               }
	               }else if ($i == 9){
	                   $sumpercent1+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==7){
	                       $row[] = '<div title="'.$aRow[loan_amount_gel].' დღის ჯარიმა" style="background: #009688; color: #fff;">'.$aRow[$i].'</div>';
	                   }else if($aRow[number1]==8){
	                       $row[] = '<div title="საკომისიო" style="background: #8BC34A; color: #fff;">'.$aRow[$i].'</div>';
	                   }else if($aRow[number1]==9){
	                       $row[] = '<div title="ნასარგებლები დღეები" style="background: #FF9800; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 10){
	                   $sumpercent2+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==5){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   } 
	               }elseif ($i == 11){
	                   $sumpercent3+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==5){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 12){
	                   $sumpercent4+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==5){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 13){
	                   $sumpercent5+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==5){
	                       $row[] = '<div title="წინა თვის მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 19){
	                   $sumpercent19+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==3){
	                       $row[] = '<div title="მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
	                   }else{
	                       $row[] = $aRow[$i];
	                   }
	               }elseif ($i == 20){
	                   $sumpercent20+=floatval(strip_tags($aRow[$i]));
	                   if($aRow[number1]==3){
	                       $row[] = '<div title="მეტობა" style="background: #F44336; color: #fff;">'.$aRow[$i].'</div>';
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
	    $data['aaData7'][] = $sumpercent19;
	    $data['aaData8'][] = $sumpercent20;
	    
	    break;
    case 'get_calculation':
        $hidde_idd	= $_REQUEST[hidde_id];
        $res = mysql_fetch_array(mysql_query("SELECT loan_currency.`name`
                                              FROM   client_loan_agreement
                                              JOIN   loan_currency ON loan_currency.id = client_loan_agreement.loan_currency_id
                                              WHERE  client_id = '$hidde_idd'"));
        $page = '<div id="dialog-form">
                    <div id="tabs1" style="width: 99%;">
                    	<ul>
                    		<li><a href="#tab-0">პროცენტის გადახდა</a></li>
                    		<li><a href="#tab-1">მანქანის გაყვანა</a></li>
                    	</ul>
                        <div id="tab-0">
                             <fieldset>
                                <table>
                                    <tr>
                                       <td style="width: 150px;"><label>სესხის ვალუტა: '.$res[name].'</label></td>
                                       <td style="width: 103px;"><label>მეორე პირგასამტეხლო</label></td>
                                       <td style="width: 20px;"><input id="otherrr_penalty" type="checkbox" class="idle" style="width: 15px;" type="text" value="1"></td></td>
                                    </tr>
                                </table>
                                <table class="dialog-form-table" style="width: 100%;">
                                   <tr>
                                       <td style="width: 120px;"><label>აირჩიე თარიღი</label></td>
                                       <td style="width: 115px;"><input id="pay_datee" class="idle" style="width: 100px;" type="text" value=""></td>
                                       <td colspan="2"><button id="check_calculation">შემოწმება</button></td>
                                   </tr>
                                   <tr style="height:5px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>გრაფიკი</label></td>
                                       <td style="width: 115px;"><select id="cal_loan_schedule_id" style="width: 105px;"></select></td>
                                       <td style="width: 120px;"><label>სულ შესატანი თანხა</label></td>
                                       <td style="width: 115px;"><input id="full_fee4" class="idle" style="width: 100px;" type="text" value="0" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:20px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>სულ შესატანი თანხა</label></td>
                                       <td style="width: 115px;"><input id="full_fee2" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                       <td style="width: 123px;"><label>სულ შეტანილი თანხა</label></td>
                                       <td><input  id="full_pay2" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>ძირი თანხა</label></td>
                                       <td colspan="3"><input id="root_fee2" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>პროცენტი</label></td>
                                       <td colspan="3"><input id="percent_fee2" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>ჯარიმა</label></td>
                                       <td colspan="3"><input id="penalty_fee2" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>დაზღვევა</label></td>
                                       <td colspan="3"><input id="pledge2" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>სხვა ხარჯი</label></td>
                                       <td colspan="3"><input id="other2" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                </table>
                             </fieldset>
                        </div>
                        <div id="tab-1">
                            <fieldset>
                                <table class="dialog-form-table" style="width: 100%;">
                                   <tr>
                                       <td colspan="3" style="width: 120px;"><label>სესხის ვალუტა: '.$res[name].'</label></td>
                                   </tr>
                                   <tr>
                                       <td style="width: 120px;"><label>აირჩიე თარიღი</label></td>
                                       <td style="width: 115px;"><input id="pay_datee1" class="idle" style="width: 100px;" type="text" value=""></td>
                                       <td colspan="2"><button id="check_calculation_out">შემოწმება</button></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>სულ შესატანი</label></td>
                                       <td><input id="full_fee3" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                       <td style="width: 123px;"><label>სულ შეტანილი თანხა</label></td>
                                       <td><input  id="full_pay3" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>ძირი თანხა</label></td>
                                       <td cospan="3"><input id="root_fee3" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                       
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>პროცენტი</label></td>
                                       <td colspan="3"><input id="percent_fee3" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>ჯარიმა</label></td>
                                       <td colspan="3"><input id="penalty_fee3" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>შეთანხმება</label></td>
                                       <td colspan="3"><input id="deal_fee3" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>საკომისიო</label></td>
                                       <td colspan="3"><input id="sakomiso" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>ნასარგებლები<br>დღეები</label></td>
                                       <td colspan="3"><input id="nasargeblebi" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>დაზღვევა</label></td>
                                       <td colspan="3"><input id="dazgveva" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                                   <tr style="height:10px;"></tr>
                                   <tr>
                                       <td style="width: 120px;"><label>სხვა ხარჯი</label></td>
                                       <td colspan="3"><input id="sxvaxarji" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                                   </tr>
                               </table>
                            </fieldset>
                        </div>
                    </div>
                 </div>';
            $data = array('page' => $page);
        break;
        
    case 'show_loan':
        $hidde_idd	= $_REQUEST[hidde_id];
        $res = mysql_fetch_assoc(mysql_query("SELECT client.id,
                                        			 client_loan_agreement.datetime AS loan_agreement_datetime,
                                                     client_loan_agreement.loan_type_id AS loan_type_id,
                                                     client_loan_agreement.agreement_type_id AS agreement_type_id,
                                                     client_loan_agreement.loan_amount AS loan_agreement_loan_amount,
                                                     client_loan_agreement.loan_months AS loan_agreement_loan_months,
                                                     client_loan_agreement.percent AS loan_agreement_percent,
                                                     client_loan_agreement.monthly_pay AS loan_agreement_monthly_pay,
                                                     client_loan_agreement.penalty_days AS loan_agreement_penalty_days,
                                                     client_loan_agreement.penalty_percent AS loan_agreement_penalty_percent,
                                                     client_loan_agreement.penalty_additional_percent AS loan_agreement_penalty_additional_percent,
                                                     client_loan_agreement.insurance_fee AS loan_agreement_insurance_fee,
                                                     client_loan_agreement.pledge_fee AS loan_agreement_pledge_fee,
                                                     client_loan_agreement.loan_fee AS loan_agreement_loan_fee,
                                                     client_loan_agreement.proceed_fee AS loan_agreement_proceed_fee,
                                                     client_loan_agreement.rs_message_number AS loan_agreement_rs_message_number,
                                                     client_loan_agreement.pay_day AS loan_agreement_pay_day,
                                                     client_loan_agreement.exchange_rate AS loan_agreement_exchange_rate,
                                                     client_loan_agreement.id AS loan_agreement_id,
                                                     client_loan_agreement.status AS loan_agreement_actived_status,
                                                     client_loan_agreement.proceed_percent AS loan_agreement_proceed_percent,
                                                     client_loan_agreement.loan_currency_id AS loan_currency_id,
                                                     client_loan_agreement.oris_code AS oris_code,
                                                     client_loan_agreement.canceled_status AS canceled_status,
                                                     client_loan_agreement.responsible_user_id AS responsible_user_id,
                                                     client_loan_agreement.loan_beforehand_percent AS loan_beforehand_percent
                                            FROM    `client`
                                            LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = client.id
                                            WHERE  client.id = $hidde_idd"));
        
        if ($res[loan_type_id] == 2) {
            $input_hidde = "display:none;";
        }else{
            $input_hidde = "display:block;";
        }
        
        if($res[agreement_type_id] == 1 || $res[agreement_type_id] == 4 || $res[agreement_type_id] == 6 || $res[agreement_type_id] == 8 || $res[agreement_type_id] == 9 || $res[agreement_type_id] == 11){
            $check_shss = "";
        }else{
            $check_shss = "display:none;";
        }
        
        $page = '<div id="dialog-form">
                    <fieldset>
                        <legend>ძირითადი ინფორმაცია</legend>
                        <table style="width: 100%;">
            			   <tr>
                               <td style="width: 125px;"><label for="phone1">ხელშეკრ. N</label></td>
                               <td style="width: 190px;"><label for="phone2">თარიღი</label></td>
                               <td style="width: 203px;"><label for="loan_type">სესხის ტიპი</label></td>
                               <td style="width: 335px;"><label for="client_surname">ხელშეკრულების ტიპი</label></td>
        	               </tr>
                           <tr>
                               <td style="width: 125px;"><input class="idle" style="width: 113px;" id="agreement_number" type="text" value="'.$res[loan_agreement_id].'" disabled="disabled"></td>
                               <td style="width: 190px;"><input class="idle" style="width: 180px;" id="agreement_date" type="text" value="'.$res[loan_agreement_datetime].'" disabled="disabled"></td>
                               <td style="width: 203px;"><select class="idle" id="loan_agreement_type" style="width: 180px;">'.loan_type($res[loan_type_id]).'</select></td>
                               <td style="width: 335px;"><select class="idle" id="agreement_type_id" style="width: 332px;">'.agreement_type($res[agreement_type_id],$res[loan_type_id]).'</select></td>
                           </tr>
                        </table>
                        <table style="width: 100%;">          
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 130px;"><label for="phone1">სესხის ვალუტა</label></td>
                               <td style="width: 130px;"><label for="client_name">სრული მოცულობა</label></td>
                               <td style="width: 130px;"><label for="phone2">ყოველთ. პროცენტი</label></td>
                               <td style="width: 130px;"><label for="client_surname">სარგებლობის ვადა</label></td>
        	                   <td style="width: 130px;"><label for="phone1">გაცემის საკომისიო</label></td>
                               <td style="width: 130px;"><label for="phone1">წინსწ.დაფარვის საკომ.</label></td>
                           </tr>
                           <tr>
                               <td style="width: 130px;"><select class="idle" id="loan_currency" style="width: 130px;">'.loan_currency($res[loan_currency_id]).'</select></td>
                               <td style="width: 130px;"><input class="idle" style="width: 129px;" id="loan_amount" type="text" value="'.$res[loan_agreement_loan_amount].'" disabled="disabled"></td>
                               <td style="width: 130px;"><input class="idle" style="width: 129px;" id="month_percent" type="text" value="'.$res[loan_agreement_percent].'" disabled="disabled"></td>
                               <td style="width: 130px;"><input class="idle" style="width: 129px;" id="loan_months" type="text" value="'.$res[loan_agreement_loan_months].'" disabled="disabled"></td>
                               <td style="width: 130px;"><input class="idle" style="width: 129px;" id="loan_fee" type="text" value="'.$res[loan_agreement_loan_fee].'" disabled="disabled"></td>
                               <td style="width: 130px;"><input class="idle" style="width: 129px;" id="loan_beforehand_percent" type="text" value="'.$res[loan_beforehand_percent].'" disabled="disabled"></td>
                           </tr>
                        </table>
                        <table style="width: 100%;"> 
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 250px;"><label style="'.$input_hidde.'" class="label_label" for="phone2">ხელშკრ. გაგრძ. საფასური</label></td>
                               <td style="width: 220px;"><label style="'.$input_hidde.'" class="label_label" for="client_name">პროცენტი</label></td>
                               <td style="width: 220px;"><label for="client_surname">სადაზღვევო ხარჯი</label></td>
        	                   <td style="width: 220px;"><label for="phone1">გირავნობის ხარჯი</label></td>
                               
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input class="idle" style="width: 200px; '.$input_hidde.'" id="proceed_fee" type="text" value="'.$res[loan_agreement_proceed_fee].'" disabled="disabled"></td>
                               <td style="width: 220px;"><input class="idle" style="width: 200px; '.$input_hidde.'" id="proceed_percent" type="text" value="'.$res[loan_agreement_proceed_percent].'" disabled="disabled"></td>
                               <td style="width: 220px;"><input class="idle" style="width: 200px;" id="insurance_fee" type="text" value="'.$res[loan_agreement_insurance_fee].'" disabled="disabled"></td>
                               <td style="width: 220px;"><input class="idle" style="width: 195px;" id="pledge_fee" type="text" value="'.$res[loan_agreement_pledge_fee].'" disabled="disabled"></td>
                               
                           </tr>
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 220px;"><label for="client_name">ყოველთვიურად შეს. თანხა</label></td>
                               <td style="width: 220px;"><label class="rs_message_number" style="'.$check_shss.'" for="client_surname">შემოსავლების სამსახ. შეტყობ. N</label></td>
                               <td style="width: 220px;"><label for="client_surname">ორისის კოდი</label></td>
                               <td colspan="2" style="width: 220px;"><label for="phone2">ვალუტის კურსი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;">
                                   <table>
                                       <tr>
                                           <td>
                                                <input class="idle" style="width: 110px;" id="monthly_pay" type="text" value="'.$res[loan_agreement_monthly_pay].'" disabled="disabled">
                                           </td>
                                           <td>
                                                <button style="margin-left: 4px;" id="check_monthly_pay">შემოწმება</button>
                                           </td>
                                       </tr>
                                   </table>
                               </td>
                               <td style="width: 220px;"><input class="idle" style="width: 200px; '.$check_shss.' " id="rs_message_number" type="text" value="'.$res[loan_agreement_rs_message_number].'" disabled="disabled"></td>
                               <td style="width: 220px;"><input class="idle" style="width: 200px;" id="oris_code" type="text" value="'.$res[oris_code].'"></td>
                               <td colspan="2" style="width: 220px;"><input class="idle" style="width: 195px;" id="exchange_rate" type="text" value="'.$res[loan_agreement_exchange_rate].'" disabled="disabled"></td>
                           </tr>
                           <tr style="height:18px"></tr>
                           <tr>
                               <td style="width: 220px;"><label for="client_name">ვადაგადაც. პირგასამტეხლო%</label></td>
                               <td style="width: 220px;"><label for="client_surname">ვადაგადაცილებული დღეები</label></td>
        	                   <td style="width: 220px;"><label for="phone1">ვადაგადაც. პირგასამტეხლო%</label></td>
                               <td style="width: 220px;"><label for="phone1">ხელმომწერი პირი</label></td>
                           </tr>
                           <tr>
                               <td style="width: 220px;"><input class="idle" style="width: 200px;" id="penalty_percent" type="text" value="'.$res[loan_agreement_penalty_percent].'" disabled="disabled"></td>
                               <td style="width: 220px;">
                                   <table style="width: 205px;">
                                       <tr style="width: 100%;">
                                           <td style="width: 25px;">
                                                <label style="width: 25px; padding-top: 5px;">მდე-</label>
                                           </td>
                                           <td style="width: 138px;"><input class="idle" style="width: 138px;" id="penalty_days" type="text" value="'.$res[loan_agreement_penalty_days].'" disabled="disabled"></td>
                                           <td style="width: 25px;">
                                                <label style="width: 25px; padding-top: 5px;">-დან</label>
                                           </td>
                                       </tr>
                                   </table>
                               </td>
                               <td style="width: 220px;"><input class="idle" style="width: 200px;" id="penalty_additional_percent" type="text" value="'.$res[loan_agreement_penalty_additional_percent].'" disabled="disabled"></td>
                               <td style="width: 205px;"><select class="idle" id="responsible_user_id" style="width: 200px;">'.getresponsible($res['responsible_user_id']).'</select></td>
                           </tr>
                        </table>
        	        </fieldset>
                </div>';
        
        $data = array('page' => $page);
        
        break;
    case 'check_calculation':
    
        $local_id             = $_REQUEST['local_id'];
        $pay_datee            = $_REQUEST['pay_datee'];
        $cal_loan_schedule_id = $_REQUEST['cal_loan_schedule_id'];
        $other_penalty        = $_REQUEST['other_penalty'];

        $res = mysql_fetch_assoc(mysql_query("SELECT 	    client_loan_schedule.id,
                                                            client_loan_schedule.pay_amount,
                                                            client_loan_schedule.root,
                                                            client_loan_schedule.percent,
                                                            client_loan_agreement.insurance_fee,
                                                            client_loan_agreement.pledge_fee,
                                                            client_loan_agreement.loan_currency_id,
                                                            client_loan_agreement.id AS agrement_id,
                                                            client_loan_agreement.loan_amount,
                                                            client_loan_agreement.status,
                                                            DATEDIFF('$pay_datee', client_loan_schedule.schedule_date) AS gadacilebuli,
                                                            client_loan_schedule.root + client_loan_schedule.remaining_root AS remaining_root,
                                                            client_loan_agreement.penalty_days,
                                                            client_loan_agreement.penalty_percent,
                                                            client_loan_agreement.penalty_additional_percent,
                                                            client_loan_schedule.schedule_date AS pay_date,
                                                            client_loan_agreement.grace_period_caunt
                                                FROM 	   `client_loan_schedule`
                                                LEFT JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                JOIN        client ON client.id = client_loan_agreement.client_id
                                                WHERE       client_loan_schedule.actived = 1 AND client_id = $local_id AND client_loan_schedule.`status` != 1 
                                                AND         client_loan_schedule.id = '$cal_loan_schedule_id'
                                                ORDER BY    pay_date ASC
                                                LIMIT 1"));
        
        $remainig_root = $res[remaining_root];
        $gadacilebuli_day_count = $res[gadacilebuli]; 
        $check_holliday_day = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS count
                                                             FROM   holidays
                                                             WHERE  actived = 1
                                                             AND    DATE(date)>='$res[pay_date]'
                                                             AND    DATE(date)< '$pay_datee'"));
    
        $gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];
        
        if ($gadacilebuli_day_count<=$res[grace_period_caunt]) {
            $penalty = 0;
        }else{
            $gadacilebuli_day_count=$gadacilebuli_day_count - $res[grace_period_caunt];
            if ($other_penalty == 1) {
                $RemainigRoot = mysql_fetch_array(mysql_query("SELECT    client_loan_schedule.remaining_root+client_loan_schedule.root AS remaining_root
                                                                FROM     client_loan_schedule
                                                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                                WHERE    client_loan_agreement.client_id = '$local_id'
                                                                AND 	 client_loan_schedule.schedule_date < '$pay_datee'
                                                                AND      client_loan_schedule.`status` = 1
                                                                AND      client_loan_schedule.penalty_check=0
                                                                ORDER BY client_loan_schedule.id DESC
                                                                LIMIT 1"));
                $penalty = round(($RemainigRoot[remaining_root] * ($res[penalty_additional_percent]/100))*$gadacilebuli_day_count,2);
            }else{
                $check_count_schedule = mysql_num_rows(mysql_query("SELECT 	    client_loan_schedule.id
                                                                    FROM 	   `client_loan_schedule`
                                                                    LEFT JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                                    JOIN        client ON client.id = client_loan_agreement.client_id
                                                                    WHERE       client_loan_schedule.actived = 1 AND client_id = '$local_id' AND client_loan_schedule.`status` =0 
                                                                    AND         client_loan_schedule.id < '$cal_loan_schedule_id'"));
    
                
            
                if ($check_count_schedule > 0) {
                    
                    $Remainig_Root = mysql_fetch_array(mysql_query("SELECT 	   client_loan_schedule.remaining_root+client_loan_schedule.root AS remaining_root
                                                                   FROM 	  `client_loan_schedule`
                                                                   LEFT JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                                   JOIN        client ON client.id = client_loan_agreement.client_id
                                                                   WHERE       client_loan_schedule.actived = 1 AND client_id = '$local_id' AND client_loan_schedule.`status` =0 
                                                                   AND         client_loan_schedule.id < '$cal_loan_schedule_id'
                                                                   LIMIT 1     "));
                    $penalty = round(($Remainig_Root[remaining_root] * ($res[penalty_additional_percent]/100))*$gadacilebuli_day_count,2);
                }else{
                    if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$res[penalty_days]) {
                        $penalty = round(($remainig_root * ($res[penalty_percent]/100))*$gadacilebuli_day_count,2);
                    }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$res[penalty_days] && $res[penalty_additional_percent]>0){
                        $penalty = round((($remainig_root * ($res[penalty_percent]/100))*$res[penalty_days])+($remainig_root * ($res[penalty_additional_percent]/100))*($gadacilebuli_day_count-$res[penalty_days]),2);
                        
                    }elseif($gadacilebuli_day_count>0 && $res[penalty_additional_percent] <= 0){
                        
                        $penalty = round(($remainig_root * ($res[penalty_percent]/100))*$gadacilebuli_day_count,2);
                    }
                }
            }
        }
        if ($res[status] == 1) {
            $res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(ROUND(SUM(CASE
                                        										WHEN money_transactions_detail.currency_id = client_loan_agreement.loan_currency_id THEN money_transactions_detail.pay_amount
                                        										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 1 THEN money_transactions_detail.pay_amount/money_transactions_detail.course
                                        										WHEN money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 2 THEN money_transactions_detail.pay_amount*money_transactions_detail.course
                                        									END),2),0) AS pay_amount
                                                   FROM    money_transactions_detail
                                                   JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                   JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                                   JOIN    client_loan_schedule ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                                                   WHERE   client_loan_schedule.id = '$res[id]' 
                                                   AND     money_transactions_detail.`status` = 3
                                                   AND     money_transactions_detail.actived = 1"));
    
            $res_pledge = mysql_fetch_assoc(mysql_query("SELECT   SUM(IFNULL(CASE
                                                                            WHEN money_transactions_detail.currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2)
                                                                            WHEN money_transactions_detail.currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount,2)
                                                                         END,0)) AS peg_pledge
                                                        FROM     money_transactions
                                                        JOIN     money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                        JOIN     client_loan_agreement ON money_transactions.agreement_id = client_loan_agreement.id
                                                        WHERE    money_transactions.client_id = '$local_id'
                                                        AND      money_transactions_detail.`status` = 8 AND money_transactions_detail.type_id = 2
                                                        AND      money_transactions_detail.actived = 1 AND money_transactions.actived = 1"));
            
            $res_pledge1 = mysql_fetch_assoc(mysql_query("SELECT   SUM(IFNULL(CASE
                                                                            WHEN money_transactions_detail.currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount/money_transactions_detail.course,2)
                                                                            WHEN money_transactions_detail.currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount,2)
                                                                         END,0)) AS peg_pledge
                                                        FROM     money_transactions
                                                        JOIN     money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                        JOIN     client_loan_agreement ON money_transactions.agreement_id = client_loan_agreement.id
                                                        WHERE    money_transactions.client_id = '$local_id'
                                                        AND      money_transactions_detail.`status` = 8 AND money_transactions_detail.type_id = 2
                                                        AND      money_transactions_detail.actived = 1 AND money_transactions.actived = 1
                                                        "));
            
            $pledge_calk = $res_pledge[peg_pledge] - $res_pledge1[peg_pledge];
            $res_other = mysql_fetch_assoc(mysql_query("SELECT ROUND(SUM(money_transactions_detail.pay_amount),2) AS pay_amount
                                                        FROM   money_transactions_detail
                                                        JOIN   money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                                        WHERE  money_transactions.client_id = '$local_id'
                                                        AND    money_transactions.actived = 1
                                                        AND    money_transactions_detail.actived = 1
                                                        AND    money_transactions_detail.`status` = 10"));
            
            $res_other1 = mysql_fetch_assoc(mysql_query("SELECT ROUND(SUM(money_transactions_detail.pay_amount),2) AS pay_amount
                                                         FROM   money_transactions_detail
                                                         JOIN   money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                                         WHERE  money_transactions.client_id = '$local_id'
                                                         AND    money_transactions.actived = 1
                                                         AND    money_transactions_detail.actived = 1
                                                         AND    money_transactions_detail.`status` = 11"));
            
            $other_pay = round($res_other[pay_amount] - $res_other1[pay_amount],2);
            
            if ($other_pay<=1) {
                $other_pay = 0;
            }
            
            $data = array('pay_amount' => $res[pay_amount]+$penalty, 'root' => $res[root], 'percent' => $res[percent], 'penalty' => $penalty, 'pay_amount1' => $res1[pay_amount], 'other2' => $other_pay, 'pledge2' => $pledge_calk);
        }else{
            global  $error;
            $error = 'ხელშეკრულება არ არის გააქტიურებული';
        }
    
    
        break;
        
    case 'check_calculation_out':
    
        $local_id  = $_REQUEST['local_id'];
        $pay_datee = $_REQUEST['pay_datee1'];
        $res_pledge = mysql_fetch_assoc(mysql_query("SELECT  SUM(IFNULL(CASE
                                                						WHEN money_transactions_detail.currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2)
                                                						WHEN money_transactions_detail.currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount,2)
                                                    				 END,0)) AS peg_pledge,
                                                             money_transactions.pay_datetime
                                                    FROM     money_transactions
                                                    JOIN     money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                    JOIN     client_loan_agreement ON money_transactions.agreement_id = client_loan_agreement.id
                                                    WHERE    money_transactions.client_id = '$local_id'
                                                    AND      money_transactions_detail.`status` = 7 AND money_transactions.type_id = 2
                                                    AND      money_transactions.actived = 1 AND money_transactions_detail.actived = 1
                                                    "));
        
        $res_pledge1 = mysql_fetch_assoc(mysql_query("SELECT   SUM(IFNULL(CASE
                                                                          WHEN money_transactions_detail.currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2)
                                                                          WHEN money_transactions_detail.currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount,2)
                                                                      END,0)) AS peg_pledge,
                                                               money_transactions.pay_datetime
                                                      FROM     money_transactions
                                                      JOIN     money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                      JOIN     client_loan_agreement ON money_transactions.agreement_id = client_loan_agreement.id
                                                      WHERE    money_transactions.client_id = '$local_id'
                                                      AND      money_transactions_detail.`status` = 8 AND money_transactions.type_id = 2
                                                      AND      money_transactions_detail.actived = 1 AND money_transactions.actived = 1
                                                      "));
        
        $pledge = $res_pledge[peg_pledge] - $res_pledge1[peg_pledge];
        
        $res_other = mysql_fetch_assoc(mysql_query("SELECT ROUND(SUM(money_transactions_detail.pay_amount),2) AS pay_amount
                                                     FROM   money_transactions_detail
                                                     JOIN   money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                                     WHERE  money_transactions.client_id = '$local_id'
                                                     AND    money_transactions.actived = 1
                                                     AND    money_transactions_detail.actived = 1
                                                     AND    money_transactions_detail.`status` = 10"));
        
        $res_other1 = mysql_fetch_assoc(mysql_query("SELECT ROUND(SUM(money_transactions_detail.pay_amount),2) AS pay_amount
                                                     FROM   money_transactions_detail
                                                     JOIN   money_transactions ON money_transactions_detail.transaction_id = money_transactions.id
                                                     WHERE  money_transactions.client_id = '$local_id'
                                                     AND    money_transactions.actived = 1
                                                     AND    money_transactions_detail.actived = 1
                                                     AND    money_transactions_detail.`status` = 11"));
        
        $other_pay = round($res_other[pay_amount] - $res_other1[pay_amount],2);
        
        if ($other_pay<=1) {
            $other_pay = 0;
        }
        
        $check_count = mysql_query("SELECT client_loan_schedule.id,
                                           DATEDIFF('$pay_datee', client_loan_schedule.schedule_date) AS gadacilebuli,
                                           client_loan_agreement.penalty_days,
                            			   client_loan_agreement.penalty_percent,
                            			   client_loan_agreement.penalty_additional_percent,
                                           client_loan_schedule.schedule_date AS pay_date,
                                           ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2) AS remaining_root,
                                           client_loan_agreement.grace_period_caunt
                                    FROM   client_loan_schedule 
                                    JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                    WHERE  client_loan_agreement.client_id = '$local_id'
                                    AND    DATE(client_loan_schedule.schedule_date)<='$pay_datee'
                                    AND    client_loan_schedule.`status` = 0
                                    AND    client_loan_schedule.actived = 1");
        
        $res_avans = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(ROUND(SUM(CASE
                                                                                WHEN    money_transactions_detail.currency_id = client_loan_agreement.loan_currency_id THEN money_transactions_detail.pay_amount
                                                                                WHEN    money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 1 THEN money_transactions_detail.pay_amount/money_transactions_detail.course
                                                                                WHEN    money_transactions_detail.currency_id !=client_loan_agreement.loan_currency_id AND money_transactions_detail.currency_id = 2 THEN money_transactions_detail.pay_amount*money_transactions_detail.course
                                                                             END),2),0) AS pay_amount
                                                    FROM    money_transactions_detail
                                                    JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                    JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                                    WHERE   client_loan_agreement.client_id = '$local_id'
                                                    AND     money_transactions_detail.`status` = 3
                                                    AND     money_transactions_detail.actived = 1"));
        
        
        $resultt = mysql_fetch_array(mysql_query("SELECT (SELECT ROUND(clsh.remaining_root+clsh.root,2) FROM client_loan_schedule AS clsh WHERE clsh.id = MIN(client_loan_schedule.id)) AS `remaining_root`,
                                                         (SELECT DATEDIFF('$pay_datee', clsh.schedule_date) FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `gadacilebuli`,
                                                          client_loan_agreement.loan_beforehand_percent,
                                                          SUM(client_loan_schedule.percent) AS percent,
                                                         (SELECT clsh.remaining_root FROM client_loan_schedule AS clsh WHERE clsh.id = MAX(client_loan_schedule.id)) AS `check_remaining_root`,
                                                          client_loan_agreement.grace_period_caunt
                                                        
                                                  FROM   client_loan_schedule
                                                  JOIN   client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                                  WHERE  client_loan_agreement.client_id = $local_id
                                                  AND    DATE(client_loan_schedule.schedule_date)<='$pay_datee'
                                                  AND    client_loan_schedule.`status` = 0
                                                  AND    client_loan_schedule.actived = 1"));
        
        $res1 = mysql_query("SELECT   client_loan_schedule.percent/30 AS `percent`
                             FROM     client_loan_schedule
                             JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                             WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.actived = 1 AND client_loan_schedule.schedule_date >= '$pay_datee'
                             ORDER BY client_loan_schedule.id ASC
                             LIMIT 1");
        
        $result1 = mysql_fetch_assoc($res1);
        
        $remaining_root = $resultt[remaining_root];
        
        if ($remaining_root == '0.00') {
            $remaining_root = $deal[remaining_root];
        }
        $rercent        = $resultt[percent];
        $sakomisio      = '0.00';
        $nasargeblebi   = '0.00';
        
        $penalty = 0;
        $i       = 0;
        while ($row_all = mysql_fetch_array($check_count)) {
        
            $gadacilebuli_day_count = $row_all[gadacilebuli];
        
            $check_holliday_day = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS count
                                                                 FROM   holidays
                                                                 WHERE  actived = 1
                                                                 AND    DATE(date)>='$row_all[pay_date]'
                                                                 AND    DATE(date)< '$pay_datee'"));
        
            $gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];
        
            if($gadacilebuli_day_count<=$row_all[grace_period_caunt]){
                $penalty1 = 0;
            }else{
                $gadacilebuli_day_count=$gadacilebuli_day_count - $row_all[grace_period_caunt];
                if ($i == 0) {
                    if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$row_all[penalty_days]) {
                        $penalty1 = round(($remaining_root * ($row_all[penalty_percent]/100))*$gadacilebuli_day_count,2);
                    }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$row_all[penalty_days]){
                        $penalty1 = round(round(($remaining_root * ($row_all[penalty_percent]/100))*$row_all[penalty_days],2)+round(($remaining_root * ($row_all[penalty_additional_percent]/100))*($gadacilebuli_day_count-$row_all[penalty_days]),2),2);
                    }
                }else{
                    $penalty1 = round(($remaining_root * ($row_all[penalty_additional_percent]/100))*$gadacilebuli_day_count,2);
                }
            }
            $i++;
            $penalty = $penalty+$penalty1;
        }
        
       
        
        if ($resultt[check_remaining_root] > 0){
            $sakomisio    = round($remaining_root * ($resultt[loan_beforehand_percent]/100),2);
            if ($resultt[gadacilebuli]>=0) {
                $nasargeblebi = round($result1[percent]*$resultt[gadacilebuli],2);
            }
            
        }

        
       $deal = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(deals_detail.amount+deals_detail.penalty),0) AS `deal_amount`, 
                                                     client_loan_schedule.remaining_root+client_loan_schedule.root - client_loan_schedule_deal.cur_root - (   SELECT IFNULL(SUM(money_transactions_detail.pay_root),0)
																																								FROM   money_transactions 
																																								JOIN   money_transactions_detail ON money_transactions_detail.transaction_id = money_transactions.id
																																						        WHERE  money_transactions_detail.actived = 1 AND money_transactions.client_id = $local_id
																																								AND    money_transactions_detail.`status` IN(1,13)
																																								AND    DATE(money_transactions_detail.pay_datetime) > client_loan_schedule_deal.pay_date) AS remaining_root
                                              FROM   client_loan_schedule_deal
                                              JOIN   deals_detail ON deals_detail.deals_id = client_loan_schedule_deal.id
                                              JOIN   client_loan_schedule ON client_loan_schedule.id = client_loan_schedule_deal.schedule_id
                                              JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                                              WHERE  client_loan_agreement.actived = 1 AND client_loan_schedule.actived = 1 
                                              AND    client_loan_schedule_deal.actived=1 AND client_loan_schedule_deal.deal_status != 2 
                                              AND    deals_detail.actived = 1 AND deals_detail.`status` = 0
                                              AND    client_loan_agreement.client_id = '$local_id'"));
       
        if (mysql_num_rows($check_count)>1) {
            
            $res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                                   FROM    money_transactions_detail
                                                   JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                   JOIN    client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                                   WHERE   client_loan_agreement.client_id = '$local_id'
                                                   AND     money_transactions_detail.`status` = 3
                                                   AND     money_transactions_detail.actived = 1"));
           
            $pay_amount = round($remaining_root + $rercent + $penalty + $sakomisio + $nasargeblebi - $res1[pay_amount] + $deal[deal_amount], 2);
           
            $data = array('pay_amount' => $pay_amount, 'root' => $remaining_root, 'percent' => $rercent, 'deal_amount' => $deal[deal_amount], 'penalty' => $penalty, 'pay_amount1' => $res_avans[pay_amount], 'nasargeblebebi' => $nasargeblebi, 'sakomisio' => $sakomisio, 'reg_pledge' => $pledge, 'reg_other' => $other_pay);
        }else{
            
            $res_pledge = mysql_fetch_assoc(mysql_query("SELECT  SUM(IFNULL(CASE
                                                						WHEN money_transactions_detail.currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2)
                                                						WHEN money_transactions_detail.currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount,2)
                                                    				 END,0)) AS peg_pledge,
                                                                 money_transactions.pay_datetime
                                                        FROM     money_transactions
                                                        JOIN     money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                        JOIN     client_loan_agreement ON money_transactions.agreement_id = client_loan_agreement.id
                                                        WHERE    money_transactions.client_id = '$local_id'
                                                        AND      money_transactions_detail.`status` = 7 AND money_transactions.type_id = 2
                                                        AND      money_transactions.actived = 1 AND money_transactions_detail.actived = 1
                                                         "));
            
            $res_pledge1= mysql_fetch_assoc(mysql_query("SELECT  SUM(IFNULL(CASE
                                                						WHEN money_transactions_detail.currency_id = 2 THEN ROUND(money_transactions_detail.pay_amount*money_transactions_detail.course,2)
                                                						WHEN money_transactions_detail.currency_id = 1 THEN ROUND(money_transactions_detail.pay_amount,2)
                                                    				 END,0)) AS peg_pledge,
                                                                 money_transactions.pay_datetime
                                                        FROM     money_transactions
                                                        JOIN     money_transactions_detail ON money_transactions.id = money_transactions_detail.transaction_id
                                                        JOIN     client_loan_agreement ON money_transactions.agreement_id = client_loan_agreement.id
                                                        WHERE    money_transactions.client_id = '$local_id'
                                                        AND      money_transactions_detail.`status` = 8 AND money_transactions.type_id = 2
                                                        AND      money_transactions.actived = 1 AND money_transactions_detail.actived = 1"));
            
            $pledge = $res_pledge[peg_pledge] - $res_pledge1[peg_pledge];
            
            $res = mysql_query("SELECT   client_loan_schedule.id,
                        				 client_loan_agreement.status AS st,
                        				 client_loan_schedule.schedule_date AS pay_date,
                        				 client_loan_schedule.`status`,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN 0
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.percent,2)
                                         END AS percent,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2)
                                         END AS remaining_root,
                                         0 AS nasargeblebi_dgeebi,
                        				 DATEDIFF('$pay_datee', client_loan_schedule.schedule_date) AS gadacilebuli,
                                         DATEDIFF('$pay_datee', client_loan_schedule.schedule_date) AS gadacilebuli_nas,
                                         client_loan_schedule.remaining_root as check_remaining_root,
                                         client_loan_agreement.loan_beforehand_percent,
                        				 client_loan_agreement.penalty_days,
                        				 client_loan_agreement.penalty_percent,
                        				 client_loan_agreement.penalty_additional_percent,
                                         client_loan_agreement.grace_period_caunt
                                FROM     client_loan_schedule
                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date <= '$pay_datee'
                                ORDER BY client_loan_schedule.id DESC
                                LIMIT 1");
            
            if (mysql_num_rows($res)==0) {
                $res = mysql_query("SELECT   client_loan_schedule.id,
                        				 client_loan_agreement.status AS st,
                        				 client_loan_schedule.schedule_date AS pay_date,
                        				 client_loan_schedule.`status`,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN 0
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.percent,2)
                                         END AS nasargeblebi_dgeebi,
                                         0 AS percent,
                                         CASE
                                             WHEN client_loan_schedule.`status` = 1 THEN client_loan_schedule.remaining_root
                        					 WHEN client_loan_schedule.`status` = 0 THEN ROUND(client_loan_schedule.root + client_loan_schedule.remaining_root,2)
                                         END AS remaining_root,
                                         
                        				 DATEDIFF('$pay_datee', IF(client.sub_client > 0 AND client_loan_schedule.number = 1,DATE(client_loan_agreement.datetime),client_loan_schedule.schedule_date)) AS gadacilebuli_nas,
                                         DATEDIFF('$pay_datee', client_loan_schedule.schedule_date) AS gadacilebuli,
                                         client_loan_schedule.remaining_root as check_remaining_root,
                                         client_loan_agreement.loan_beforehand_percent,
                        				 client_loan_agreement.penalty_days,
                        				 client_loan_agreement.penalty_percent,
                        				 client_loan_agreement.penalty_additional_percent,
                                         client_loan_agreement.grace_period_caunt
                                FROM     client_loan_schedule
                                JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                JOIN     client ON client_loan_agreement.client_id = client.id
                                WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.schedule_date >= '$pay_datee'
                                ORDER BY client_loan_schedule.id ASC
                                LIMIT 1");
            }
            
            $res1 = mysql_query("SELECT   client_loan_schedule.percent/30 AS `percent`
                                 FROM     client_loan_schedule
                                 JOIN     client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                                 WHERE    client_loan_agreement.client_id = '$local_id' AND client_loan_schedule.actived = 1 AND client_loan_schedule.schedule_date >= '$pay_datee'
                                 ORDER BY client_loan_schedule.id ASC
                                 LIMIT 1");
            
            $result  = mysql_fetch_assoc($res);
            $result1 = mysql_fetch_assoc($res1);
            
            $remainig_root = $result[remaining_root];
            
            if ($remainig_root == '0.00') {
                $remainig_root = $deal[remaining_root];
            }
            
            $gadacilebuli_day_count = $result[gadacilebuli];
            
            $check_holliday_day = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS count
                                                                 FROM   holidays
                                                                 WHERE  actived = 1
                                                                 AND    DATE(date)>='$result[pay_date]'
                                                                 AND    DATE(date)< '$pay_datee'"));
            
            $gadacilebuli_day_count = $gadacilebuli_day_count - $check_holliday_day[count];
            
            $penalty = 0;
            if ($gadacilebuli_day_count<=$res[grace_period_caunt]) {
                $penalty = 0;
            }else{
                $gadacilebuli_day_count=$gadacilebuli_day_count - $res[grace_period_caunt];
                if ($gadacilebuli_day_count>0 && $gadacilebuli_day_count<=$result[penalty_days] && $result[status] == 0) {
                    $penalty = round(($remainig_root * ($result[penalty_percent]/100))*$gadacilebuli_day_count,2);
                }elseif ($gadacilebuli_day_count>0 && $gadacilebuli_day_count>$result[penalty_days] && $result[status] == 0){
                    $penalty = round(round(($remainig_root * ($result[penalty_percent]/100))*$result[penalty_days],2)+round(($remainig_root * ($result[penalty_additional_percent]/100))*($gadacilebuli_day_count-$result[penalty_days]),2),2);
                }
            }
            $sakomisio      = '0.00';
            $nasargeblebi   = $result[nasargeblebi_dgeebi];
            
            if ($result[check_remaining_root] > 0){
                $sakomisio    = round($remainig_root * ($result[loan_beforehand_percent]/100),2);
//                 if ($result[gadacilebuli]>=0) {
//                     $nasargeblebi = round($result1[percent]*$result[gadacilebuli],2);
//                 }

                if ($result[gadacilebuli_nas]>=0) {
                    $nasargeblebi = round($result1[percent]*$result[gadacilebuli_nas],2);
                }
                
            }
            
        
            if ($result[st] == 1){
                $res1 = mysql_fetch_assoc(mysql_query("SELECT  IFNULL(SUM(money_transactions_detail.pay_amount),0) AS pay_amount
                                                       FROM    money_transactions_detail
                                                       JOIN    money_transactions ON money_transactions.id = money_transactions_detail.transaction_id
                                                       LEFT JOIN client_loan_agreement ON client_loan_agreement.id = money_transactions.agreement_id
                                                       WHERE   client_loan_agreement.client_id = '$local_id' 
                                                       AND     money_transactions_detail.`status` = 3
                                                       AND     money_transactions_detail.actived = 1"));
            
                $pay_amount = round($remainig_root + $result['percent'] + $penalty + $sakomisio + $nasargeblebi-$res1[pay_amount]+$deal[deal_amount], 2);
            
                $data = array('pay_amount' => $pay_amount, 'root' => $remainig_root, 'percent' => $result[percent], 'deal_amount' => $deal[deal_amount], 'penalty' => $penalty, 'pay_amount1' => $res1[pay_amount], 'nasargeblebebi' => $nasargeblebi, 'sakomisio' => $sakomisio, 'reg_pledge' => $pledge, 'reg_other' => $other_pay);
            }else{
                global  $error;
                $error = 'ხელშეკრულება არ არის გააქტიურებული';
            }
        }
    
        break;
        
    case 'cancel_loan':
        $hidde_idd	= $_REQUEST[hidde_id];
        $res = mysql_fetch_array(mysql_query("SELECT loan_currency.`name`
                                              FROM   client_loan_agreement
                                              JOIN   loan_currency ON loan_currency.id = client_loan_agreement.loan_currency_id
                                              WHERE  client_id = '$hidde_idd'"));
        $page = '<div id="dialog-form">
                    <fieldset>
                        <table class="dialog-form-table" style="width: 100%;">
                           <tr>
                               <td colspan="3" style="width: 120px;"><label>სესხის ვალუტა: '.$res[name].'</label></td>
                           </tr>
                           <tr>
                               <td style="width: 120px;"><label>აირჩიე თარიღი</label></td>
                               <td style="width: 115px;"><input id="reregistering_date" class="idle" style="width: 100px;" type="text" value=""></td>
                               <td colspan="2"><button id="check_reregistering_ltd">შემოწმება</button></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>სულ შესატანი</label></td>
                               <td ><input id="reregistering_fee" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                               <td cospan="2"><input id="reregistering_avans" class="idle" style="width: 79px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>ძირი თანხა</label></td>
                               <td cospan="3"><input id="reregistering_root_fee" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>პროცენტი</label></td>
                               <td colspan="3"><input id="reregistering_percent_fee" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>შეთანხმება</label></td>
                               <td colspan="3"><input id="reregistering_deal_fee" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>ჯარიმა</label></td>
                               <td colspan="3"><input id="reregistering_penalty_fee" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>საკომისიო</label></td>
                               <td colspan="3"><input id="reregistering_sakomiso" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>ნასარგებლები<br>დღეები</label></td>
                               <td colspan="3"><input id="reregistering_nasargeblebi" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>დაზღვევა</label></td>
                               <td colspan="3"><input id="reregistering_pledge" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                           <tr style="height:10px;"></tr>
                           <tr>
                               <td style="width: 120px;"><label>სხვა ხარჯი</label></td>
                               <td colspan="3"><input id="reregistering_other" class="idle" style="width: 100px;" type="text" value="" disabled="disabled"></td>
                           </tr>
                       </table>
                    </fieldset>
                </div>';
            $data = array('page' => $page);
        break;
        
    case 'get_loan_schedule':
        $local_id  = $_REQUEST[local_id];
        $pay_datee = $_REQUEST[pay_datee];
        
        $data = array('page' => get_calculation_page($local_id, $pay_datee));
        
        break;
        
    case 'get_difference':
        $user_id = $_SESSION['USERID'];
        $res = mysql_query("SELECT client.id AS client_id,
                                   client_loan_schedule.id AS schedule_id,
        			               client_loan_agreement.exchange_rate AS start_cource,
                        		  (SELECT cource FROM cur_cource WHERE actived = 1 AND DATE(datetime) = DATE(NOW())) AS end_cource,
                                   CASE
                                      WHEN client_loan_agreement.loan_currency_id = 1 THEN ROUND(((client_loan_schedule.remaining_root/client_loan_agreement.exchange_rate) - (client_loan_schedule.remaining_root/(SELECT cource FROM cur_cource WHERE actived = 1 AND DATE(datetime) = DATE(NOW())))),2)
                                      WHEN client_loan_agreement.loan_currency_id = 2 THEN ROUND(((client_loan_schedule.remaining_root*client_loan_agreement.exchange_rate) - (client_loan_schedule.remaining_root*(SELECT cource FROM cur_cource WHERE actived = 1 AND DATE(datetime) = DATE(NOW())))),2)
                        		   END AS difference,
                        		   client_loan_schedule.remaining_root
                             FROM  client
                             JOIN  client_loan_agreement ON client_loan_agreement.client_id = client.id
                             JOIN  client_loan_schedule ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                             WHERE client.actived = 1 AND client_loan_agreement.`status` = 1 AND client_loan_agreement.canceled_status = 0
                             AND   client_loan_schedule.actived = 1 AND DATE_FORMAT(client_loan_schedule.schedule_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
                             AND   client_loan_schedule.remaining_root > 0");
        
        while ($row = mysql_fetch_array($res)) {
            $check = mysql_num_rows(mysql_query("SELECT id
                                                 FROM  `difference_cource`
                                                 WHERE  cliet_loan_schedule_id = $row[schedule_id]"));
            if ($check == 0) {
                mysql_query("INSERT INTO `difference_cource` 
                    					(`user_id`, `datetime`, `client_id`, `cliet_loan_schedule_id`, `start_cource`, `end_cource`, `remaining_root`, `difference`, `actived`) 
                    		      VALUES 
                    		            ('$user_id', NOW(), '$row[client_id]', '$row[schedule_id]', '$row[start_cource]', '$row[end_cource]', '$row[remaining_root]', '$row[difference]', '1')");
            }
            
        }
        
        break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

function get_calculation_page($id, $date){
    $req = mysql_query("SELECT client_loan_schedule.id,
                               client_loan_schedule.schedule_date AS name 
                        FROM   client_loan_schedule
                        JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                        WHERE  client_loan_agreement.client_id = '$id' AND client_loan_schedule.schedule_date <= '$date'
                        AND    client_loan_schedule.`status` = 0 AND client_loan_schedule.actived = 1");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        $data .= '<option value="' . $res['id'] . '" >' . $res['name'] . '</option>';
    }
    return $data;
}

function loan_type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   loan_type");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function agreement_type($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   agreement_type");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function loan_currency($id){
    $req = mysql_query("SELECT id,
                              `name`
                        FROM   loan_currency");

    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function getresponsible($id){
    $req = mysql_query("SELECT id,`name`
                        FROM `user_info`
                        WHERE NOT ISNULL(user_info.trust_number)");

    $data .= '<option value="0" selected="selected">----</option>';
    while( $res = mysql_fetch_assoc($req)){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    return $data;
}

function check_sub_client($id){
    $res=mysql_fetch_assoc(mysql_query("SELECT IF(ISNULL(sub_client),0,sub_client) AS `sub_client`
                                        FROM  `client` 
                                        WHERE  id = '$id'
                                        LIMIT 1;"));
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
                        <th style="width: 6%;">სესხის<br>გაცემა<br>ლარი</th>
                        <th style="width: 7%;">სესხის<br>გაცემა<br>დოლარი</th>
                        <th style="width: 6%;">დარჩენ.<br>ძირი<br>ლარი</th>
                        <th style="width: 6%;">დარჩენ.<br>ძირი<br>დოლარი</th>
                        <th style="width: 6%;">დარიცხვა%<br>ლარი</th>
                        <th style="width: 6%;">დარიცხვა%<br>დოლარი</th>
                        <th style="width: 6%;">გადახდა%<br>ლარი</th>
                        <th style="width: 6%;">გადახდა%<br>დოლარი</th>
                        <th style="width: 6%;">ძირის<br>გადახდა<br>ლარი</th>
                        <th style="width: 6%;">ძირის<br>გადახდა<br>დოლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>ლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>დოლარი</th>
                        <th style="width: 6%;">კურსთა<br>შორისი<br>სხვაობა</th>
            
                        <th style="width: 6%;">დარიცხ.<br>დაზღვევა<br>ლარი</th>
                        <th style="width: 6%;">დარიცხ.<br>დაზღვევა<br>დოლარი</th>
                        <th style="width: 6%;">გადახდა<br>დაზღვევა<br>ლარი</th>
                        <th style="width: 6%;">გადახდა<br>დაზღვევა<br>დოლარი</th>
                        <th style="width: 6%;">დაზღვევა<br>ვალდებ.<br>ლარი</th>
                        <th style="width: 6%;">დაზღვევა<br>ვალდებ.<br>დოლარი</th>
                        
                        <th style="width: 6%;">დარიცხვა<<br>>სხვა<br>ხარჯი</th>
                        <th style="width: 6%;">გადახდა<br>სხვა<br>ხარჯი</th>
                        <th style="width: 6%;">ვალდებ.<br>სხვა<br>ხარჯი</th>
            
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
                        <th id="gacema_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="gacema_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="darchenili_vali" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="darchenili_vali1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="daricxva_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="daricxva_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="procenti_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="procenti_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="dziri_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="dziri_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="remaining_root_gel" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="delta_cource" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_fee" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_fee1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_payed" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_payed1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_delta" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_delta1" style="text-align: left; font-weight: bold;">&nbsp;</th>
            
                        <th id="other" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="other1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="other_delta" style="text-align: left; font-weight: bold;">&nbsp;</th>
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
                        <th style="width: 6%;">სესხის<br>გაცემა<br>ლარი</th>
                        <th style="width: 6%;">დარჩენ.<br>ძირი<br>დოლარი</th>
                        <th style="width: 6%;">დარჩენ.<br>ძირი<br>ლარი</th>
                        <th style="width: 6%;">დარიცხვა%<br>დოლარი</th>
                        <th style="width: 6%;">დარიცხვა%<br>ლარი</th>
                        <th style="width: 6%;">გადახდა%<br>დოლარი</th>
                        <th style="width: 6%;">გადახდა%<br>ლარი</th>
                        <th style="width: 6%;">ძირის<br>გადახდა<br>დოლარი</th>
                        <th style="width: 6%;">ძირის<br>გადახდა<br>ლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>დოლარი</th>
                        <th style="width: 6%;">ვალდე-<br>ბულება<br>ლარი</th>
                        <th style="width: 6%;">კურსთა<br>შორისი<br>სხვაობა</th>
            
                        <th style="width: 6%;">დარიცხ.<br>დაზღვევა<br>ლარი</th>
                        <th style="width: 6%;">დარიცხ.<br>დაზღვევა<br>დოლარი</th>
                        <th style="width: 6%;">გადახდა<br>დაზღვევა<br>ლარი</th>
                        <th style="width: 6%;">გადახდა<br>დაზღვევა<br>დოლარი</th>
                        <th style="width: 6%;">დაზღვევა<br>ვალდებ.<br>ლარი</th>
                        <th style="width: 6%;">დაზღვევა<br>ვალდებ.<br>დოლარი</th>
            
                        <th style="width: 6%;">დარიცხვა<<br>>სხვა<br>ხარჯი</th>
                        <th style="width: 6%;">გადახდა<br>სხვა<br>ხარჯი</th>
                        <th style="width: 6%;">ვალდებ.<br>სხვა<br>ხარჯი</th>
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
                        <th id="gacema_lari" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="gacema_lari1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="darchenili_vali" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="darchenili_vali1" style="text-align: left; font-weight: bold;">&nbsp;</th>
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
                        <th id="insurance_fee1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_payed" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_payed1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_delta" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="insurance_delta1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="other" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="other1" style="text-align: left; font-weight: bold;">&nbsp;</th>
                        <th id="other_delta" style="text-align: left; font-weight: bold;">&nbsp;</th>
                    </tr>
                </tfoot>';
    }
    
    $req_deal = mysql_query("SELECT DATE(deals_detail.pay_date) AS `date`,
                                    ROUND(deals_detail.amount+deals_detail.root,2) AS `amount`,
                                    deals_detail.penalty,
                                    IF(deals_detail.status=1,'გადახდილი','გადასახდელი') AS `status`
                             FROM  client_loan_schedule
                             JOIN  client_loan_schedule_deal ON client_loan_schedule_deal.schedule_id = client_loan_schedule.id
                             JOIN  deals_detail ON deals_detail.deals_id = client_loan_schedule_deal.id
                             JOIN  client_loan_agreement ON client_loan_agreement.id = client_loan_schedule.client_loan_agreement_id
                             AND   client_loan_agreement.client_id = '$id' AND client_loan_schedule_deal.actived = 1 AND deals_detail.actived = 1");
    
    while ($row_deal = mysql_fetch_assoc($req_deal)){
        
        $dat_deal.='<tr style="width:100%; border: 1px solid #000; background: #e0e0e0;">
                       <td style="width:30%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row_deal[date].'<label></td>
                       <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row_deal[amount].'</label></td>
                       <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row_deal[penalty].'</label></td>
                       <td style="width:30%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row_deal[status].'</label></td>
                    </tr>';
    }
    
    
    $req = mysql_query(" SELECT client_loan_schedule.number,
                                DATE_FORMAT(client_loan_schedule.schedule_date, '%d/%m/%Y') AS schedule_date,
                                client_loan_schedule.root,
                                client_loan_schedule.percent,
                                client_loan_schedule.pay_amount,
                                client_loan_schedule.remaining_root,
                                client_loan_schedule.status,
                                client_loan_schedule.schedule_date AS Sched_date,
                                client_loan_schedule.activ_status
                         FROM   client_loan_schedule
                         JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                         WHERE  client_loan_agreement.client_id = $id AND client_loan_schedule.actived=1");
    
    while ($row = mysql_fetch_assoc($req)){
        $sum_percent += $row[percent];
        $sum_P       += $row[pay_amount];
        
        $color        = "";
        
         $curdate=date("Y-m-d");
          
         if ($row[Sched_date] <= $curdate && ($row[activ_status]==0 || $row[status] == 1)) {
            $color = 'background: #4CAF50;';
         }
        
        $dat.='<tr style="width:100%; border: 1px solid #000; '.$color.'">
                    <td style="width:5%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[number].'<label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[schedule_date].'</label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[pay_amount].'</label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[root].'</label></td>
                    <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row[percent].'<label></td>
                    <td style="width:19%;"><label style="font-size: 12px; text-align:center;">'.$row[remaining_root].'</label></td>
                </tr>';
    }
    
  $res = mysql_fetch_assoc(mysql_query("SELECT  IF(ISNULL(client.sub_client) OR ISNULL(client.sub_client),0,client.sub_client) AS sub_client,
                                                client_loan_agreement.loan_months,
                                				client_loan_agreement.loan_amount,
                                				client_loan_agreement.percent,
                                                CONCAT(client_loan_agreement.penalty_percent,' -',client_loan_agreement.penalty_days,'დღე+ ', client_loan_agreement.penalty_additional_percent) AS penalty_info,
			                                    IF(ISNULL(client_loan_agreement.loan_beforehand_percent),0,client_loan_agreement.loan_beforehand_percent) AS loan_beforehand_percent,
                                				DATE_FORMAT(client_loan_agreement.datetime,'%m') AS `month_id`,
                                				DATE_FORMAT(client_loan_agreement.datetime,'%Y') AS `year`,
                                				DATE_FORMAT(client_loan_agreement.datetime,'%d') AS `day`,
                                				CONCAT(client.`name`,' ',client.lastname) AS `name`,
                                                client_loan_agreement.loan_type_id,
                                                client_loan_agreement.canceled_status,
                                                client_loan_agreement.loan_currency_id,
                                                loan_currency.name AS loan_name,
                                                CONCAT(' / ',client_car.car_marc,' / ',client_car.registration_number, ' / ', 
                                                CASE
                            						 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
                            						 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
                                                     WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
                                                     WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
                                			    END,
                                                ' / ორისის კოდი:', client_loan_agreement.oris_code) AS cl_car_info,
                                                client.letter_comment,
                                                client.id AS cl_hidde_id
                                        FROM `client_loan_agreement`
                                        JOIN  client ON client.id = client_loan_agreement.client_id
                                        JOIN loan_currency ON loan_currency.id = client_loan_agreement.loan_currency_id
                                        JOIN client_car ON client_car.client_id = client.id
                                        WHERE client.actived = 1 AND client.id = '$id'"));
  if ($res[sub_client] > 0) {
      $dis = '';
      $res1 = mysql_fetch_assoc(mysql_query(" SELECT client_loan_agreement.loan_months,
                                                     client_loan_agreement.loan_amount,
                                                     client_loan_agreement.percent,
                                                     CONCAT(client_loan_agreement.penalty_percent,' -',client_loan_agreement.penalty_days,'დღე+ ', client_loan_agreement.penalty_additional_percent) AS penalty_info,
			                                         IF(ISNULL(client_loan_agreement.loan_beforehand_percent),0,client_loan_agreement.loan_beforehand_percent) AS loan_beforehand_percent,
                                                     DATE_FORMAT(client_loan_agreement.datetime,'%m') AS `month_id`,
                                                     DATE_FORMAT(client_loan_agreement.datetime,'%Y') AS `year`,
                                                     DATE_FORMAT(client_loan_agreement.datetime,'%d') AS `day`,
                                                     CONCAT(client.`name`,' ',client.lastname) AS `name`,
                                                     client_loan_agreement.loan_type_id,
                                                     client_loan_agreement.loan_currency_id,
                                                     loan_currency.name AS loan_cource
                                              FROM  `client_loan_agreement`
                                              JOIN   client ON client.id = client_loan_agreement.client_id
                                              JOIN   loan_currency ON loan_currency.id = client_loan_agreement.loan_currency_id
                                              WHERE  client.actived = 1 AND client.id = '$res[sub_client]'"));
      
      $req1 = mysql_query("SELECT client_loan_schedule.number,
                                  DATE_FORMAT(client_loan_schedule.schedule_date, '%d/%m/%Y') AS schedule_date,
                                  client_loan_schedule.root,
                                  client_loan_schedule.percent,
                                  client_loan_schedule.pay_amount,
                                  client_loan_schedule.remaining_root,
                                  client_loan_schedule.status,
                                  client_loan_schedule.schedule_date AS Sched_date,
                                  client_loan_schedule.activ_status
                           FROM   client_loan_schedule
                           JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                           WHERE  client_loan_agreement.client_id = '$res[sub_client]' AND client_loan_schedule.actived=1");
      
      while ($row1 = mysql_fetch_assoc($req1)){
          $sum_percent1 += $row1[percent];
          $sum_P1       += $row1[pay_amount];
      
          $color1        = "";
          $curdate=date("Y-m-d");
          
          if ($row1[Sched_date] <= $curdate && $row1[activ_status]==0) {
              $color1 = 'background: #4CAF50;';
          }
      
          $dat1.='<tr style="width:100%; border: 1px solid #000; '.$color1.'">
                        <td style="width:5%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[number].'<label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[schedule_date].'</label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[pay_amount].'</label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[root].'</label></td>
                        <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">'.$row1[percent].'<label></td>
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
  

  
  
 $data = '<div id="dialog-form" style="overflow-y: scroll; height: 480px;">
                <fieldset>
                    <div style="width:100%; font-size: 14px;">
                        <table style="width:100%;">
                            <tr style="width:100%;">
                                <td style="width:7%;"><label style="font-size: 14px;">კლიენტის:<label></td>
                                <td style="width:60%;"><label style="font-size: 14px;">'.$res[name].$res[cl_car_info].'</label></td>
                                <td style="width:15%;"><label style="font-size: 14px;">სესხის ვალუტა:</label></td>
                                <td style="width:18%;"><label style="font-size: 14px;">'.$res[loan_name].'</label></td>
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
                                                <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">ჯარიმის პირობები:<label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res1[penalty_info].'</label></td>
                                                <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                                <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">წელი</label></td>
                                                <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res1[year].'</label></td>
                                            </tr>
                                            <tr style="width:100%;border: 1px solid #000;">
                                                <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">წინსწ. დაფარვის საკომისიო:<label></td>
                                                <td colspan="4" style="width:20%;"><label style="font-size: 12px;"><label>'.$res1[loan_beforehand_percent].'</td>
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
                                                <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თვე</label></td>
                                                <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">ანუიტეტი</label></td>
                                                <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">ძირი<label></td>
                                                <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">პროცენტი</label></td>
                                                <td style="width:19%;"><label style="font-size: 12px; text-align:center;">ნაშთი</label></td>
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
                                            <td style="width:30%;border-right: 1px solid #000;"><label style="font-size: 12px;">ჯარიმის პირობები:<label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;">'.$res[penalty_info].'</label></td>
                                            <td style="width:20%;border-right: 1px solid #000;"><label style="font-size: 12px;"></label></td>
                                            <td style="width:15%;border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">წელი</label></td>
                                            <td style="width:15%;"><label style="font-size: 12px; text-align:center;">'.$res[year].'</label></td>
                                        </tr>
                                        <tr style="width:100%;border: 1px solid #000;">
                                            <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px;">წინსწ. დაფარვის საკომისიო:<label></td>
                                            <td colspan="4" style="width:20%;"><label style="font-size: 12px;">'.$res[loan_beforehand_percent].'<label></td>
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
                                            <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">ანუიტეტი</label></td>
                                            <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">ძირი<label></td>
                                            <td style="width:19%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">პროცენტი</label></td>
                                            <td style="width:19%;"><label style="font-size: 12px; text-align:center;">ნაშთი</label></td>
                                        </tr>';
                            $data.=$dat;
                            $data.='
                                </table>
                            </div> 
                      </td>
                  </tr>
                  <tr style="height:10px;"></tr>
                  <tr>
                       <td>
                           <label>კომენტარი</label>
                       </td>
                  </tr>
                  <tr>
                       <td>
                           <textarea class="idle" id="letter_comment" style="resize: vertical;width: 100%;height: 40px;">'.$res['letter_comment'].'</textarea>
                       </td>
                  </tr>
                  
                </table>
               </fieldset>
               
               <fieldset>
                   <legend>შეთანხმება</legend>
                   <table style="width:500px;">
                         <tr style="width:100%; border: 1px solid #000; background: #e0e0e0;">
                            <td style="width:30%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თარიღი<label></td>
                            <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">თანხა</label></td>
                            <td style="width:20%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">ჯარიმა</label></td>
                            <td style="width:30%; border-right: 1px solid #000;"><label style="font-size: 12px; text-align:center;">სტატუსი</label></td>
                         </tr>
                         '.$dat_deal.'
                   </table>
               </fieldset>
        	   <fieldset>
                    <legend>ბარათი</legend>
                   <table class="display" id="table_letter">
                       '.$tables.'
                   </table>
               </fieldset>
        <input type="hidden" id="id" value="' . $id . '" />
        <input type="hidden" id="cl_iddd" value="' . $id . '" />
        <input type="hidden" id="hidde_cl_id" value="' .$res[cl_hidde_id]. '" />
        <input type="hidden" id="loan_currency_id" value="' . $res[loan_currency_id] . '" />
        <input type="hidden" id="canceled_status" value="' . $res[canceled_status] . '" />
    </div>
    ';
    return $data;
}


function test ($id,$ch){
    
    $sub_client_id = mysql_fetch_array(mysql_query("SELECT IFNULL(sub_client,0) AS sub_client
                                                    FROM   client
                                                    WHERE  client.id = $id"));
    if($ch == 0){
        $fa[] = $id;
    }else{
        $fa[] = $sub_client_id[sub_client];
    }
    
    if($sub_client_id[sub_client]>0){
        test1 ($sub_client_id[sub_client],1);
    }else{
        return $fa;
    }
}

function test1 ($id,$ch){
    test ($id,$ch);
}


?>
