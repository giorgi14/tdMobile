<?php
require_once 'includes/classes/core.php';
require_once 'includes/excel_reader2.php';
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
		$element	= 'choose_file';
		$file_name	= $_REQUEST['file_name'];
		$type		= $_REQUEST['type'];
		$path		= $_REQUEST['path'];
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
//echo $filename."f";

	$data = new Spreadsheet_Excel_Reader($filename);
	$r=$data->rowcount($sheet_index=0); $i=0;
	echo  $r;
	while (1!=$r){
		mysql_query("INSERT INTO `task_detail`
							  (`task_id`, `user_id`,  `status`, `person_n`, `first_name`, `last_name`, `person_status`,`phone`, `mail`, `addres`, `city_id`, `family_id`, `b_day`, `profesion`)
							    VALUES ( '".$_REQUEST['task_id']."', '".$_SESSION['USERID']."', '1',
										 '".$data->val($r,'A')."', '".$data->val($r,'B')."',
									 	 '".$data->val($r,'C')."', '".$data->val($r,'D')."',
									 	 '".$data->val($r,'E')."', '".$data->val($r,'F')."',
									 	 '".$data->val($r,'G')."', '".$data->val($r,'H')."',
									 	 '".$data->val($r,'I')."', '".$data->val($r,'J')."',
									 	 '".$data->val($r,'K')."')") or die (err);
		$r--; //return 0;
	}

	echo "xls File has been successfully Imported";

			if (file_exists($path)) {
				unlink($path);
			}
//			move_uploaded_file ( $_FILES [$element] ['tmp_name'], $path);
//
			// for security reason, we force to remove all uploaded file
//			@unlink ( $_FILES [$element] );
		}

		break;
    default:
       $error = 'Action is Null';
}
$data['error'] = $error;

echo json_encode($data);


/* ******************************
 *	File Upload Functions
 * ******************************
 */

?>