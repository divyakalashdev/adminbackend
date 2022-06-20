<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../DB.class.php';
$db = new DB;
  
$search = urldecode($_GET['search']);
    
$con = array(
    'like' => array('tags' => $search)
);
$profile_ids = array();
$video_ids = array();
if(!empty($search)){
    $tags = $db->getRows('tags', $con);
}

$list = array();
try{
    if(!empty($tags)){
        foreach($tags as $t){
            if(!empty($t['profile_id'])){
                array_push($profile_ids, $t['profile_id']);
            }else if(!empty($t['video_id'])){
                array_push($video_ids, $t['video_id']);
            }
        }
        if(count($profile_ids) > 0){
            $sql = "SELECT categories.category, categories.height, tags.tags, profiles.* FROM profiles LEFT JOIN tags ON tags.profile_id = profiles.id LEFT JOIN categories ON categories.id = profiles.profile_type WHERE profiles.id IN(".implode(", ", $profile_ids).")";
            $profiles = $db->customQuery($sql);
            if(!empty($profiles)){
                foreach($profiles as $p){
                    $p['catid'] = $p['profile_type'];
                    $p['title'] = $p['name'];
                    $p['thumbnail'] = $p['avatar'];
                    $p['media'] = $p['poster'];
                    $p['media_type'] = 'profile';
                    unset($p['profile_type']);
                    unset($p['name']);
                    unset($p['avatar']);
                    unset($p['poster']);
                    
                    array_push($list, $p);
                }
            }
        }
        
        if(count($video_ids) > 0){
            $sql = "SELECT categories.category, categories.height, tags.tags, videos.* FROM videos LEFT JOIN tags ON tags.video_id = videos.id LEFT JOIN categories ON categories.id = videos.catid WHERE videos.id IN(".implode(", ", $video_ids).")";
            $videos = $db->customQuery($sql);
            if(!empty($videos)){
                foreach($videos as $v){
                    if(empty($v['video_url'])){
                        $v['media'] = $v['audio_url'];
                        $v['media_type'] = "audio";
                    }else{
                        $v['media'] = $v['video_url'];
                        $v['media_type'] = "video";
                    }
                    unset($v['video_url']);
                    unset($v['audio_url']);
                    array_push($list, $v);
                }
            }
        }
    }
}catch(Exception $e) {
    $list = '';
}
$path = $db->getBasePath()."/";
    
    
if(!empty($list) && count($list) > 0){
    echo json_encode(array("status" => 200, "msg" => "Data found.", "media_list" => $list, 'path' => $path));
}else{
    echo json_encode(array("status" => 201, "msg" => "No data found."));
}