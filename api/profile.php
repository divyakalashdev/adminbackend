<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
include '../DB.class.php';
$db = new DB;
$list = array();
if(isset($_REQUEST['profileid'])){
    try{
        $pid = $_REQUEST['profileid'];
        $con = array(
            'where' => array('profile_id' => $pid),
        );
        $posters = $db->getRows('profiles_images', $con);
        if(!empty($posters)){
            foreach($posters as $p){
                $poster['id'] = $p['id'];
                $poster['poster'] = $p['poster'];
                unset($p['profile_id']);
                unset($p['created_at']);
                unset($p['updated_at']);
                
                array_push($list, $poster);
            }
        }
    }catch(Exception $e) {
        $list = '';
    }
}
$path = $db->getBasePath()."/";
if(!empty($list) && count($list) > 0){
    echo json_encode(array("status" => 200, "msg" => "Data found.", "poster_list" => $list, 'path' => $path));
}else{
    echo json_encode(array("status" => 201, "msg" => "No data found.", 'path' => $path));
}