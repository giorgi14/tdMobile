<?php
class JsonTree { 

	private $_category_arr = array();
    private $_data = '';

    public function __construct() {
        $this->_category_arr = $this->_getCategory();
    }
    
    private function _getCategory() { 
        $query = mysql_query("	SELECT	`id`, `parent_id`, `name`
								FROM	`production_category`");
        
        $result = array(); 
	    while ($row = mysql_fetch_array($query)) { 
	        $result[$row["parent_id"]][] = $row; 
	    }	   
        return $result; 
    }
    
    private function _outTree($parent_id, $level) {
        if (isset($this->_category_arr[$parent_id])) {     	
            foreach ($this->_category_arr[$parent_id] as $value) {            	
            	$this->_data .= '<option value="' . $value['id'] . '">' . str_repeat("-", ($level * 3)) . '&nbsp;' . $value['name'] . "</option>";
            	
                $level++;
                
                $this->_outTree($value['id'], $level);
                $level--;
            }            
        } 
    }
    
    public function GetData() {
    	$this->_outTree(0, 0);
        return $this->_data;
    }
}
?>