<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:POST');
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

//instantiate new rider
$rider = new rider($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show rider details
    try {
 
        // decode jw
        $decoded = JWT::decode($jwt,new key ($key,'HS256'));

        // set rider property values here
        $rider->id = $decoded->data->id;
        
  
        // update rider with data gotten
        if($rider->getriderDetails($rider->id)){
          
             // set response code
             http_response_code(200);
        
             echo json_encode(
                     array(
                         "message" => "rider Data",
                         "name" => $rider->name,
                         "email" => $rider->email,
                         "gender" => $rider->gender,
                         "dob" => $rider->dob,
                         "location" => $rider->location,
                         "phone_number" => $rider->phone_number,
                         "about" => $rider->about,
                         "photo" => $rider->photo,
                         "wallet" => $rider->wallet,
                         "account_number" => $rider->account_number,
                         "bank_code" => $rider->bank_code,
                         "bank_name" => $rider->bank_name,
                     )
                 );
        }
         
        // message if unable to update rider
        else{
            // set response code
            http_response_code(401);
         
            // show error message
            echo json_encode(array("message" => "Unable To Fetch rider Data."));
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