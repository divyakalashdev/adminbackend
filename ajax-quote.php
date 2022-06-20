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
    
    if($db->insert('dailyquotes', $data)){
        echo "OK";
    }else{
        echo "FAILED";
    }
} else if(isset($_POST['update_quote']) && $_POST['update_quote'] == "update"){
    $qid = trim(strip_tags($_POST['qid']));
    $quote = trim(strip_tags($_POST['quote']));
    $writtenby = trim(strip_tags($_POST['writtenby']));
    $con['id'] = $qid;
    
    $conditions['where'] = array('id' => $qid);
    $conditions['return_type'] = 'single';
    $profile = $db->getRows('dailyquotes', $conditions);
    $data = array();
    $data['quotes'] = $quote;
    $data['quotesby'] = $writtenby;
    if($db->update('dailyquotes', $data, $con)){
        echo "OK";
    }else{
        echo "FAILED";
    }
    
} else if(isset($_POST['delete_quote']) && $_POST['delete_quote'] == "delete"){
    $qid = trim(strip_tags($_POST['delete_quote_id']));
    
    $conditions['where'] = array('id' => $qid);
    $conditions['return_type'] = 'single';
    $profile = $db->getRows('dailyquotes', $conditions);
    if($profile != null){
        $con['id'] = $qid;
        if($db->delete('dailyquotes', $con)){
            echo "OK";
        }else{
            echo "FAILED";
        }
    }else{
        echo "FAILED";
    }
}