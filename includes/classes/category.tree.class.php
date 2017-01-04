<?php
class CategoryTree {
	
	private $_category_arr	= array();
    private $_data			= '';
    private $_point			= '';
    private $_parent_id		= 0;
    
    public function __construct($point) {
    	$this->_point = $point;
    	$this->_category_arr = $this->_getCategory();
    }
    
    private function _getCategory() {
        $query = mysql_query("	SELECT	`id`, `parent_id`, `name`
								FROM	`production_category`
        						WHERE `actived`=1");
        
        $result = array();
	    while ($row = mysql_fetch_array($query)) {
	        $result[$row["parent_id"]][] = $row;
	    }
        return $result;
    }
    
    private function _outTree($parent_id, $level) {
        if (isset($this->_category_arr[$parent_id])) {
            foreach ($this->_category_arr[$parent_id] as $value) {
            	if ($value['id'] == $this->_point) {
                	$this->_data .= '<option value="' . $value['id'] . '" selected>' . str_repeat("-", ($level * 3)) . '&nbsp;' . $value['name'] . "</option>";
            	}else{
                	$this->_data .= '<option value="' . $value['id'] . '">' . str_repeat("-", ($level * 3)) . '&nbsp;' . $value['name'] . "</option>";
            	}
                $level++;
                
                $this->_outTree($value['id'], $level);
                $level--;
            }
        }
    }
    
    public function GetData() {
    	$this->_outTree($this->_parent_id, 0);
        return $this->_data;
    }
    
    public function SetParent($parent_id) {
    	$this->_parent_id = $parent_id;
    }
}
?>