<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'DB.class.php';
$db = new DB();

/* ============================== Profile Operations Starts here ==============================*/
if(isset($_POST['save_quote']) && $_POST['save_quote'] == "save"){
    $quote = trim(strip_tags($_POST['quote']));
    $quotewrittenby = trim(strip_tags($_POST['quotewrittenby']));
    
    $data = array();
    $data['quotes'] = $quote;
    $data['quotesby'] = $quotewrittenby;
    
    if($db->insert('appusers', $data)){
        echo "OK";
    }else{
        echo "FAILED";
    }
} else if(isset($_POST['update_quote']) && $_POST['update_quote'] == "update"){
    $uid = trim(strip_tags($_POST['qid']));
    $quote = trim(strip_tags($_POST['quote']));
    $writtenby = trim(strip_tags($_POST['writtenby']));
    $con['id'] = $uid;
    
    $conditions['where'] = array('id' => $uid);
    $conditions['return_type'] = 'single';
    $profile = $db->getRows('appusers', $conditions);
    $data = array();
    $data['quotes'] = $quote;
    $data['quotesby'] = $writtenby;
    if($db->update('appusers', $data, $con)){
        echo "OK";
    }else{
        echo "FAILED";
    }
    
} else if(isset($_POST['delete_quote']) && $_POST['delete_quote'] == "delete"){
    $uid = trim(strip_tags($_POST['delete_user_id']));
    
    $conditions['where'] = array('id' => $uid);
    $conditions['return_type'] = 'single';
    $profile = $db->getRows('appusers', $conditions);
    if($profile != null){
        $con['id'] = $uid;
        if($db->delete('appusers', $con)){
            echo "OK";
        }else{
            echo "FAILED";
        }
    }else{
        echo "FAILED";
    }
}