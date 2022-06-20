<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../DB.class.php';
$db = new DB;
  
try{
    $offset = $_GET['_start'];
    $limit = $_GET['_limit'];
    $distype = $_GET['distype'];
    $list = array();
    
    if($distype == 'profile'){
        if(isset($_GET['pid'])){
            $profile_id = $_GET['pid'];
            $con = array(
                'start' => $offset,
                'limit' => $limit,
                'where' => array('profile_id' => $profile_id, 'status' => 0),
                'order_by' => 'arrange_id',
            );
            
            $videos = $db->getRows('videos', $con);
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
            }else{
                $catid = $_GET['pid'];
                $con = array(
                    'start' => $offset,
                    'limit' => $limit,
                    'where' => array('profile_type' => $catid, 'status' => 0),
                    'order_by' => 'id DESC',
                );
                $profile = $db->getRows('profiles', $con);
                if(!empty($profile)){
                    foreach($profile as $p){
                        $p['catid'] = $p['profile_type'];
                        $p['title'] = $p['name'];
                        $p['thumbnail'] = $p['avatar'];
                        $p['media'] = $p['poster'];
                        $p['media_type'] = $distype;
                        unset($p['profile_type']);
                        unset($p['name']);
                        unset($p['avatar']);
                        unset($p['poster']);
                        
                        array_push($list, $p);
                    }
                }
            }
        }else if(isset($_GET['catid'])){
            $catid = $_GET['catid'];
            $con = array(
                'start' => $offset,
                'limit' => $limit,
                'where' => array('profile_type' => $catid, 'status' => 0),
                'order_by' => 'id DESC',
            );
            $profile = $db->getRows('profiles', $con);
            if(!empty($profile)){
                foreach($profile as $p){
                    $p['catid'] = $p['profile_type'];
                    $p['title'] = $p['name'];
                    $p['thumbnail'] = $p['avatar'];
                    $p['media'] = $p['poster'];
                    $p['media_type'] = $distype;
                    unset($p['profile_type']);
                    unset($p['name']);
                    unset($p['avatar']);
                    unset($p['poster']);
                    
                    array_push($list, $p);
                }
            }else{
                $con = array(
                    'start' => $offset,
                    'limit' => $limit,
                    'where' => array('catid' => $catid),
                    'order_by' => 'arrange_id',
                );
                
                $videos = $db->getRows('videos', $con);
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
        
        //print_r($list);exit;
    }else if(isset($_GET['catid'])){
        $catid = $_GET['catid'];
        if($catid == '2'){
            $file = fopen("../explore.json", "r");
            //Output lines until EOF is reached
            while(! feof($file)) {
              $line = fgets($file);
            }
            fclose($file);
            $explore_videos = json_decode($line, true);
            $exp_video_id = implode(',', $explore_videos['explore_list']);
            
            $sql = 'SELECT * FROM videos WHERE id IN('.$exp_video_id.') ORDER BY FIELD(id,'.$exp_video_id.')';

            $videos = $db->customQuery($sql);
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
        }else{
            $con = array(
                'start' => $offset,
                'limit' => $limit,
                'where' => array('catid' => $catid),
                'order_by' => 'arrange_id',
            );
            
            $videos = $db->getRows('videos', $con);
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
            }else{
                $con = array(
                    'start' => $offset,
                    'limit' => $limit,
                    'where' => array('profile_type' => $catid, 'status' => 0),
                    'order_by' => 'id DESC',
                );
                $profile = $db->getRows('profiles', $con);
                if(!empty($profile)){
                    foreach($profile as $p){
                        $p['catid'] = $p['profile_type'];
                        $p['title'] = $p['name'];
                        $p['thumbnail'] = $p['avatar'];
                        $p['media'] = $p['poster'];
                        $p['media_type'] = $distype;
                        unset($p['profile_type']);
                        unset($p['name']);
                        unset($p['avatar']);
                        unset($p['poster']);
                        
                        array_push($list, $p);
                    }
                }
            }
        }
    }
    $path = $db->getBasePath()."/";
}catch(Exception $e) {
    $list = '';
}

if(!empty($list) && count($list) > 0){
    echo json_encode(array("status" => 200, "msg" => "Data found.", "video_list" => $list, 'path' => $path));
}else{
    echo json_encode(array("status" => 201, "msg" => "No data found."));
}