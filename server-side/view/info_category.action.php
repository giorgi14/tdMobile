<?php
/* ******************************
 *	Category aJax actions
 * ******************************
*/
 
include('../../includes/classes/core.php');
$action	= $_REQUEST['act'];
$error	= '';
$data	= '';
$par_id 		= $_REQUEST['par_id'];
switch ($action) {
	case 'get_add_page':
		$page		= GetPage();
		$data		= array('page'	=> $page);
		
        break;
    case 'get_edit_page':
	    $cat_id		= $_REQUEST['id'];
		$page		= GetPage(GetCategory($cat_id));
        
        $data		= array('page'	=> $page);
        
        break;
 	case 'get_list' :
		$count	= $_REQUEST['count'];
	    $hidden	= $_REQUEST['hidden'];
	    
	    $rResult = mysql_query("SELECT	`info`.`id`,
                                		`info`.`name`,
                                		(SELECT `name` FROM `info_category` WHERE `id` = `info`.`parent_id`)
							    FROM	`info_category` AS `info`
	    						WHERE	`info`.`actived` = 1");
	    
		$data = array(
			"aaData"	=> array()
		);
		
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
    case 'save_category':
		$cat_id 		= $_REQUEST['id'];
		$par_id 		= $_REQUEST['parent_id'];
		
    	$cat_name		= htmlspecialchars($_REQUEST['cat'], ENT_QUOTES);
		
		if($cat_name != '' && $cat_id == ''){
			if(!CheckCategoryExist($cat_name, $par_id)){
				AddCategory($cat_name, $par_id);
			} else {
				$error = '"' . $cat_name . '" უკვე არის სიაში!';
			}
		}else{
			SaveCategory($cat_id, $cat_name, $par_id);
		}
		
        break;
    case 'disable':
		$cat_id	= $_REQUEST['id'];
		DisableCategory($cat_id);
		
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

function AddCategory($cat_name, $par_id){
    
	mysql_query("INSERT INTO `info_category`
					(`name`, `parent_id`) 
				 VALUES
					('$cat_name', '$par_id')");
}

function SaveCategory($cat_id, $cat_name, $par_id)
{
	mysql_query("UPDATE
	    			`info_category`
				 SET
				    `name` = '$cat_name',
				    `parent_id`	= $par_id
				 WHERE
					`id` = $cat_id");
}

function DisableCategory($cat_id)
{
    mysql_query("UPDATE `info_category`
				 SET    `actived` = 0
				 WHERE	`id` = $cat_id");
}

function CheckCategoryExist($cat_name, $par_id) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT `id`
										  FROM   `info_category`
										  WHERE  `name` = '$cat_name' && `parent_id` = $par_id && `actived` = 1"));
	if($res['id'] != ''){
		return true;
	}
	return false;
}
function Get_category($par_id)

{ 			

	$data = '';
	$req = mysql_query("SELECT `id`, `name`
						FROM `info_category`
						WHERE actived=1");


	$data .= '<option value="0" selected="selected">----</option>';
	while( $res = mysql_fetch_assoc($req)){
		if($res['id'] == $par_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}

function Get_Client($id){
    $data = '';
    $req = mysql_query("SELECT `id`,`name`
                        FROM  `client`
                        WHERE `actived` = 1");
    
    
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

function GetCategory($cat_id) 
{
    $res = mysql_fetch_assoc(mysql_query("SELECT `id`,
    											 `name`,
    											 `parent_id`
									      FROM   `info_category`
									      WHERE  `id` = $cat_id" ));
    
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
					<td style="width: 170px;"><label for="category">ქვე კატეგორია</label></td>
					<td>
						<input type="text" id="category" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 170px;"><label for="parent_id">კატეგორია</label></td>
					<td>
						<select id="parent_id" class="idls large">' . Get_Category($res['parent_id'])  . '</select>
					</td>
				</tr>
			</table>
			<!-- ID -->
			<input type="hidden" id="cat_id" value="' . $res['id'] . '" />
        </fieldset>
    </div>
    ';
	return $data;
}

?>
