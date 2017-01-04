<?php
require_once('../../includes/classes/core.php');

$action = $_REQUEST['act'];
$error	= '';
$data	= '';
$user   = $_SESSION['USERID'];

switch ($action) {
	case 'file_upload':
		$element	   = $_REQUEST['button_id'];
		$file_name	   = $_REQUEST['file_name'];
		$type		   = $_REQUEST['file_type'];
		$path		   = $_REQUEST['path'];
		$rand_name     = $file_name . '.' . $type;
		$original_name = $_REQUEST['file_name_original'];
		$path		   = $path . $file_name . '.' . $type;
		$table_id      = $_REQUEST['table_id'];
		$table_name    = $_REQUEST['table_name'];
		
		if (! empty ( $_FILES [$element] ['error'] )){
			switch ($_FILES [$element] ['error']) {
				case '1' :
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2' :
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3' :
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4' :
					$error = 'No file was uploaded.';
					break;
				case '6' :
					$error = 'Missing a temporary folder';
					break;
				case '7' :
					$error = 'Failed to write file to disk';
					break;
				case '8' :
					$error = 'File upload stopped by extension';
					break;
				case '999' :
				default :
					$error = 'No error code avaiable';
			}
		}elseif (empty ( $_FILES [$element] ['tmp_name'] ) || $_FILES [$element] ['tmp_name'] == 'none'){
			$error = 'No file was uploaded..';
		}else{
			if(file_exists($path)){
				unlink($path);
			}
			move_uploaded_file ( $_FILES [$element] ['tmp_name'], $path);
			
			mysql_query("INSERT INTO `file` 
			                        (`user_id`, `name`, `rand_name`, `date`) 
			                  VALUES 
			                        ('$user', '$original_name', '$rand_name', NOW())");
			
			$file_id = mysql_insert_id();
			
			$file_tbale = mysql_query("SELECT `name`,
                                    		  `rand_name`,
                                    		  `file_date`,
			                                  `id`
                            		   FROM   `file`
                            		   WHERE  `id` = $file_id AND `actived` = 1");
			$str_file_table = array();
			
			while ($file_res_table = mysql_fetch_assoc($file_tbale)){
			    $str_file_table[] = array('name' => $file_res_table[name],'rand_name' => $file_res_table[rand_name],'id' => $file_res_table[id]);
			}
			
            $data = array('page'	=> $str_file_table);
			
			@unlink ( $_FILES [$element] );
		}

		break;		
    case 'delete_file':
        $file_id    = $_REQUEST['file_id'];
        $table_name = $_REQUEST['table_name'];
		$path		= "../../media/uploads/file/";
		
		mysql_query("UPDATE `file` 
		                SET `actived` = 0 
		              WHERE `id`      = $file_id");
		
		$str_file_table = 1;
		
		$data		= array('page'	=> $str_file_table);
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

?>