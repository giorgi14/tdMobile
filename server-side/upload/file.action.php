<?php
require_once('../../includes/classes/core.php');

$action = $_REQUEST['act'];
$error	= '';
$data   = '';
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
		    if ($table_name == ''){
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
                                        		  `date`,
    			                                  `id`
                                		   FROM   `file`
                                		   WHERE  `id` = $file_id AND `actived` = 1");
    			$str_file_table = array();
    			
    			while ($file_res_table = mysql_fetch_assoc($file_tbale)){
    			    $str_file_table[] = array('name' => $file_res_table[name],'rand_name' => $file_res_table[rand_name],'id' => $file_res_table[id]);
    			}
    			
                $data = array('page' => $str_file_table);
    			
    			@unlink ( $_FILES [$element] );
		    }elseif ($table_name == 'car_picture'){
		        if(file_exists($path)){
		            unlink($path);
		        }
		         
		        move_uploaded_file ( $_FILES [$element] ['tmp_name'], $path);
		         
		        mysql_query("INSERT INTO `file`
		                                (`user_id`, `name`, `rand_name`, `date`)
		                          VALUES
		                                ('$user', '$original_name', '$rand_name', NOW())");
		         
		        $file_id = mysql_insert_id();
		        
		        mysql_query("INSERT INTO `car_picture` 
                                        (`client_id`, `file_id`) 
                                  VALUES 
                                        ('$table_id', '$file_id')");
		        
		       
		         
		        @unlink ( $_FILES [$element] );
		    }elseif ($table_name == 'client_papers'){
		        if(file_exists($path)){
		            unlink($path);
		        }
		         
		        move_uploaded_file ( $_FILES [$element] ['tmp_name'], $path);
		         
		        mysql_query("INSERT INTO `file`
		                                (`user_id`, `name`, `rand_name`, `date`)
		                          VALUES
		                                ('$user', '$original_name', '$rand_name', NOW())");
		         
		        $file_id = mysql_insert_id();
		        
		        mysql_query("INSERT INTO `client_papers` 
                                        (`client_id`, `file_id`) 
                                  VALUES 
                                        ('$table_id', '$file_id')");
		        
		       
		         
		        @unlink ( $_FILES [$element] );
		    }
		}

		break;		
    case 'delete_file':
        $file_id  = $_REQUEST['file_id'];
        $local_id = $_REQUEST['local_id'];
        $table_id = $_REQUEST['table_id'];
		if ($table_id == 'client_papers') {
    		mysql_query("UPDATE `file` 
    		                SET `actived` = 0 
    		              WHERE `id`      = $file_id");
    		mysql_query("UPDATE `client_papers`
            		        SET `actived` = 0
            		     WHERE `file_id`  = $file_id");
    		
             
             $file_tbale = mysql_query("SELECT  file.`name`,
                                				file.`rand_name`,
                                				file.`date`,
                                				file.`id`
                                        FROM   `client_papers`
                                        JOIN    file ON file.id = client_papers.file_id
                                        WHERE   client_papers.`client_id` = '$local_id' AND file.`actived` = 1");
            $str_file_documents = '';
            while ($file_res_document = mysql_fetch_assoc($file_tbale)){
                $str_file_documents .= '
                                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_document[date].'</div>
                                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; width:29%;float:left;">'.$file_res_document[name].'</div>
                                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:28%; float:left;" onclick="download_file(\''.$file_res_document[rand_name].'\')">ჩამოტვირთვა</div>
                                        <div style="border:1px solid #CCC; padding:5px; text-align:center; vertical-align:middle; cursor:pointer; width:8%; float:left;" onclick="delete_file(\''.$file_res_document[id].'\',\'client_papers\')">-</div>';
            } 
		}
        $data = array('documets' => $str_file_documents);
		
		
        break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data);

?>