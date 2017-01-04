<?php

include('classes/core.php');

$nav_id		= 1;
$user_id	= $_SESSION['USERID'];
$error		= '';
$data		= '';


$sql = mysql_query("SELECT 	`menu_detail`.`id`,
							`menu_detail`.`title`,
							`menu_detail`.`page_id`,
							`menu_detail`.`url`,
							`menu_detail`.`icon`,
                            `menu_detail`.`sub_icon`
					FROM 	`users`
                    LEFT JOIN `group` ON `users`.`group_id` = `group`.id
					LEFT JOIN `group_permission` ON `group`.id = `group_permission`.`group_id`
					LEFT JOIN menu_detail ON `group_permission`.`page_id` = `menu_detail`.`page_id`
					WHERE `users`.`id` = $user_id AND `menu_detail`.`menu_id` = $nav_id AND `menu_detail`.`parent` = 0
					ORDER BY `menu_detail`.`position`");

$par_class = GetParentClass($nav_id);
$categories = array(
	"nav"		=> array(),
	"nav_class"	=> $par_class
);

while ($row = mysql_fetch_assoc($sql)) {
	
	$id = $row['id'];
	$ssql = mysql_query("	SELECT 	`menu_detail`.`id`,
									`menu_detail`.`title`,
									`menu_detail`.`page_id`,
									`menu_detail`.`url`,
									`menu_detail`.`icon`,
                                    `menu_detail`.`sub_icon`
							FROM 	`users`
	                        LEFT JOIN `group` ON `users`.`group_id` = `group`.id
							LEFT JOIN `group_permission` ON `group`.id = `group_permission`.`group_id`
							LEFT JOIN menu_detail ON `group_permission`.`page_id` = `menu_detail`.`page_id`
							WHERE 	`users`.`id` = $user_id AND `menu_detail`.`menu_id` = $nav_id AND `menu_detail`.`parent`='$id'
							ORDER BY `menu_detail`.`position`");

	$category 			= $row;	
	$category["sub"] 	= array();

	while ($srow = mysql_fetch_assoc($ssql)) {
		
		$subcat			= $srow;
		$subcat["sub"]	= array();
		
		$sssql = mysql_query("	SELECT 	`menu_detail`.`id`,
										`menu_detail`.`title`,
										`menu_detail`.`page_id`,
										`menu_detail`.`url`,
										`menu_detail`.`icon`,
                                        `menu_detail`.`sub_icon`
								FROM 	`users` LEFT JOIN `group` ON `users`.`group_id` = `group`.id
								LEFT JOIN `group_permission` ON `group`.id = `group_permission`.`group_id`
								LEFT JOIN menu_detail ON `group_permission`.`page_id` = `menu_detail`.`page_id`
								WHERE 	`users`.`id` = $user_id AND `menu_detail`.`menu_id` = $nav_id AND `menu_detail`.parent=$srow[id]
								ORDER BY `menu_detail`.`position`");
		
		while ($ssrow = mysql_fetch_assoc($sssql)) {
			
			$subsubcat = $ssrow;			
			array_push($subcat["sub"], $subsubcat);
		}
		
		array_push($category["sub"], $subcat);
	}
	
	array_push($categories["nav"], $category);
}

$data = $categories;
$data['error'] = $error;

echo json_encode($data);


function GetParentClass($nav_id) {
	$res = mysql_fetch_assoc(mysql_query("SELECT `class`
										  FROM `menu`
										  WHERE `id` = $nav_id"));
	return $res['class'];
}
?>