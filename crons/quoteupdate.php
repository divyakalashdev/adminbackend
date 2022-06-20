<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../DB.class.php';
$db = new DB();
$con = array(
        'where' => array('status' => 0),
        'order_by' => 'id asc',
        'return_type' => 'single',
        'select' => 'id, quotes, quotesby'
    );
$quote = $db->getRows('dailyquotes', $con);

//('id', 'quotes', 'quotesby')
if(!empty($quote)){
    $detail = json_encode($quote, JSON_UNESCAPED_UNICODE);
    $quoteid = $quote['id'];
    $path = '../api/';
    $file = $path.'/quoteofday.txt';

    $myfile = fopen($file, "wa+") or die("Unable to open file!");
    fwrite($myfile, $detail);
    fclose($myfile);

    
    if( $db->update('dailyquotes', array('status' => 1), array('id' => $quoteid)) ){
        
        $response['type'] = "success";
        $response['message'] = "Quote status updated";
    }else{
        $response['type'] = "error";
        $response['message'] = "Failed to update quote status";
    }
    $response['type'] = "success";
        $response['message'] = "Quote status updated";
}else{
    $response['type'] = "error";
    $response['message'] = "Invalid request";
}
echo json_encode($response);

?>