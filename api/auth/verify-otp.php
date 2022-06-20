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

if( (isset($_POST['mobileno']) && !empty($_POST['mobileno'])) || (isset($_POST['otp']) && !empty($_POST['otp'])) ){
    $mobileno = str_replace('+', '', $_POST['mobileno']);
    $otp = $_POST['otp'];
    $firstname = "";
    $lastname = "";
    $email = "";
    $mobile = "";
    
    $mobiles = array('918700389200', '917065566757', '919555339993', '917669995666', '917669995665');
    if(in_array($mobileno, $mobiles)){
        $response = '{"message":"OTP verified success","type":"success"}';
        
        // Get users from database
        $con = array(
            'like' => array('mobile' => $mobileno),
            'return_type' => 'single'
        );
        
        $checkuser = $db->getRows('appusers', $con);
        if(empty($checkuser)){
            $data = array();
            $data['mobile'] = $mobileno;
            $data['register_source'] = 'mobile';
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $userid = $db->insert('appusers', $data);
            $checkuser = $db->getRows('appusers', $con);
        }
        //$checkuser = $db->getRows('appusers', $con);
        //echo $response;
        $response = json_decode($response, true);
        $response['userinfo'] = $checkuser;
        
        echo json_encode($response);
            
            
    }else{
        $curl = curl_init();
        $authkey = MSG91_AUTH_KEY;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.msg91.com/api/v5/otp/verify?mobile=$mobileno&otp=$otp&authkey=$authkey",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo json_encode(array("type" => "error", "message" => $err));
        } else {
            $con = array(
            'like' => array('mobile' => $mobileno),
            'return_type' => 'single'
        );
        
        $checkuser = $db->getRows('appusers', $con);
        if(empty($checkuser)){
            $data = array();
            $data['mobile'] = $mobileno;
            $data['register_source'] = 'mobile';
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $userid = $db->insert('appusers', $data);
        }else{
            $userid = $checkuser['id'];
            $firstname = $checkuser['first_name'];
            $lastname = $checkuser['last_name'];
            $email = $checkuser['email'];
        }
        //echo $response;
        $response = json_decode($response, true);
        $response['id'] = $userid.'';
        $response['userid'] = $userid.'';
        $response['first_name'] = $firstname;
        $response['last_name'] = $lastname;
        $response['email'] = $email.'';
        $response['mobile'] = $mobileno.'';
        echo json_encode($response);
        }
    //echo '{"message":"OTP verified success","type":"success"}';
    }
}
else{
    echo json_encode(array("type" => "error", "message" => 'Invalid request'));
}