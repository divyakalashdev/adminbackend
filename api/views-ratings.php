<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../DB.class.php';
$file = fopen("../video-settings.json", "r");
$line = '';
//Output lines until EOF is reached
while (!feof($file)) {
    $line .= fgets($file);
}
fclose($file);

$view_setting = json_decode($line, true);
$timelimit = $view_setting['view_timing_limit'];
$viewcount = $view_setting['view_counts_limit'];

if (isset($_REQUEST['media_id']) && isset($_REQUEST['userid']) && isset($_REQUEST['watch_duration'])) {
    $db = new DB;
    $mid = trim(strip_tags($_REQUEST['media_id']));
    $userid = trim(strip_tags($_REQUEST['userid']));
    $watch_duration = trim(strip_tags($_REQUEST['watch_duration']));
    $status = '';
    $msg = '';
    try {
        // Get users from database
        $con = array(
            'return_type' => 'single',
            'where' => array('media_id' => $mid, 'userid' => $userid)
        );

        $views_info = $db->getRows('views_ratings', $con);

        if (!empty($views_info)) {
            $views = $views_info['views'];
            $total_duration = $views_info['total_duration'];
            //$view_duration = trim(strip_tags($_REQUEST['view_duration']));
            if ($watch_duration >= $timelimit && $views < $viewcount) {
                $total_duration = $total_duration + $watch_duration;
                $views = $views + 1;
            }
            $data = array('views' => $views, 'view_duration' => $watch_duration, 'total_duration' => $total_duration);
            $cons['media_id'] = $mid;
            $cons['userid'] = $userid;
            $db->update('views_ratings', $data, $cons);
            $d = array('views' => $views);
            $vcons['id'] = $mid;
            $db->update('videos', $d, $vcons);
            $status = '200';
            $msg = 'Information updated';
        } else {
            /* if ($watch_duration > $timelimit) {
                $data = array('userid' => $userid, 'media_id' => $mid, 'views' => 1, 'view_duration' => $watch_duration, 'total_duration' => $watch_duration);
            }else{ */
            $data = array('userid' => $userid, 'media_id' => $mid, 'views' => 1, 'view_duration' => $watch_duration, 'total_duration' => $watch_duration);
            $db->insert('views_ratings', $data);
            $d = array('views' => 1);
            $vcons['id'] = $mid;
            $db->update('videos', $d, $vcons);
            $status = '200';
            $msg = 'Information stored';
        }
    } catch (Exception $e) {
        $status = '201';
        $msg = "Please try later";
    }
    echo json_encode(array("status" => $status, "msg" => $msg));
} else if (isset($_REQUEST['media_id']) && isset($_REQUEST['userid']) && isset($_REQUEST['rating'])) {
    $mid = trim(strip_tags($_REQUEST['media_id']));
    $userid = trim(strip_tags($_REQUEST['userid']));
    $rating = trim(strip_tags($_REQUEST['rating']));
    $db = new DB;
    try {
        // Get users from database
        $con = array(
            'return_type' => 'single',
            'where' => array('media_id' => $mid)
        );

        $views_info = $db->getRows('views_ratings', $con);

        if (!empty($views_info)) {
            $data = array('rating' => $rating);
            $cons['media_id'] = $mid;
            $cons['userid'] = $userid;
            $db->update('views_ratings', $data, $cons);
            $status = '200';
            $msg = 'Information updated';
        } else {
            $data = array('userid' => $userid, 'media_id' => $mid, 'rating' => $rating);
            $db->insert('views_ratings', $data);
            $status = '200';
            $msg = 'Information stored';
        }
    } catch (Exception $e) {
        $status = '201';
        $msg = "Please try later";
    }
    echo json_encode(array("status" => $status, "msg" => $msg));
} else if (isset($_REQUEST['media_id']) && isset($_REQUEST['view_rating'])) {
    $mid = trim(strip_tags($_REQUEST['media_id']));

    $db = new DB;
    try {
        $views_info = $db->customQuery("SELECT media_id, sum(views) as media_view, sum(total_duration) as watch_time, ROUND(AVG(rating),1) as rating FROM views_ratings where media_id = $mid");

        if (!empty($views_info)) {
            $status = '200';
            echo json_encode(array('status' => 200, 'media_id' => $views_info[0]['media_id'], 'views' => $views_info[0]['media_view'], 'watch_time' => $views_info[0]['watch_time'], 'rating' => $views_info[0]['rating']));
        } else {
            echo json_encode(array("status" => 201, "msg" => "No data found"));
        }
    } catch (Exception $e) {
        echo json_encode(array("status" => 201, "msg" => "Please try later"));
    }
}
