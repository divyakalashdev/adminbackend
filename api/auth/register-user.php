<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../../DB.class.php';
$db = new DB;

//Send failed response if request is not valid
if (isset($_POST['logintype']) && $_POST['logintype'] != '') {
    $email = $_POST['email'];
    $filepath = '';
    // Get users from database
    $con = array(
        'where' => array('email' => $email),
        'return_type' => 'single'
    );
    $checkuser = $db->getRows('appusers', $con);print_r($checkuser);exit;
    if(empty($checkuser)){
        $firstname = $_POST['firstname'];
        $midname = $_POST['midname'];
        $lastname = $_POST['lastname'];
        $logintype = $_POST['logintype'];
        if(isset($_POST['picurl'])){
            $pic = $_POST['picurl'];
            $profile_pic = "profile_".time().".jpg";
            $filepathpic = 'img/photos/'.$profile_pic;
            $filepath = '../img/photos/'.$profile_pic;
            file_put_contents('../img/photos/' . $profile_pic, file_get_contents($pic));
        }
        $data['first_name'] = $firstname;
        $data['middle_name'] = $midname;
        $data['last_name'] = $lastname;
        $data['$data'] = $email;
        $data['profile_pic'] = $filepathpic;
        $data['register_source'] = $logintype;
        $userid = $db->insert('appusers', $data);
        
        $con = array(
            'where' => array('id' => $userid),
            'return_type' => 'single'
        );
        $user = $db->getRows('appusers', $con);
        if (empty($user)) {
            $resp['message'] = "Invalid token";
            $resp['type'] = "error";
        }else{
            $resp['message'] = "User registered successfully.";
            $resp['type'] = "success";
            $resp['userinfo'] = $user;
        }
    }else{
        $firstname = $_POST['firstname'];
        $midname = $_POST['midname'];
        $lastname = $_POST['lastname'];
        $logintype = $_POST['logintype'];
        $profiledata = array('first_name' => $firstname, 'last_name' => $lastname, 'middle_name' => $midname);
        if(isset($_POST['picurl'])){
            $pic = $_POST['picurl'];
            $profile_pic = "profile_".time().".jpg";
            
            $filepathpic = '/img/photos/'.$profile_pic;
            $filepath = '../img/photos/'.$profile_pic;
            file_put_contents('../img/photos/'. $profile_pic, file_get_contents($pic));
            $profiledata['profile_pic'] = $filepathpic;
        }
        $db->update('appusers', $profiledata, array('id' => $checkuser['id']));
        $con = array(
            'where' => array('id' => $checkuser['id']),
            'return_type' => 'single'
        );
        $user = $db->getRows('appusers', $con);
        if (empty($user)) {
            $resp['message'] = "Invalid token";
            $resp['type'] = "error";
        }else{
            $resp['message'] = "Info updated successfully.";
            $user['dob'] = date('d-m-Y', strtotime($user['dob']));
            $resp['userinfo'] = $user;
            $resp['type'] = "success";
        }
    }
    echo json_encode($resp);
}else{
    echo json_encode(array("type" => "error", "message" => 'Invalid request'));
}