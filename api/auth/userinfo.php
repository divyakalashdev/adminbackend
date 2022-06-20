<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../../DB.class.php';
$db = new DB;

//Send failed response if request is not valid
if (isset($_REQUEST['userid']) && $_REQUEST['userid'] != '') {
    $userid = $_REQUEST['userid'];
        
    $con = array(
        'where' => array('id' => $userid),
        'return_type' => 'single'
    );
    $user = $db->getRows('appusers', $con);
    
    if (empty($user)) {
        $resp['message'] = "Invalid user";
        $resp['type'] = "error";
    }else{
        $resp['message'] = "User found.";
        $resp['type'] = "success";
        $user['dob'] = date('d-m-Y', strtotime($user['dob']));
        $resp['userinfo'] = $user;
    }
    echo json_encode($resp);
}else{
    echo json_encode(array("type" => "error", "message" => 'Invalid request'));
}