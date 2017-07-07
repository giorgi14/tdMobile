<?php
require_once('classes/core.php');
$data	= '';
$status = 0;
$ids    = json_decode($_REQUEST['id']);
$check  = $_REQUEST['check'];
$idd    = '';
foreach($ids as $id) {
    $idd .= $id.','; 
}
$idd .= '0';

if ($check == 1) {
   $filt = "AND id IN($idd)";
}else{
   $filt = '';
}

$result = mysql_query("SELECT id,
                              address,
                              content
                       FROM   sent_list
                       WHERE  actived = 1 AND status = 0 $filt");

while ($row = mysql_fetch_array($result)) {
    
    $text       = $row[content];
    $encodedtxt = urlencode($text);
    
    $check = file_get_contents('http://msg.ge/bi/sendsms.php?username=calldato1&password=di48fj47sh0&client_id=330&service_id=0330&to='.$row[address].'&text='.$encodedtxt.'');
    
    if ($check) {
        mysql_query("UPDATE  sent_list
                        SET `status` =  1
                     WHERE   id      = '$row[id]'");
        $status = 1;
    }
}
$data = array("status" => $status);
echo json_encode($data);
?>