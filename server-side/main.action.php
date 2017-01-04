<?php
require_once('../includes/classes/core.php');
$action = $_REQUEST['act'];
$error	= '';
$data	= '';

switch ($action) {
	case 'news':
	    $news_now_count = mysql_fetch_row(mysql_query("   SELECT COUNT(*) AS `new_news`
                                                            FROM `action`
                                                            WHERE TIMEDIFF(NOW(),create_date) <= '04:00:00'
                                                            AND actived = 1"));
	    
	    $news_arch_count = mysql_fetch_row(mysql_query("   SELECT COUNT(*) AS `new_news`
                                                             FROM `action`
                                                             WHERE TIMEDIFF(NOW(),create_date) > '04:00:00'
                                                             AND actived = 1"));
	    $data['news'] = array('news_now'=>$news_now_count[0],'news_arch'=>$news_arch_count[0]);
        break;
	case 'incomming_call':
        $incomming_call_day = mysql_query(" SELECT 	COUNT(*) AS `day_count`,
                                                    DATE(call_datetime) AS `day`
                                            FROM 	`asterisk_incomming`
                                            WHERE  DATE(`call_datetime`) > DATE_ADD(DATE(NOW()), INTERVAL -7 DAY)
                                            AND    DATE(`call_datetime`) <= DATE(NOW())
                                            AND    NOT ISNULL(disconnect_cause)
                                            GROUP BY DATE(call_datetime)
                                            ORDER BY DATE(call_datetime) ASC");
        
        while ($incomming_call_day_res = mysql_fetch_assoc($incomming_call_day)){
            $record[] = intval($incomming_call_day_res[day_count]);
            $data['incomming_call_day_date'][] = $incomming_call_day_res[day];
        }
        $data['incomming_call_day'][] = (object)array('name'=>'');
        $data['incomming_call_day'][] = (object)array('data'=>$record);
        break;
    case 'outgoing_call':
        $outgoing_call_day = mysql_query("  SELECT 	COUNT(*) AS `day_count`,
                                                    DAY(call_datetime) AS `day`
                                            FROM 	`asterisk_outgoing`
                                            WHERE   DATE(`call_datetime`) > DATE_ADD(DATE(NOW()), INTERVAL -7 DAY)
                                            AND 	DATE(`call_datetime`) <= DATE(NOW()) AND LENGTH(phone) != 3
                                            GROUP BY DATE(call_datetime)
                                            ORDER BY DATE(call_datetime) ASC");
        
        while ($outgoing_call_day_res = mysql_fetch_assoc($outgoing_call_day)){
            $record[] = intval($outgoing_call_day_res[day_count]);
        }
        $data['outgoing_call_day'][] = (object)array('name'=>'');
        $data['outgoing_call_day'][] = (object)array('data'=>$record);
        break;
 	case 'inner_call' :
 	    $inner_call_day = mysql_query(" SELECT 	COUNT(*) AS `day_count`,
                                    			DAY(call_datetime) AS `day`
                                        FROM 	`asterisk_outgoing`
                                        WHERE   DATE(`call_datetime`) > DATE_ADD(DATE(NOW()), INTERVAL -7 DAY)
                                        AND 	DATE(`call_datetime`) <= DATE(NOW()) AND LENGTH(phone) = 3
                                        GROUP BY DATE(call_datetime)
                                        ORDER BY DATE(call_datetime) ASC");
 	    
 	    while ($inner_call_day_res = mysql_fetch_assoc($inner_call_day)){
 	        $record[] = intval($inner_call_day_res[day_count]);
 	    }
 	    $data['inner_call_day'][] = (object)array('name'=>'');
 	    $data['inner_call_day'][] = (object)array('data'=>$record);
        break;
    case 'task':
        $task_new_count = mysql_fetch_row(mysql_query("   SELECT COUNT(*) AS `task_new` FROM `task` WHERE actived = 1 AND task_status_id = 7"));
        $task_proces_count = mysql_fetch_row(mysql_query("   SELECT COUNT(*) AS `task_proces` FROM `task` WHERE actived = 1 AND task_status_id = 8"));
        $task_done_count = mysql_fetch_row(mysql_query("   SELECT COUNT(*) AS `task_done` FROM `task` WHERE actived = 1 AND task_status_id = 11"));
        $task_delete_count = mysql_fetch_row(mysql_query("   SELECT COUNT(*) AS `task_delete` FROM `task` WHERE actived = 1 AND task_status_id = 12"));
        
        $data['task'] = array('task_new'=>$task_new_count[0],'task_proces'=>$task_proces_count[0],'task_done'=>$task_done_count[0],'task_delete'=>$task_delete_count);
        break;
    case 'answer_unanswer':
        $answer_unanswer = mysql_query("  SELECT 'ნაპასუხები' AS `answer`,COUNT(*) AS `answer_count`
                                                            FROM `asterisk_incomming`
                                                            WHERE disconnect_cause != 'ABANDON'
                                                            AND   NOT ISNULL(disconnect_cause)
                                                            AND   DATE(`call_datetime`) = DATE(NOW())
                                                            UNION ALL
                                                            SELECT 'უპასუხო' AS `unanswer`,COUNT(*) AS `answer_count`
                                                            FROM `asterisk_incomming`
                                                            WHERE disconnect_cause = 'ABANDON'
                                                            
                                                            AND   DATE(`call_datetime`) = DATE(NOW())");
        while($res = mysql_fetch_assoc($answer_unanswer)){
            $count[] = intval($res[answer_count]);
        }
        
        $answer_unanswer_today = mysql_query("  SELECT 'ნაპასუხები' AS `answer`,COUNT(*) AS `answer_count`
                                                FROM `asterisk_incomming`
                                                WHERE disconnect_cause != 'ABANDON'
                                                AND    NOT ISNULL(disconnect_cause)
                                                AND   DATE(`call_datetime`) = DATE(NOW())
                                                UNION ALL
                                                SELECT 'უპასუხო' AS `unanswer`,COUNT(*) AS `answer_count`
                                                FROM `asterisk_incomming`
                                                WHERE disconnect_cause = 'ABANDON'
                                                AND   DATE(`call_datetime`) = DATE(NOW())");
        while($res_today = mysql_fetch_assoc($answer_unanswer_today)){
            $count_today[] = intval($res_today[answer_count]);
        }
        $data['answer_unanswer'][] = array('name'=>'ზარი','data'=>array(array('ნაპასუხები', $count[0]),array('უპასუხო', $count[1])));
        $data['answer_unanswer_today'] = array('ans'=>$count_today[0],'unans'=>$count_today[1]);
        break;
    case 'sl':
        $sl_content = mysql_fetch_assoc(mysql_query("SELECT 	`sl_min`,
                                                				`sl_procent`
                                                     FROM 		`sl_content`"));
        $sl = mysql_fetch_assoc(mysql_query("SELECT     
                                    					ROUND((SUM(IF(asterisk_incomming.wait_time<$sl_content[sl_min], 1, 0)) / COUNT(*) ) * 100) AS `percent`,
                                    					COUNT(asterisk_incomming.wait_time ) AS `num`
                                             FROM       `asterisk_incomming`
                                             WHERE      DATE(asterisk_incomming.call_datetime) = DATE(NOW()) AND asterisk_incomming.disconnect_cause != 'ABANDON'"));
        $data['sl']['min'] = $sl_content['sl_min'];
        $data['sl']['percent'] = $sl['percent'];
        $data['sl']['sl_procent'] = $sl_content['sl_procent'];
        break;
    case 'asa':
        $asa = mysql_fetch_assoc(mysql_query("  SELECT  TIME_FORMAT(SEC_TO_TIME(AVG(asterisk_incomming.wait_time)),'%i:%s') AS `wait_time_avg`,
                                        				TIME_FORMAT(SEC_TO_TIME(MIN(asterisk_incomming.wait_time)),'%i:%s') AS `wait_time_min`,
                                        				TIME_FORMAT(SEC_TO_TIME(MAX(asterisk_incomming.wait_time)),'%i:%s') AS `wait_time_max`
                                                FROM    `asterisk_incomming`
                                                WHERE   DATE(asterisk_incomming.datetime) = DATE(NOW())
                                                AND     asterisk_incomming.duration > 0"));
        $data['asa']['wait_time_avg'] = $asa['wait_time_avg'];
        $data['asa']['wait_time_min'] = $asa['wait_time_min'];
        $data['asa']['wait_time_max'] = $asa['wait_time_max'];
        break;
    case 'hold_avg_time':
        $hold_avg = mysql_fetch_assoc(mysql_query(" SELECT  TIME_FORMAT(SEC_TO_TIME(AVG(asterisk_incomming.wait_time)),'%i:%s') AS `wait_time_avg`,
                                            				TIME_FORMAT(SEC_TO_TIME(MIN(asterisk_incomming.wait_time)),'%i:%s') AS `wait_time_min`,
                                            				TIME_FORMAT(SEC_TO_TIME(MAX(asterisk_incomming.wait_time)),'%i:%s') AS `wait_time_max`
                                                    FROM    `asterisk_incomming`
                                                    WHERE   DATE(asterisk_incomming.datetime) = DATE(NOW())"));
        $data['hold_avg_time']['wait_time_avg'] = $hold_avg['wait_time_avg'];
        $data['hold_avg_time']['wait_time_min'] = $hold_avg['wait_time_min'];
        $data['hold_avg_time']['wait_time_max'] = $hold_avg['wait_time_max'];
        break;
    case 'free_space_on_disk':
        $space = mysql_fetch_assoc(mysql_query("SELECT  SUBSTR(total_space,1,2) AS `total_space`,
                                                        SUBSTR(total_space,1,2) - SUBSTR(free_space,1,2) AS `busy_space`,
                                                        SUBSTR(free_space,1,2) AS `free_space`
                                                FROM    `hdd`"));
        $data['space']['total_space'] = 15;
        $data['space']['array_space'][]  = array('type'=>'pie','name'=>'ადგილი','innerSize'=>'100%','data'=>array(array('დაკავებული', intval(6) ),array('თავისუფალი', intval(9) )));
        break;
    case 'live_operators':
        $in_busy = mysql_fetch_assoc(mysql_query("  SELECT COUNT(*) AS `in_busy`
                                                    FROM `asterisk_incomming`
                                                    WHERE DATE(asterisk_incomming.datetime) = DATE(NOW())
                                                    AND ISNULL(asterisk_incomming.disconnect_cause)
                                                    AND NOT ISNULL(dst_extension)"));
        $data['live_operators'][] = array('name'=>'თავის','data'=>array((3-intval($in_busy[in_busy]))));
        $data['live_operators'][] = array('name'=>'დაკავ','data'=>array(intval($in_busy[in_busy])));
        $data['live_operators'][] = array('name'=>'გამორთ','data'=>array(0));
        break;
    case 'live_calls':
        $in_talk = mysql_fetch_assoc(mysql_query("  SELECT COUNT(*) AS `in_talk`
                                                    FROM `asterisk_incomming`
                                                    WHERE DATE(asterisk_incomming.datetime) = DATE(NOW())
                                                    AND ISNULL(asterisk_incomming.disconnect_cause)
                                                    AND NOT ISNULL(dst_extension)"));
        $in_queue = mysql_fetch_assoc(mysql_query(" SELECT COUNT(*) AS `in_queue`
                                                    FROM `asterisk_incomming`
                                                    WHERE DATE(asterisk_incomming.datetime) = DATE(NOW())
                                                    AND ISNULL(asterisk_incomming.disconnect_cause)
                                                    AND ISNULL(dst_extension)"));
        
        $data['live_calls']['in_talk'] = $in_talk['in_talk'];
        $data['live_calls']['in_queue'] = $in_queue['in_queue'];
        break;
//     case 'operator_answer':
//         $operator = mysql_query("   SELECT  user_info.`name`,asterisk_incomming.dst_extension,asterisk_incomming.user_id,
//                                             user_info.image,
//                                             COUNT(*) AS `ans`
//                                     FROM `asterisk_incomming`
//                                     LEFT JOIN user_info ON asterisk_incomming.user_id = user_info.user_id
//                                     WHERE DATE(asterisk_incomming.datetime) = DATE(NOW())
//                                     AND asterisk_incomming.duration > 0
//                                     GROUP BY asterisk_incomming.user_id");
//         $ope = '<div class="row header">
//                   <div class="cell">
//                     ოპერატორი
//                   </div>
//                   <div class="cell" style="width: 95px;">
//                     ნაპასუხები ზარი
//                   </div>
                  
//                 </div>';
//         while ($operator_res = mysql_fetch_assoc($operator)){
//             $ope.='<div class="row">
//                             <div class="cell">
//                             <div style="width: 24px; height: 24px; background: url(\'media/uploads/file/'.$operator_res[image].'\');background-size: 24px 24px; background-repeat: no-repeat; float: left;"></div> <div style="margin-top: 5px; margin-left: 5px; float: left;">'.$operator_res[name].'</div>
//                             </div>
//                             <div class="cell align_right">
//                             '.$operator_res[ans].'
//                             </div>
//                             </div>';
//         }
//         $data['operator_answer']=$ope;
//         break;
//     case 'operator_answer_dur':
//         $operator_avg = mysql_query("   SELECT  user_info.`name`,
//                                                 user_info.image,
//                                                 SEC_TO_TIME(SUM(asterisk_incomming.duration)) AS `total_duration`,
//     				                            SEC_TO_TIME(AVG(asterisk_incomming.duration)) AS `duration_avg`
//                                         FROM `asterisk_incomming`
//                                         LEFT JOIN user_info ON asterisk_incomming.user_id = user_info.user_id
//                                         WHERE DATE(asterisk_incomming.datetime) = DATE(NOW())
//                                         AND asterisk_incomming.duration > 0
//                                         GROUP BY asterisk_incomming.user_id");
//         $ope_avg = '<div class="row header">
//                       <div class="cell">
//                         ოპერატორი
//                       </div>
//                       <div class="cell" style="width: 75px;">
//                         საუბ. ხ-ბა.
//                       </div>
//                       <div class="cell" style="width: 81px;">
//                         საუბ. საშ. ხ-ბა
//                       </div>
//                     </div>';
//         while ($operator_avg_res = mysql_fetch_assoc($operator_avg)){
//             $ope_avg.='<div class="row">
//                             <div class="cell">
//                             <div style="width: 24px; height: 24px; background: url(\'media/uploads/file/'.$operator_avg_res[image].'\');background-size: 24px 24px; background-repeat: no-repeat; float: left;"></div> <div style="margin-top: 5px; margin-left: 5px; float: left;">'.$operator_avg_res[name].'</div>
//                             </div>
//                             <div class="cell align_right">
//                             '.$operator_avg_res[total_duration].'
//                             </div>
//                             <div class="cell align_right">
//                             '.$operator_avg_res[duration_avg].'
//                             </div>
//                             </div>';
//         }
//         $data['operator_answer_dur']=$ope_avg;
//         break;
    default:
       $error = 'Action is Null';
}

$data['error'] = $error;

echo json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

?>
