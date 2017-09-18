<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
	case 'get_add_page':
	    $status	= $_REQUEST['status'];
	    
	    if ($status == 1) {
	        $check = mysql_num_rows(mysql_query("SELECT cur_cource.id
                                                 FROM   cur_cource
                                                 WHERE  cur_cource.actived = 1 AND DATE(datetime) = CURDATE()
	                                             LIMIT 1"));
	        if ($check == 0) {
	            $page = GetPage();
    		    $data = array('page' => $page);
	        }else {
	            $data = array('page' => 1);
	        }
	        
	    }else{
    		$page		= GetPage();
    		$data		= array('page'	=> $page);
	    }
		break;
	case 'get_edit_page':
		$id		= $_REQUEST['id'];
	    $page		= GetPage(Getcource($id));
        $data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("SELECT   cur_cource.id,
		                                 datetime AS `date`,
                        			     cur_cource.`cource`
                                FROM     cur_cource
                                WHERE    cur_cource.actived = 1");

		$data = array("aaData"	=> array());

		while ( $aRow = mysql_fetch_array( $rResult ) ){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ )
			{
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
	case 'save_cource':
		$id 	     = $_REQUEST['id'];
		$cource      = $_REQUEST['cource'];
		$cource_date = $_REQUEST['cource_date'];

		if ($id == '') {
		    $res = mysql_query("SELECT datetime 
                		        FROM   cur_cource 
                		        WHERE  DATE(datetime) = '$cource_date' 
		                        AND actived = 1");
		    
		    $check = mysql_num_rows($res); 
		    if ($check == 0) {
		        Addcource($cource_date, $cource);
		    }else{
		        global $error;
		        $error = 'მოცემულ თარიღში კურსი უკვე დამატებულია!';
		    }
        }else{
			Savecource($id, $cource_date, $cource);
		}
		
		
		break;
	case 'disable':
		$id	= $_REQUEST['id'];
		DisableHolidays($id);

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

function Addcource($cource_date, $cource){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("INSERT INTO `cur_cource` 
            		        (`user_id`, `datetime`, `cource`, `actived`) 
            		  VALUES 
            		        ('$user_id', '$cource_date', '$cource', 1)");
}

function Savecource($id, $cource_date, $cource){
    
	$user_id = $_SESSION['USERID'];
	mysql_query("UPDATE `cur_cource`
                    SET `user_id`  = '$user_id',
	                    `datetime` = '$cource_date',
                        `cource`   = '$cource'
                 WHERE  `id`       = '$id'");
	
	mysql_query("UPDATE `money_transactions`
        	        SET `course`           = '$cource'
        	     WHERE  DATE(pay_datetime) = '$cource_date'");
	
	mysql_query("UPDATE `money_transactions_detail`
        	        SET `course`           = '$cource'
        	     WHERE  DATE(pay_datetime) = '$cource_date'");
}

function DisableHolidays($id){
	mysql_query("UPDATE `cur_cource`
				 SET    `actived` = 0
				 WHERE  `id`      = $id");
}

function Getcource($id){
	$res = mysql_fetch_assoc(mysql_query("	SELECT  cur_cource.id,
                                    				cur_cource.`cource`,
	                                                cur_cource.datetime
                                            FROM    cur_cource
											WHERE   cur_cource.`id` = $id" ));

	return $res;
}


function GetPage($res = ''){
    
	$data = '  <div id="dialog-form">
            	    <fieldset>
            	    	<table class="dialog-form-table">
	                        <tr>
            					<td style="width: 170px;"><label for="name">თარიღი</label></td>
            					<td>
            						<input type="text" id="cource_date" value="' . $res['datetime'] . '" />
            					</td>
            				</tr>
            			    <tr style="height:10px;"></tr>
            				<tr>
            					<td style="width: 170px;"><label for="name">კურსი</label></td>
            					<td>
            						<input type="text" id="cource" value="' . $res['cource'] . '" />
            					</td>
            				</tr>
            			</table>
            			<!-- ID -->
            			<input type="hidden" id="id" value="' . $res['id'] . '" />
                    </fieldset>
                </div>';
	
	return $data;
}

?>
