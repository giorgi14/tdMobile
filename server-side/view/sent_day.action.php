<?php
require_once('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
 
switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
		$id		= $_REQUEST['id'];
	    $page		= GetPage(Getdays($id));
        $data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("SELECT id,
		                              `name`,
		                               count
                                FROM   sent_day
                                WHERE  actived = 1");

		$data = array("aaData"	=> array());

		while ( $aRow = mysql_fetch_array( $rResult ) )
		{
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
	case 'save_holidays':
		$id    = $_REQUEST['id'];
		$name  = $_REQUEST['name'];
		$count = $_REQUEST['count'];

		if ($id == '') {
            Add($name, $count);
        }else{
			Save($id, $name, $count);
		}
		
		
		break;
	case 'disable':
		$id	= $_REQUEST['id'];
		Disabledays($id);

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

function Add($name,$count){
    
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO `sent_day`
							(`user_id`, `datetime`, `name`, `count`)
					  VALUES 		
	                        ('$user_id', NOW(), '$name', '$count')");
}

function Save($id, $name, $count){
    
	$user_id	= $_SESSION['USERID'];
	mysql_query("UPDATE `sent_day`
	                SET `user_id` = '$user_id',
	                    `name`    = '$name',
						`count`   = '$count'
	             WHERE  `id`      = $id");
}

function Disabledays($id)
{
	mysql_query("	UPDATE `sent_day`
					SET    `actived` = 0
					WHERE  `id` = $id");
}

function Getdays($id)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT  sent_day.id,
                                    				sent_day.`name`,
	                                                sent_day.`count`
                                            FROM    sent_day
											WHERE   sent_day.`id` = $id" ));

	return $res;
}


function GetPage($res = '')
{
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>

	    	<table class="dialog-form-table">
				<tr>
					<td style="width: 170px;"><label for="name">დასახელება</label></td>
					<td>
						<input type="text" id="name" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="date">დღეების რაოდენობა</label></td>
					<td>
						<input type="number" id="caunt"  value="' . $res['count'] . '" />
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
