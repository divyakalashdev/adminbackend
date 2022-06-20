<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../functions.php';

if(sendEmail('Sunni Kumar', 'verify@divyakalash.com', "Feedback", "Hi! how are you?")){
    echo "Mail Sent";
}else{
    echo "Failed";
}