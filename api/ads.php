<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../DB.class.php';
$file = fopen("../ads.json", "r");

//Output lines until EOF is reached
while(! feof($file)) {
    $line = fgets($file);
}
fclose($file);

$adstatus = json_decode($line, true);
    
if(isset($_REQUEST['list']) && $_REQUEST['list'] == 1){
    $db = new DB;
    try{
        // Get users from database
        $con = array(
            'order_by' => 'sequence ASC',
            'where' => array('status' => 0),
            'wherenot' => array('screen_name' => 'detail')
        );
        $ad_list = array();
        $ads = $db->getRows('ads', $con);
        foreach($ads as $a){
            unset($a['click']);
            unset($a['created_at']);
            unset($a['updated_at']);
            array_push($ad_list, $a);
        }
    }catch(Exception $e) {
        $ad_list = '';
    }
    $path = $db->getBasePath()."/";
    if(/*$adstatus['google_ads'] == "1" && */!empty($ad_list) && count($ad_list) > 0){
        echo json_encode(array("status" => 200, "msg" => "Some ads found.", "ads_list" => $ad_list, 'google_ad' => $adstatus['google_ads'], 'client_ad' => $adstatus['client_ads'], 'path' => $path));
    }else{
        echo json_encode(array("status" => 200, "msg" => "No ads added yet. Running Google Ads", 'google_ad' => $adstatus['google_ads'], 'client_ad' => $adstatus['client_ads']));
    }
}else if(isset($_REQUEST['adsbyid'])){
    $id = trim(strip_tags($_REQUEST['adsbyid']));
    $db = new DB;
    try{
        // Get users from database
        /*$con = array(
            'where' => array('category_id' => $id),
            'return_type' => 'single'
        );*/
        $path = $db->getBasePath()."/";
        $sql = 'SELECT * FROM ads LEFT JOIN categories ON categories.id = ads.category_id WHERE ads.category_id = '.$id.' and categories.ad_status = 0';
        $ads = $db->customQuery($sql);//print_r($ads);exit;
        //$ads = $db->getRows('ads', $con);
        if(/*$adstatus['google_ads'] == "1" && */!empty($ads)){
            unset($ads['click']);
            unset($ads['created_at']);
            unset($ads['updated_at']);
            echo json_encode(array("status" => 200, "msg" => "Ad found", 'ad' => $ads, 'google_ad' => $adstatus['google_ads'], 'client_ad' => $adstatus['client_ads'], 'path' => $path));
        }else{
            echo json_encode(array("status" => 200, "msg" => "No ads added yet.", 'google_ad' => $adstatus['google_ads'], 'client_ad' => $adstatus['client_ads']));
        }
    }catch(Exception $e) {
        $ads = '';
    }
}else if(isset($_REQUEST['count']) && $_REQUEST['count'] == 1 && isset($_REQUEST['aid'])){
    $id = trim(strip_tags($_REQUEST['aid']));
    $db = new DB;
    try{
        // Get users from database
        $con = array(
            'id' => $id,
            'return_type' => 'single'
        );
        
        $ads = $db->getRows('ads', $con);
        //print_r($ads);
        
        $click = $ads['click'] + 1;
        
        $condition['id'] = $id;
        
        if($db->update('ads', array('click' => $click), array('id' => $id))){
            echo json_encode(array("status" => 200, "msg" => "Ad Click updated"));
        }else{
            echo json_encode(array("status" => 201, "msg" => "Failed to update ad click"));
        }
    }catch(Exception $e) {
        $ads = '';
    }
}