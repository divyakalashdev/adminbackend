<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../DB.class.php';
$db = new DB;

if(isset($_POST['sendcode']) && $_POST['sendcode'] == 'yes'){
    $email = $_POST['email'];
    
    $con = array(
        'email' => $email,
        'return_type' => 'single'
    );
    $user = $db->getRows('users_email_verify', $con);
    if(!empty($user)){
        $code = $db->generateRandomDigit(4);
        if ($db->update('users_email_verify', array('code' => $code, 'status' => 0), array('email' => $email))) {
            $resp['message'] = "Verification code sent on email.";
            $resp['type'] = "success";
        }else{
            $resp['message'] = "1 Failed to send verification code. Please try later.";
            $resp['type'] = "error";
        }
    }else{
        $code = $db->generateRandomDigit(4);
        $rs = $db->sendEmailVerifyOTP($email, $code);
        if($rs == 'success'){
            $emaildata['email'] = $email;
            $emaildata['code'] = $code;
            if($db->insert('users_email_verify', $emaildata)){
                $resp['message'] = "Verification code sent on email.";
                $resp['type'] = "success";
            }else{
                $resp['message'] = "2 Failed to send verification code. Please try later.";
                $resp['type'] = "error";
            }
        }else{
            $resp['message'] = "3 Failed to send verification code. Please try later.";
            $resp['type'] = "error";
        }
    }
    echo json_encode($resp);
}else if(isset($_POST['email']) && isset($_POST['code'])){
    $email = $_POST['email'];
    $code = $_POST['code'];
    
    $con = array(
        'where' => array('email' => $email, 'code' => $code),
        'return_type' => 'single'
    );
    $user = $db->getRows('users_email_verify', $con);
    
    if(!is_null($user)){
        if ($db->update('users_email_verify', array('code' => 0, 'status' => 1), array('email', $email))) {
            $resp['message'] = "Verification code verified.";
            $resp['type'] = "success";
        }else{
            $resp['message'] = "Failed to send verification code. Please try later.";
            $resp['type'] = "error";
        }
        $con = array(
                'email' => $email,
                'return_type' => 'single'
            );
        $checkuser = $db->getRows('appusers', $con)->first();
        if(empty($checkuser)){
            $data['email'] = $email;
            $data['register_source'] = 'email';
            $db->insert('appusers', $data);
            
            $checkuser = $db->getRows('appusers', $con)->first();
        }
        if(!empty($checkuser)){
            $resp['message'] = "User registered successfully.";
            $resp['type'] = "success";
            $checkuser['dob'] = date('d-m-Y', strtotime($checkuser['dob']));
            $resp['userinfo'] = $checkuser;
        }else{
            $resp['message'] = "Failed to register user please try later";
            $resp['type'] = "error";
        }
    }else{
        $resp['message'] = "Invalid verification code.";
        $resp['type'] = "error";
    }
    echo json_encode($resp);
} else {
    $status = "error";
    $errorMsg = "Invalid request";
    echo json_encode(array('msg' => $errorMsg, 'type' => $status));
}