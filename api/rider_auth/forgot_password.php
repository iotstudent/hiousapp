<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:POST');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,application/json,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-with');

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../config/sm.php';
include_once '../../models/Riders.php';




//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new rider
$rider = new Rider($db);


// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$rider->email = $data->email;
$email_exists = $rider->emailExists();
 

// check if email exists

if($email_exists){

    // code for verifying mail
    $code=$rider->randString(5);
    
    //insert into rider table reset code 
    $rider->insert('ref_code',$code);

    sendResetEmail($rider->email,$code);
     // set response code
     http_response_code(200);
 
     // tell the user mail sent
     echo json_encode(array("message" => " If you have an account with us, a reset code has been sent to it  "));
} else{
 
    // set response code
    http_response_code(401);
 
    // tell the user account does not exist
    echo json_encode(array("message" => "You do not have an account with us "));
}
 