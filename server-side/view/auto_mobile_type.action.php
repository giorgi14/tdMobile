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
	    $page		= GetPage(Get($id));
        $data		= array('page'	=> $page);

		break;
	case 'get_list' :
		$count	= $_REQUEST['count'];
		$hidden	= $_REQUEST['hidden'];
		 
		$rResult = mysql_query("SELECT  car_type.id,
                        				car_type.`name`
                                FROM    car_type
                                WHERE   car_type.actived = 1");

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
	case 'save_holidays':
		$id 		              = $_REQUEST['id'];
		$name                     = $_REQUEST['name'];

		if($name != ''){
		    if ($id == '') {
                if(!CheckExist($name, $id)){
                    Add($name);
                } else {
				    $error = '"' . $name . '" უკვე არის სიაში!';
				}
			}else {
				Save($id, $name);
			}
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

function Add($name,$date)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("INSERT INTO 	 `car_type`
								(`name`,`user_id`)
					VALUES 		('$name', '$user_id')");
}

function Save($id, $name, $date)
{
	$user_id	= $_SESSION['USERID'];
	mysql_query("	UPDATE `car_type`
					SET     `name` = '$name',
							`user_id` ='$user_id'
					WHERE	`id` = $id");
}

function Disable($id)
{
	mysql_query("	UPDATE `car_type`
					SET    `actived` = 0
					WHERE  `id` = $id");
}

function CheckExist($name)
{
	$res = mysql_fetch_assoc(mysql_query("	SELECT `id`
											FROM   `car_type`
											WHERE  `name` = '$name' && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}


function Get($id){
	$res = mysql_fetch_assoc(mysql_query("	SELECT  car_type.id,
                                    				car_type.`name`
                                            FROM    car_type
											WHERE   car_type.`id` = $id" ));

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
			</table>
			<!-- ID -->
			<input type="hidden" id="id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
