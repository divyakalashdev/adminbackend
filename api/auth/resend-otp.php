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

if( (isset($_POST['mobileno']) && !empty($_POST['mobileno'])) && (isset($_POST['type']) && !empty($_POST['type'])) ){
    $mobileno = "91".$_POST['mobileno'];
    $mobiles = array('918700389200', '917065566757', '919555339993', '917669995666', '917669995665');
    if(in_array($mobileno, $mobiles)){
        echo '{"message":"OTP already verified","type":"error"}';
    }else{
        $type = $_POST['type'];
        if($type == 't'){
            $type = 'text';
        }else if($type == 'v'){
            $type = 'voice';
        }
        $curl = curl_init();
        $authkey = MSG91_AUTH_KEY;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.msg91.com/api/retryotp.php?authkey=$authkey&mobile=$mobileno&retrytype=$type",
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
            echo $response;
        }
        //echo '{"message":"OTP already verified","type":"error"}';
    }
}
else{
    echo json_encode(array("type" => "error", "message" => 'Invalid request'));
}