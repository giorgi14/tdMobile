<?php
require_once 'classes/core.php';
require_once 'excel_reader2.php';
?>
<?php
/* ******************************
 *	File Upload aJax actions
 * ******************************
 */

$action = $_REQUEST['act'];
$error	= '';
$data	= '';

switch ($action) {
	case 'upload_file':
		$element		= 'choose_file';
		$file_name		= $_REQUEST['file_name'];
		$type			= $_REQUEST['type'];
		$path			= $_REQUEST['path'];
		$overhead_id	= $_REQUEST['overhead_id'];
		$user_id		= $_SESSION['USERID'];
		$path		= $path . $file_name . '.' . $type;

		if (! empty ( $_FILES [$element] ['error'] )) {
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
			} elseif (empty ( $_FILES [$element] ['tmp_name'] ) || $_FILES [$element] ['tmp_name'] == 'none') {
				$error = 'No file was uploaded..';
			} else {
	
			$filename=$_FILES [$element] ['tmp_name'];
			
				$data = new Spreadsheet_Excel_Reader($filename);
				$r=$data->rowcount($sheet_index=0); 
				$i=0;
				
				while (1!=$r){
					$status=1;
					$chek_name = $data->val($r,'C');
					$check = mysql_query("SELECT    production.`name` AS `prod_name`,
                                                            	      production_unit.`name` AS `unit`
                                                            FROM      production
                                                            LEFT JOIN production_unit ON production_unit.id = production.unit
                                                            WHERE     production.`name` = '$chek_name' AND production.actived = 1 
					                                        LIMIT 1");
					if (mysql_num_rows($check) != 0 ) {
					    $prod_name_status = 1;
					    $unit = mysql_fetch_assoc($check);
					    if ($unit[unit] == $data->val($r,'D')) {
					        $unit_status=1;
					    }else{
					        $unit_status=0;
					    }
					}else {
					    $prod_name_status = 0;
					    $unit_status      = 0;
					}
					mysql_query("INSERT INTO `overhead_detail`
					                        (`user_id`, `datetime`, `overhead_id`, `goods_id`, `name`, `unit`, `quantity`, `price`, `amount`, `vat_type`, `status`, `unit_status`, `prod_name_status`, `actived`)
					                  VALUES
					                        ('$user_id', NOW(), '$overhead_id', '".$data->val($r,'B')."', '".$data->val($r,'C')."', '".$data->val($r,'D')."', '".$data->val($r,'E')."', '".$data->val($r,'F')."', '".$data->val($r,'G')."', '".$data->val($r,'H')."', 0, '$unit_status', '$prod_name_status', 1)") or die (err);
					
					
					$r--;
					
				}
			
				echo 1;
			
				if (file_exists($path)) {
					unlink($path);
				}
			}

		break;

}


?>