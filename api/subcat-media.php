<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../DB.class.php';
$db = new DB;

try {
    $parent_id = $_GET['parentid'];
    $sub_id = $_GET['subcatid'];
    $list = array();

    if (isset($_GET['parentid']) && isset($_GET['subcatid'])) {
        $con = array(
            'where' => array('catid' => $sub_id),
            'order_by' => 'arrange_id',
        );

        $sql = "SELECT p.id, p.category, p.thumbnail, p.priority, s.display_type, p.height, s.id as subid, s.category as subcategory FROM categories as p LEFT JOIN categories as s ON p.parent_id = $parent_id WHERE p.id = $sub_id AND s.id = $parent_id AND p.status = 0 ORDER BY p.priority";
        $category_info = $db->customQuery($sql);

        $sql = "SELECT * FROM `category_posters` WHERE cat_id = $sub_id";
        $category_posters = $db->customQuery($sql);

        $videos = $db->getRows('videos', $con);
        if (!empty($videos)) {
            foreach ($videos as $v) {
                if (empty($v['video_url'])) {
                    $v['media'] = $v['audio_url'];
                    $v['media_type'] = "audio";
                } else {
                    $v['media'] = $v['video_url'];
                    $v['media_type'] = "video";
                }
                unset($v['video_url']);
                unset($v['audio_url']);
                array_push($list, $v);
            }
        } else {
            $list = array();
        }
    }
    $path = $db->getBasePath() . "/";
} catch (Exception $e) {
    $list = '';
}

if (!empty($category_info) && count($category_info) > 0) {
    echo json_encode(array("status" => 200, "msg" => "Data found.", "category_info" => $category_info, 'cat_posters' => $category_posters, "media_list" => $list, 'path' => $path));
} else {
    echo json_encode(array("status" => 201, "msg" => "No data found."));
}
