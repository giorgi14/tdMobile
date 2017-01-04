<?php

class log{
	
	public $user_id;
	public $date;
	public $table;
	public $column;
	public $old_value;
	public $new_value;
	public $event;
	public $id;
	
	public function log(){
		
		$this->user_id	= $_SESSION['USERID'];
		$this->date		= date('Y-m-d H:i:s');
		
	}
	
	public function setInsertLog($table){
		
		$this->table	= $table;
		$this->event	= 'insert';
		$this->id		= mysql_insert_id();
		
 		$result  		= mysql_fetch_assoc(mysql_query("SELECT *
						 								 FROM  	`$table`
						 							     WHERE 	`id`=$this->id"));
 				
       	$result_columns = mysql_query("SELECT 	CONCAT(COLUMN_NAME) AS `COLUMN_NAME`
                                       FROM  	`INFORMATION_SCHEMA`.`COLUMNS` 
                                       WHERE 	`TABLE_SCHEMA`='adrenaline' AND `TABLE_NAME`='$this->table' AND `COLUMN_NAME`!='id' AND `COLUMN_NAME`!='user_id'"); 
       	
       	$array = array();
       	
       	while($row = mysql_fetch_assoc($result_columns)){
       		
       		$array[] = $row['COLUMN_NAME']; 
       			      			  			
       	}
       	
       	foreach ($array as &$collumn) {
       		
       		mysql_query("INSERT INTO `logs`
			       			(user_id, date, `table`, `event`, `collumn`, `row_id`, `old_value`, `new_value`)
			       		 VALUES
			       			($this->user_id, '$this->date', '$this->table', '$this->event', '$collumn', $this->id, NULL, '$result[$collumn]')");
       		
       	}
       	
	}

	public function setUpdateLogBefore($table, $id){
	
		$this->table	= $table;
		$this->event	= 'update';
		$this->id		= $id;
	
		$result  		= mysql_fetch_assoc(mysql_query("SELECT *
														 FROM  	`$table`
														 WHERE 	`id`=$this->id"));
			
		$result_columns = mysql_query("SELECT 	CONCAT(COLUMN_NAME) AS `COLUMN_NAME`
										FROM  	`INFORMATION_SCHEMA`.`COLUMNS`
										WHERE 	`TABLE_SCHEMA`='adrenaline' AND `TABLE_NAME`='$this->table' AND `COLUMN_NAME`!='id' AND `COLUMN_NAME`!='user_id'");
							
		$array = array();
	
		while($row = mysql_fetch_assoc($result_columns)){
			 
			$array[] = $row['COLUMN_NAME'];
	
		}
	
		foreach ($array as &$collumn) {
			 
					mysql_query("INSERT INTO `logs`
						(user_id, date, `table`, `event`, `collumn`, `row_id`, `old_value`, `new_value`)
					VALUES
						($this->user_id, '$this->date', '$this->table', '$this->event', '$collumn', $this->id, '$result[$collumn]', NULL)");
			 
		}
	
	}
	
	public function setUpdateLogAfter($table, $id){
	
		$this->table	= $table;
		$this->event	= 'update';
		$this->id		= $id;
	
		$result  		= mysql_fetch_assoc(mysql_query("SELECT *
														FROM  	`$table`
														WHERE 	`id`=$this->id"));
			
		$result_columns = mysql_query(" SELECT 	CONCAT(COLUMN_NAME) AS `COLUMN_NAME`
										FROM  	`INFORMATION_SCHEMA`.`COLUMNS`
										WHERE 	`TABLE_SCHEMA`='adrenaline' AND `TABLE_NAME`='$this->table' AND `COLUMN_NAME`!='id' AND `COLUMN_NAME`!='user_id'");
			
		$array = array();
	
		while($row = mysql_fetch_assoc($result_columns)){
	
			$array[] = $row['COLUMN_NAME'];
	
		}
	
		foreach ($array as &$collumn) {
	
			mysql_query("UPDATE `logs`
						 SET 	`new_value` = '$result[$collumn]'
						 WHERE  `logs`.`collumn` = '$collumn' AND `logs`.`row_id` = $this->id
						 ORDER  BY `logs`.`id` DESC
						 LIMIT 	1");
	
		}
		
		mysql_query("DELETE  FROM  `logs`
					 WHERE   `logs`.`old_value` = `logs`.`new_value` AND `logs`.`row_id` = $this->id");
		
	}
     
}


?>