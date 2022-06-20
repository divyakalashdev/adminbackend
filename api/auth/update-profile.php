<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//print_r($_POST);exit;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../DB.class.php';
$db = new DB;

if(isset($_POST['newregisterupdate']) && $_POST['newregisterupdate'] == 'yes'){
    $userid = $_POST['userid'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $dob = date('Y-m-d', strtotime(str_replace("/", "-", $_POST['dob'])));
    if($db->update('appusers', ['first_name' => $firstname, 'last_name' => $lastname, 'gender' => $gender, 'dob' => $dob], array('id' => $userid))){
        
        $user = $db->getRows('appusers', array('id' => $userid, 'return_type' => 'single'));
        
        $resp['message'] = "Info updated successfully.";
        $resp['type'] = "success";
        $user['dob'] = date('d-m-Y', strtotime($user['dob']));
        $resp['userinfo'] = $user;
    }else{
        $resp['message'] = "Failed to update info please try later";
        $resp['type'] = "error";
    }
    echo json_encode($resp);
}else if( (isset($_POST['update_profile']) && !empty($_POST['update_profile']))){
    $userid = $_POST['userid'];
    $first_name = $_POST['firstname'];
    $last_name = $_POST['lastname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    // Get users from database
    $con = array(
        'where' => array('id' => $userid),
        'return_type' => 'single'
    );
    
    $checkuser = $db->getRows('appusers', $con);
    if(!empty($checkuser)){
        $data = array();
        if(isset($_POST['update_pic']) && $_POST['update_pic'] == 'y' && isset($_FILES['image']['name'])){
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'profile_'.time().".".$ext;
            $imagePath = "../img/photos/".$image;
            if(move_uploaded_file($_FILES['image']['tmp_name'],$imagePath)){
                unlink("../".$checkuser['profile_pic']);
                $data['profile_pic'] = "img/photos/".$image;
            }
            
        }
  
        $data['first_name'] = $first_name;
        $data['last_name'] = $last_name;
        $data['gender'] = $gender;
        $data['dob'] = $dob;
        $data['mobile'] = $mobile;
        $data['email'] = $email;
        $data['city'] = $city;
        $data['state'] = $state;
        $data['country'] = $country;
        $data['updated_at'] = date("Y-m-d H:i:s");
        $userid = $db->update('appusers', $data, array('id' => $userid));
    }
    $checkuser = $db->getRows('appusers', $con);
    $resp['message'] = "Info updated successfully.";
    $resp['type'] = "success";
    $checkuser['dob'] = date('d-m-Y', strtotime($checkuser['dob']));
    $resp['userinfo'] = $checkuser;
    
    echo json_encode($resp);
}
else{
    echo json_encode(array("type" => "error", "message" => 'Invalid request'));
}