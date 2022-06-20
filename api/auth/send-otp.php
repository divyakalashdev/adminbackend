<?php
/*header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");*/


/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
include '../../DB.class.php';

/*Send OTP to user mobile by MSG91 API*/
if(isset($_POST['mobileno']) && !empty($_POST['mobileno'])){
    $mobileno = "91".$_POST['mobileno'];
    $mobiles = array('918700389200', '917065566757', '919555339993', '917669995666', '917669995665');
    if(in_array($mobileno, $mobiles)){
        echo '{"request_id":"316c6f6f696f323937333132","type":"success"}';
    }else{
        $authkey = MSG91_AUTH_KEY;
        $template_id = SMS_TEMPLATE_ID;
        $curl = curl_init();
        /*curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.msg91.com/api/v5/otp?authkey=$authkey&template_id=$template_id&mobile=$mobileno",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));*/
        
        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=$template_id&mobile=$mobileno&authkey=$authkey",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          //CURLOPT_POSTFIELDS => "{\"Value1\":\"Param1\",\"Value2\":\"Param2\",\"Value3\":\"Param3\"}",
          CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
          ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo json_encode(array("type" => "error", "message" => $err));
        } else {
            echo $response;
            //echo '{"request_id":"316c6f6f696f323937333132","type":"success"}';
        }
    }
}else{
    echo json_encode(array("type" => "error", "message" => 'Invalid request'));
}