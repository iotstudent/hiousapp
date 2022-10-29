<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:PUT');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,application/json,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-with');

include_once '../../config/core.php';
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Riders.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$rider = new Rider($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jw
        $decoded = JWT::decode($jwt,new key ($key,'HS256'));

        // set user property values here
        $rider->password = $data->password;
        $rider->confirm_password = $data->confirm_password;
        $rider->id = $decoded->data->id;
        
        if($rider->password == $rider->confirm_password){
                // update user with data gotten
        if($rider->updatePassword()){
        
             // set response code
             http_response_code(200);

             // show success message
             echo json_encode(array("message" => "Password successfully changed"));
        }
         
        // message if unable to update user
        else{
            // set response code
            http_response_code(401);
         
            // show error message
            echo json_encode(array("message" => "Unable to change password."));
        }
        }
        
    }catch (Exception $e){
 
        // set response code
        http_response_code(401);
     
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}