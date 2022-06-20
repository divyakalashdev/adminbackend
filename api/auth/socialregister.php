<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
include '../../DB.class.php';
$db = new DB;
//print_r($_POST['logintype']);exit;

if (!empty($_POST['logintype']) && !empty($_POST['email'])) {
    $email = $_POST['email'];
    $filepath = '';
    // Get users from database
    $con = array(
        'where' => array('email' => $email),
        'return_type' => 'single'
    );
    $checkuser = $db->getRows('appusers', $con);
    if(empty($checkuser)){
        $firstname = $_POST['firstname'];
        if(isset($_POST['midname'])){
            $midname = $_POST['midname'];
        }else{
            $midname = "";
        }
        $lastname = $_POST['lastname'];
        $logintype = $_POST['logintype'];
        if(isset($_POST['picurl'])){
            $pic = $_POST['picurl'];
            $profile_pic = "profile_".time().".jpg";
            $filepathpic = 'img/photos/'.$profile_pic;
            $filepath = '../img/photos/'.$profile_pic;
            $content = curl_get_file_contents($pic);
            //file_put_contents('../img/photos/' . $profile_pic, file_get_contents($pic));
            file_put_contents('../img/photos/' . $profile_pic, $content);
        }
        $data['first_name'] = $firstname;
        $data['middle_name'] = $midname.'';
        $data['last_name'] = $lastname;
        $data['email'] = $email;
        $data['profile_pic'] = $filepathpic;
        $data['register_source'] = $logintype;
        $userid = $db->insert('appusers', $data);
        
        $con = array(
            'where' => array('id' => $userid),
            'return_type' => 'single'
        );
        $user = $db->getRows('appusers', $con);
        if (empty($user)) {
            $resp['message'] = "Failed to register user";
            $resp['type'] = "error";
        }else{
            $resp['message'] = "User registered successfully.";
            $resp['type'] = "success";
            $user['dob'] = date('d-m-Y', strtotime($user['dob']));
            $resp['userinfo'] = $user;
        }
    }else{
        $firstname = $_POST['firstname'];
        if(isset($_POST['midname'])){
            $midname = $_POST['midname'];
        }else{
            $midname = "";
        }
        $lastname = $_POST['lastname'];
        $logintype = $_POST['logintype'];
        $profiledata = array('first_name' => $firstname, 'last_name' => $lastname, 'middle_name' => $midname);
        if(isset($_POST['picurl'])){
            $pic = $_POST['picurl'];
            $profile_pic = "profile_".time().".jpg";
            
            $filepathpic = '/img/photos/'.$profile_pic;
            $filepath = '../img/photos/'.$profile_pic;
            //file_put_contents('../img/photos/'. $profile_pic, file_get_contents($pic));
            $content = curl_get_file_contents($pic);
            file_put_contents('../img/photos/' . $profile_pic, $content);
            unlink('..'.$checkuser['profile_pic']);
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
            $resp['type'] = "success";
            $user['dob'] = date('d-m-Y', strtotime($user['dob']));
            $resp['userinfo'] = $user;
        }
    }
    echo json_encode($resp);
}else{
    echo json_encode(array("type" => "error", "message" => 'Invalid request'));
}

function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return FALSE;
}