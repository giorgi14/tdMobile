<?php
/* ******************************
 *	Workers aJax actions
 * ******************************
 */
include('../../includes/classes/core.php');



$action 	= $_REQUEST['act'];
$user_id	= $_SESSION['USERID'];
$error 		= '';
$data 		= '';

switch ($action) {
	case 'get_add_page':
		$page		= GetGroupPage();
		$data		= array('page'	=> $page);
		
		break;
	case 'get_edit_page':
	    $group_id		= $_REQUEST['id'];
		$page		    = GetGroupPage($group_id);
        
        $data		= array('page'	=> $page);
        
	    break;
	case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
		$rResult = mysql_query("SELECT `id`, `name`
								FROM `group`");
		
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
	case 'get_pages_list':
		$count    = $_REQUEST['count'];
		$hidden   = $_REQUEST['hidden'];
		$group_id = $_REQUEST['group_id'];
		

		if(!empty($group_id)){
			$rResult = mysql_query("SELECT    `pages`.`id`,
											  `menu_detail`.`title`,
											   IFNULL(group_permission.page_id,0) AS `check`
									FROM      `pages`
									LEFT JOIN `menu_detail` ON `menu_detail`.`page_id` = `pages`.`id`
									LEFT JOIN `group_permission` ON group_permission.page_id = `pages`.`id` && `group_permission`.`group_id` = $group_id
									WHERE     ((`menu_detail`.`parent` != 0 && menu_detail.url = '#') || (menu_detail.url = '')) AND menu_detail.page_id != 3");
		}else {
			$rResult = mysql_query("SELECT    `pages`.`id`,
    								          `menu_detail`.`title`
    								FROM      `pages`
    								LEFT JOIN `menu_detail` ON `menu_detail`.`page_id` = `pages`.`id`
    								WHERE     (`menu_detail`.`parent` != 0 && menu_detail.url = '#') || (menu_detail.url = '') AND menu_detail.page_id != 3");
		}	
		
		$data = array("aaData"	=> array());
		
		while ( $aRow = mysql_fetch_array( $rResult ) ){
			$row = array();
			for ( $i = 0 ; $i < $count ; $i++ ){
				/* General output */
				$row[] = $aRow[$i];
				if($i == ($count - 1)){
					$check = "";
					if($aRow['check'] != 0){
						$check.="checked";
					}
					$row[] = '<input type="checkbox" name="check_' . $aRow[$hidden] . '" class="check1" value="' . $aRow[$hidden] . '" '.$check.'/>';
				}
			}
			$data['aaData'][] = $row;
		}
						
		break;
	case 'save_group':
		$group_name		= $_REQUEST['nam'];
		$group_pages	= json_decode(stripslashes($_REQUEST['pag']));
		$group_id       = $_REQUEST['group_id'];	

		if(empty($group_id)){
			SaveGroup($group_name, $group_pages);
		}else{
			ClearForUpdate($group_id);
			UpdateGroup($group_id, $group_pages, $group_name);
		}
  		
		break;        
    case 'disable':
		$group_id = $_REQUEST['id'];
		DisableGroup($group_id);
				
        break;           
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Workers Functions
 * ******************************
 */


function SaveGroup($group_name, $group_pages){
	mysql_query("INSERT	INTO `group`
						(`group`.`name`)
				VALUES
						('$group_name')");
	
	$group_id = mysql_insert_id();
	

	
	$parrentaray = array();
	foreach($group_pages as $group_page) {
		mysql_query("INSERT	INTO `group_permission`
						(`group_permission`.`group_id`, `group_permission`.`page_id`)
					VALUES
						('$group_id','$group_page')");

		
		$res = mysql_fetch_assoc( mysql_query("	SELECT		`menu_detail`.`parent` as `parent_id`
												FROM		`pages`
												LEFT JOIN	`menu_detail` ON `menu_detail`.`page_id` = `pages`.`id`
												LEFT JOIN	`menu_detail` as `menu_detail1` ON `menu_detail1`.`id` =  `menu_detail`.`parent`
												WHERE		`pages`.`id` = '$group_page' AND `menu_detail`.`parent` != 0 "));
		if( !in_array($res['parent_id'], $parrentaray) ){
			array_push($parrentaray, $res['parent_id']);		
		}
	}	
	$res = mysql_fetch_assoc( mysql_query("	SELECT	`pages`.`id` as `id`
											FROM	`pages`
											WHERE	`pages`.`name` = 'logout'"));
	mysql_query("INSERT	INTO `group_permission`
					(`group_permission`.`group_id`, `group_permission`.`page_id`)
				VALUES
					('$group_id','$res[id]')");

	
	mysql_query("INSERT	INTO `group_permission`
					(`group_permission`.`group_id`, `group_permission`.`page_id`)
				VALUES
					('$group_id','3')");

	
	foreach($parrentaray as $parrent) {
		mysql_query("INSERT	INTO `group_permission`
						(`group_permission`.`group_id`, `group_permission`.`page_id`)
					VALUES
						('$group_id','$parrent')");

	}
		
}

function UpdateGroup($group_id, $group_pages, $group_name){
	
	mysql_query("UPDATE  `group`
					SET  `name` = '$group_name'
			      WHERE  `id`  = $group_id");
	
	$parrentaray = array();
	foreach($group_pages as $group_page) {
		mysql_query("INSERT	INTO `group_permission`
            		            (`group_permission`.`group_id`, `group_permission`.`page_id`)
            		      VALUES
            		            ('$group_id','$group_page')");
	
	
		$res = mysql_fetch_assoc( mysql_query("	SELECT		`menu_detail`.`parent` as `parent_id`
                                				FROM		`pages`
                                				LEFT JOIN	`menu_detail` ON `menu_detail`.`page_id` = `pages`.`id`
                                				LEFT JOIN	`menu_detail` as `menu_detail1` ON `menu_detail1`.`id` =  `menu_detail`.`parent`
                                				WHERE		`pages`.`id` = '$group_page' AND `menu_detail`.`parent` != 0 "));
		if( !in_array($res['parent_id'], $parrentaray) ){
			array_push($parrentaray, $res['parent_id']);
		}
	}
	$res = mysql_fetch_assoc( mysql_query("	SELECT	`pages`.`id` as `id`
											FROM	`pages`
											WHERE	`pages`.`name` = 'logout'"));
	mysql_query("INSERT	INTO `group_permission`
                        	(`group_permission`.`group_id`, `group_permission`.`page_id`)
                      VALUES
                        	('$group_id','$res[id]')");
	
	
	mysql_query("INSERT	INTO `group_permission`
                        	(`group_permission`.`group_id`, `group_permission`.`page_id`)
                      VALUES
                        	('$group_id','3')");
	
	
	foreach($parrentaray as $parrent) {
		mysql_query("INSERT	INTO `group_permission`
                        		(`group_permission`.`group_id`, `group_permission`.`page_id`)
                          VALUES
                        		('$group_id','$parrent')");
	
	}
	
}



function DisableGroup($group_id)
{
    mysql_query("DELETE FROM `group`
				 WHERE  `id` = '$group_id'");
}	


function GetGroupNameById($group_id){
	$res = mysql_fetch_assoc(mysql_query("SELECT   `name`
							     FROM   `group`
								WHERE   `id` = $group_id"));
	
	return $res['name'];
}


function ClearForUpdate($group_id){
	mysql_query("DELETE FROM group_permission
				 WHERE group_id = $group_id");
}

function GetGroupPage($res = ''){
	
	$data = '
	<div id="dialog-form">
 	    <fieldset>
	    	<legend>ჯგუფი</legend>
			<div style=" margin-top: 2px; ">
				<div style="width: 170px; display: inline;">
					<label for="group_name" style="float: left;">ჯგუფის სახელი :</label>
					<input type="text" id="group_name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" style="display: inline; margin-left: 25px;width: 400px;" value="'.GetGroupNameById($res).'"/>
				</div>
			</div>
        </fieldset>	
 	    <fieldset>
	    	<legend>გვერდები</legend>									
            <div id="dynamic" style="margin-top: 25px;">
                <table class="display" id="pages" style="width: 100% !important; ">
                    <thead>
                        <tr style=" white-space: no-wrap;" id="datatable_header">
                            <th >ID</th> 
                            <th style="width: 315px  !important;">გვერდის სახელი</th>
                            <th style="width: 25px !important;">#</th>   
                        </tr>
                    </thead>
                </table>
            </div>
        </fieldset>						
    </div>

	<input type="hidden" id="group_id" value="' . $res . '" />
			
    ';
	return $data;
}

?>