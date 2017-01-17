<?php
require_once 'core.php';

class page {
	
	private $user_id;
	private $page_id;
	
	function page($user_id, $page_id) {
		
		$this->user_id = $user_id;
		$this->page_id = $page_id;
		
	}
	
	function reqPage() {
		
		$result = mysql_query("	SELECT   	CONCAT(page_group.name, '/', pages.name, '.php') AS dest
								FROM 		users LEFT JOIN group_permission
								ON 			users.group_id = group_permission.group_id
								LEFT JOIN 	pages ON group_permission.page_id = pages.id
								LEFT JOIN	page_group ON pages.page_group_id = page_group.id 
								WHERE 		users.id = $this->user_id AND pages.id = $this->page_id");
		
		$row = mysql_fetch_assoc($result);
		$page_name = $row['dest'];
		
		echo '<div id="page-container" class="page-' . $this->page_id . '">';
		require_once 'client-side/'.$page_name;
		echo '</div>';
		
	}
	
}

?>