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
		$page		= GetPage();
		$data		= array('page'	=> $page);

		break;
	case 'get_edit_page':
	    $per_id		= $_REQUEST['id'];
		$page		= GetPage(GetWorker($per_id));

        $data		= array('page'	=> $page);

	    break;
	case 'get_list':
	    $count = $_REQUEST['count'];
	    $hidden = $_REQUEST['hidden'];
		$rResult = mysql_query("	SELECT 	  `users`.`id`,
                            				  `user_info`.`name`,
                            				  `user_info`.`tin`,
                            				  `position`.`person_position`,
                            				  `user_info`.`address`				
                                    FROM   	  `user_info` 
                                    LEFT JOIN `position` ON `user_info`.`position_id` = `position`.`id`
                                    JOIN 	  `users` ON `user_info`.`user_id` = `users`.`id`
                                    WHERE  	  `users`.`actived` = 1");

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
    case 'save_pers':
		$persons_id 		= $_REQUEST['id'];
    	$name 				= htmlspecialchars($_REQUEST['n'], ENT_QUOTES);
		$tin 				= $_REQUEST['t'];
		$position 			= $_REQUEST['p'];
		$address 			= htmlspecialchars($_REQUEST['a'], ENT_QUOTES);
		$image				= $_REQUEST['img'];
		$password			= $_REQUEST['pas'];
		$home_number		= $_REQUEST['h_n'];
		$mobile_number		= $_REQUEST['m_n'];
		$comment			= $_REQUEST['comm'];
		$user				= $_REQUEST['user'];
		$userpassword		= md5($_REQUEST['userp']);
		$group_permission	= $_REQUEST['gp'];
		$dep_id             = $_REQUEST['dep_id'];

		$CheckUser 			= CheckUser($user);


		if(empty($persons_id)){
			if($CheckUser){
				AddWorker($user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment,  $user, $userpassword, $group_permission);
				}else{
					$error = "მომხმარებელი ასეთი სახელით  უკვე არსებობს\nაირჩიეთ სხვა მომხმარებლის სახელი";
				}
		}else{
			SaveWorker($persons_id, $user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment,  $user, $userpassword, $group_permission);
		}


        break;
    case 'disable':
		$per_id = $_REQUEST['id'];
		DisableWorker($per_id);

        break;
	case 'delete_image':
		$pers_id 		= $_REQUEST['id'];
		DeleteImage($pers_id);

		break;
	case 'view_img':
	    $page		= GetIMG($_REQUEST[id]);
	    $data		= array('page'	=> $page);
	     
	    break;
	case 'clear':
		$file_list = $_REQUEST['file'];
		ClearProduct();
		if (!empty($file_list)) {
			$file_list = ClearFiles(json_decode($file_list));
		}
		$data = array('file_list' => json_encode($file_list));




    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	Workers Functions
 * ******************************
 */
 
function GetIMG($id){
    $res = mysql_fetch_array(mysql_query("SELECT rand_name FROM `file` WHERE id = $id"));
    if (empty($res[0])) {
        $image = '0.jpg';
    }else{
        $image = $res[0];
    }
    $data = '<div id="dialog-form">
	           <fieldset>
                <img style="margin: auto;display: block;" width="350" height="350"  src="media/uploads/file/'.$image.'">
               </fieldset>
             </div>
            ';

    return $data;
}
function CheckUser($user){
	$res = mysql_query("SELECT `username`
						FROM   `users`
						WHERE  `username` = '$user'");

	if(mysql_num_rows($res) > 0){
		return false;
	}

	return true;
}

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
					('$group_id','31')");


	foreach($parrentaray as $parrent) {
		mysql_query("INSERT	INTO `group_permission`
						(`group_permission`.`group_id`, `group_permission`.`page_id`)
					VALUES
						('$group_id','$parrent')");

	}

	return $group_id;
}

function ClearProduct() {
	$req = mysql_query("SELECT	`id`,
    							`name`
						FROM `persons`");

	while( $res = mysql_fetch_assoc($req)){
		$name = htmlspecialchars($res[name], ENT_QUOTES);

		GLOBAL $log;
		$log->setUpdateLogBefore('persons', $res[id]);

		mysql_query("	UPDATE
		`persons`
		SET
		`name`	= '$name'
		WHERE
		`id`	= '$res[id]'");

		$log->setUpdateLogAfter('persons', $res[id]);
	}
}

function AddWorker($user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment,  $user, $userpassword, $group_permission)
{
    if($user != '' && $userpassword !='' && $group_permission !=''){
        $ext			= $_REQUEST['ext'];
        if(strlen($_REQUEST['userp']) == 32){
    
        }else{
            mysql_query("INSERT	INTO	`users`
                        (`username`,`password`,`group_id`)
                        VALUES
                        ('$user','$userpassword','$group_permission')");
        }
    }
    
    $persons_id = mysql_insert_id();
    
	mysql_query("INSERT INTO `user_info`
					(`user_id`, `name`, `tin`, `position_id`, `address`, `image`, `home_phone`, `mobile_phone`, `comment`)
				 VALUES
					($persons_id, '$name', '$tin', $position, '$address', '$image', '$home_number', '$mobile_number', '$comment')");

}

function SaveWorker($persons_id, $user_id, $name, $tin, $position, $address, $image, $password, $home_number, $mobile_number, $comment, $user, $userpassword, $group_permission)
{
	mysql_query("UPDATE `user_info` SET
                    	`user_id`		= '$persons_id',
                    	`name`			= '$name',
                    	`tin`			= '$tin',
                    	`position_id`	= $position,
                    	`address`		= '$address',
                    	`image`			= '$image',
                    	`home_phone`	= '$home_number',
                    	`mobile_phone`  = '$mobile_number',
                    	`comment`		= '$comment'
                  WHERE `user_id` = $persons_id");

	if( $user!= '' && $userpassword!='' && $group_permission!=''){
		$ext			= $_REQUEST['ext'];
		if(strlen($_REQUEST['userp']) == 32){
		    mysql_query("	UPDATE	`users` SET
                			        `users`.`username` = '$user',
                			        `users`.`group_id` = '$group_permission'
		                    WHERE	`users`.`id` = '$persons_id' AND `users`.actived = 1");
		}else{
		mysql_query("	UPDATE	`users` SET
								`users`.`username` = '$user',
								`users`.`password` = '$userpassword',
								`users`.`group_id` = '$group_permission'
						WHERE	`users`.`id` = '$persons_id'	&& `users`.actived = 1");
		}
	}
}

function DisableWorker($per_id)
{
    mysql_query("UPDATE `users` SET
                        `actived` = 0
				 WHERE  `users`.`id` = '$per_id'");
}

function GetPosition($point)
{
	$data = '';
    $req = mysql_query("SELECT 	`id`,
    						   	`person_position`
						FROM 	`position`
						WHERE 	`actived` = '1'");

	if($point == ''){
		$data = '<option value="0" selected="selected"></option>';
	}

	while( $res = mysql_fetch_assoc( $req )){
		if($res['id'] == $point){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['person_position'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['person_position'] . '</option>';
		}
	}

	return $data;
}

function GetDepart($id){
    $data = '';
    $req = mysql_query("SELECT 	`id`,
    						   	`name`
						FROM 	`department`
						WHERE 	`actived` = '1'");
    
    $data = '<option value="0" selected="selected"></option>';

    while( $res = mysql_fetch_assoc( $req )){
        if($res['id'] == $id){
            $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
        } else {
            $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
        }
    }
    
    return $data;
}

function GetWorker($per_id){
    $res = mysql_fetch_assoc(mysql_query("	SELECT	`users`.`id` as `id`,
                                    				`user_info`.`name` as `name`,
                                    				`user_info`.`tin` as `tin`,
                                    				`user_info`.`position_id` as `position`,
                                    				`user_info`.`address` as `address`,
                                    				`file`.`rand_name` as `image`,
                                                    `file`.`id` as `image_id`,
                                    				`users`.`username` as `username`,
                                    				`users`.`password` as `user_password`,
                                    				`users`.`group_id` as `group_id`,
                                    				`user_info`.`home_phone` as `home_number`,
                                    				`user_info`.`mobile_phone` as `mobile_number`,
                                    				`user_info`.`comment` as `comment`
                                            FROM	`user_info`
                                            LEFT JOIN	`users` ON `users`.`id` = `user_info`.`user_id`
                                            LEFT JOIN	`file` ON `users`.`id` = `file`.`users_id`
                                            WHERE	`user_info`.`user_id` = '$per_id'"));
	return $res;
}

function DeleteImage($pers_id)
{
	mysql_query("UPDATE
	`persons`
	SET
	`image`			= NULL
	WHERE
	`id`			= $pers_id");
}

function GetGroupPermission( $group_id ){
	$data = '';
	$req = mysql_query("SELECT	`group`.id as `id`,
								`group`.`name` as `name`
						FROM	`group`");

	if($group_id == ''){
		$data = '<option value="0" selected="selected"></option>';
	}

	while( $res = mysql_fetch_assoc( $req )){
		if($res['id'] == $group_id){
			$data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
		} else {
			$data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
		}
	}

	return $data;
}

function GetPage($res = '')
{
    
    $image = $res['image'];
	if(empty($image)){
		$image = '0.jpg';
	}else{
	    $disable_img = 'disabled';
	}
	$data = '
	<div id="dialog-form">
	    <fieldset>
	    	<legend>ძირითადი ინფორმაცია</legend>

	    	<table class="dialog-form-table">
				<tr>
					<td style="width: 120px;"><label for="name">სახელი, გვარი</label></td>
					<td>
						<input type="text" id="name" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['name'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 120px;"><label for="tin">პირადი ნომერი</label></td>
					<td>
						<input type="text" id="tin" class="idle user_id" onblur="this.className=\'idle user_id\'" onfocus="this.className=\'activeField user_id\'" value="' . $res['tin'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 120px;"><label for="position">თანამდებობა</label></td>
					<td>
						<select id="position" class="idls" style="width: 233px;">' . GetPosition($res['position']) . '</select>
					</td>
				</tr>
				<tr>
					<td style="width: 120px;"><label for="address">მისამართი</label></td>
					<td>
						<input type="text" id="address" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['address'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 120px;"><label for="home_number">სახლის ტელ: </label></td>
					<td>
						<input type="text" id="home_number" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['home_number'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 120px;"><label for="mobile_number">მობილური ტელ: </label></td>
					<td>
						<input type="text" id="mobile_number" class="idle address" onblur="this.className=\'idle address\'" onfocus="this.className=\'activeField address\'" value="' . $res['mobile_number'] . '" />
					</td>
				</tr>
				<tr>
					<td style="width: 120px;"><label for="comment">შენიშვნა: </label></td>
					<td valign="top">
						<textarea id="comment" class="idle"  style="width: 226px !important;resize: vertical;">' . $res['comment'] . '</textarea>
					</td>
				</tr>
			</table>
			<!-- ID -->
			<div id="accordion">
			  <h3>მომხმარებელი</h3>
			  <div>
				<div>
					<div style="width: 170px; display: inline;"><label for="user" style="float:left;">მომხმარებელი :</label>
						<input type="text" id="user" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['username'] . '" style="display: inline; margin-left: 51px;"/>
					</div>
				</div>
				<div style=" margin-top: 2px; ">
					<div style="width: 170px; display: inline;"><label for="user_password" style="float:left;">პაროლი :</label>
						<input type="password" id="user_password" class="idle" onblur="this.className=\'idle\'" onfocus="this.className=\'activeField\'" value="' . $res['user_password'] . '" style="display: inline; margin-left: 87px;"/>
					</div>
				</div>
				<div style=" margin-top: 2px; ">
					<div style="width: 170px; display: inline; margin-top: 5px;"><label for="group_permission" style="float:left;">ჯგუფი :</label>
						<select id="group_permission" class="idls" style="display: inline; margin-left: 101px;">' . GetGroupPermission( $res['group_id'] ) . '</select>
					</div>
				</div>
			  </div>
			</div>
        </fieldset>
 	    <fieldset>
	    	<legend>თანამშრომლის სურათი</legend>

	    	<table class="dialog-form-table" width="100%">
	    		<tr>
					<td id="img_colum">
						<img style="margin-left: 5px;" width="105" height="105" id="upload_img" src="media/uploads/file/'.$image.'" />
					</td>
				</tr>
				<tr>
					<td style="padding-left: 30px;">
						<span>
							<a href="#" onclick="view_image('.$res[image_id].')" class="complate">View</a> | <a href="#" id="delete_image" image_id="'.$res[image_id].'" class="delete">Delete</a>
						</span>
					</td>
				</tr>
				</tr>
					<td style="padding-left: 5px;">
						<div style="margin-top:10px; width: 127px; margin-left: -5px;" class="file-uploader">
							<input id="choose_file" type="file" name="choose_file" class="input" style="display: none;">
							<button id="choose_button'.$disable_img.'" class="center" >აირჩიეთ ფაილი</button>
						</div>
					</td>
				</tr>
			</table>
        </fieldset>
		<input type="hidden" id="pers_id" value="' . $res['id'] . '" />
		<input type="hidden" id="is_user" value="'; 
		$incUs = mysql_fetch_array(mysql_query("SELECT id+1 AS `id` FROM users ORDER BY id DESC LIMIT 1"));
		$data .= $incUs[0];
		$data .= '" />
    </div>
    ';
	return $data;
}

?>