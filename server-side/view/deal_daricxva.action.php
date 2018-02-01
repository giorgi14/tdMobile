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
                                       amount
                                FROM   deals_daricxva
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
		$comment                = $_REQUEST['comment'];
		$user_id                = $_SESSION['USERID'];
		
		if($id==''){
		    mysql_query("INSERT INTO `deals_daricxva` 
                                    (`user_id`, `datetime`, `deals_id`, `pay_date`, `amount`, `comment`, `actived`) 
                              VALUES 
                                    ('$user_id', NOW(), '$hidde_inc_id', '$deal_amount_payed_date', '$deal_amount', '$comment', 1)");
		}else{
		   mysql_query("UPDATE `deals_daricxva`
                           SET `user_id`      = '$user_id',
                               `datetime`     =  NOW(),
                               `pay_date`     = '$deal_amount_payed_date',
                               `amount`       = '$deal_amount',
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

    mysql_query("UPDATE deals_daricxva
                    SET actived = 0
                 WHERE  id IN($id)
                ");
}

function GetSchedule($id){
    
	$res = mysql_fetch_assoc(mysql_query("	SELECT id,
                                                   DATE(pay_date) AS `pay_date`,
                                                   amount,
                                                   comment
                                            FROM   deals_daricxva
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
        						<input style="width: 260px;" id="deal_amount_daricxva_date" type="text" value="'.$res[pay_date].'">
        					</td>
                	    </tr>
        				<tr style="height:5px;"></tr>
                	    <tr>
	                        <td style="width: 80px;"><label for="date">თანხა</label></td>
        				    <td style="width: 260px;">
        						<input style="width: 260px;" id="deal_daricxva_amount" type="text" value="'.$res[amount].'">
        					</td>
                	    </tr>
        				<tr style="height:10px;"></tr>
                        <tr>
	                        <td style="width: 80px;"><label for="date">კომენტარი</label></td>
        					<td style="width: 260px;">
        						<textarea class="idle" id="daricxva_comment" style="resize: vertical;width: 98%;height: 40px;">'.$res['comment'].'</textarea>
        					</td>
	                    </tr>
        			</table>
        		</fieldset>
    			<!-- ID -->
    			<input type="hidden" id="deal_daricxva_id" value="' . $res['id'] . '" />
    			
    		</div>';
	return $data;
}

?>
