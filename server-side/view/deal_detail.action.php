<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
	case 'get_add_page':
		$page = GetPage();
		$data = array('page'	=> $page);

		break;
	case 'get_edit_page':
		$id	  = $_REQUEST['id'];
	    $page = GetPage(GetSchedule($id));
        $data = array('page' => $page);

		break;
	case 'get_list' :
		$count	      = $_REQUEST['count'];
		$hidden	      = $_REQUEST['hidden'];
		$hidde_inc_id = $_REQUEST['hidde_inc_id'];
		 
		$rResult = mysql_query("SELECT id,
                                       DATE(pay_date),
                                       ROUND(amount+root,2),
                                       penalty,
                                       IF(`status` = 0,'გადაუხდელი','გადახდილი') 
                                FROM   deals_detail
                                WHERE  actived = 1 AND deals_id = '$hidde_inc_id'
		                        ORDER BY pay_date DESC ");

		$data = array("aaData"	=> array());

		while ( $aRow = mysql_fetch_array( $rResult ) ){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ ){
				/* General output */
				$row[] = $aRow[$i];
				if($i == ($count - 1)){
				    $row[] = '<div class="callapp_checkbox">
                                  <input type="checkbox" id="callapp_checkbox_'.$aRow[$hidden].'" name="check_'.$aRow[$hidden].'" value="'.$aRow[$hidden].'" class="check" />
                                  <label for="callapp_checkbox_'.$aRow[$hidden].'"></label>
                              </div>';
				}
			}
			$data['aaData'][] = $row;
		}

		break;
		
	case 'save_deal_det':
		$id 		            = $_REQUEST['id'];
		$hidde_inc_id           = $_REQUEST['hidde_inc_id'];
		$deal_amount_payed_date = $_REQUEST['deal_amount_payed_date'];
		$deal_amount            = $_REQUEST['deal_amount'];
		$deal_root_amount       = $_REQUEST['deal_root_amount'];
		$deals_penalty          = $_REQUEST['deals_penalty'];
		$deals_penalty_date     = $_REQUEST['deals_penalty_date'];
		$comment                = $_REQUEST['comment'];
		$user_id                = $_SESSION['USERID'];
		
		if($id==''){
		    mysql_query("INSERT INTO `deals_detail` 
                                    (`user_id`, `datetime`, `deals_id`, `pay_date`, `amount`, `root`, `comment`, `status`, `actived`) 
                              VALUES 
                                    ('$user_id', NOW(), '$hidde_inc_id', '$deal_amount_payed_date', '$deal_amount', '$deal_root_amount', '$comment', 0, 1)");
		}else{
		   mysql_query("UPDATE `deals_detail`
                           SET `user_id`      = '$user_id',
                               `datetime`     =  NOW(),
                               `pay_date`     = '$deal_amount_payed_date',
                               `amount`       = '$deal_amount',
                               `root`         = '$deal_root_amount',
                               `penalty`      = '$deals_penalty',
		                       `penalty_date` = '$deals_penalty_date',
                               `comment`      = '$comment'
                        WHERE  `id`           = '$id'");
		}	    
		
		break;
	case 'disable':
		$id	= $_REQUEST['id'];
		Disable($id);

		break;
	default:
		$error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Category Functions
* ******************************
*/

function Disable($id){

    mysql_query("UPDATE deals_detail
                    SET actived = 0
                 WHERE  id IN($id)
                ");
}

function GetSchedule($id){
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT id,
                                                   DATE(pay_date) AS `pay_date`,
                                                   amount,
                                                   root,
                                                   penalty,
	                                               penalty_date,
                                                   comment
                                            FROM   deals_detail
                                            WHERE  actived = 1 AND id = '$id'"));
    return $res;
}


function GetPage($res = ''){
    
    if ($res[id]=='') {
        $dis='disabled="disabled"';
        $dis1='';
    }else{
        $dis='';
        $dis1='disabled="disabled"';
    }
    
	$data = '<div id="dialog-form">
        	   <fieldset>
    				<table class="dialog-form-table">
	                    <tr>
        					<td style="width: 80px;"><label for="date">თარიღი</label></td>
	                        <td style="width: 260px;">
        						<input style="width: 260px;" id="deal_amount_payed_date" type="text" value="'.$res[pay_date].'">
        					</td>
                	    </tr>
        				<tr style="height:5px;"></tr>
                	    <tr>
	                        <td style="width: 80px;"><label for="date">თანხა</label></td>
        				    <td style="width: 260px;">
        						<input style="width: 260px;" id="deal_amount" type="text" value="'.$res[amount].'">
        					</td>
                	    </tr>
        				<tr style="height:5px;"></tr>
                        <tr>
	                        <td style="width: 80px;"><label for="date">ძირი</label></td>
        				    <td style="width: 260px;">
        						<input style="width: 260px;" id="deal_root_amount" type="text" value="'.$res[root].'">
        					</td>
                	    </tr>
        				<tr style="height:5px;"></tr>
                	    <tr>
	                        <td style="width: 80px;"><label for="date">ჯარიმა</label></td>
        					<td style="width: 260px;">
        						<input style="width: 260px;" id="deals_penalty" type="text" value="'.$res[penalty].'">
        					</td>
	                    </tr>
        				<tr style="height:5px;"></tr>
                	    <tr>
	                        <td style="width: 80px;"><label for="date">დარიცხვის თარიღი</label></td>
        					<td style="width: 260px;">
        						<input style="width: 260px;" id="deals_penalty_date" type="text" value="'.$res[penalty_date].'">
        					</td>
	                    </tr>
	                    <tr style="height:10px;"></tr>
                        <tr>
	                        <td style="width: 80px;"><label for="date">კომენტარი</label></td>
        					<td style="width: 260px;">
        						<textarea class="idle" id="comment" style="resize: vertical;width: 98%;height: 40px;">'.$res['comment'].'</textarea>
        					</td>
	                    </tr>
        			</table>
        		</fieldset>
    			<!-- ID -->
    			<input type="hidden" id="deal_detail_id" value="' . $res['id'] . '" />
    			
    		</div>';
	return $data;
}

?>
