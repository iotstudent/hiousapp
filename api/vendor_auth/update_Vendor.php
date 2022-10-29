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
include_once '../../models/Vendors.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new vendor
$vendor = new vendor($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$vendor->name = $data->name;
$vendor->email = $data->email;
$vendor->location = $data->location;
$vendor->phone_number = $data->phone_number;
$vendor->about = $data->about;



// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show vendor details
    try {
 
        // decode jw
        $decoded = JWT::decode($jwt,new key ($key,'HS256'));

        // get vendor if from jwt token
        $vendor->id = $decoded->data->id;
        
  
        // update vendor with data gotten
        if($vendor->update()){
        
             // set response code
             http_response_code(200);
          
             // json response
             echo json_encode(
                     array(
                         "message" => "vendor Updated",
                         "jwt" => $jwt
                     )
                 );
        }
         
        // message if unable to update vendor
        else{
            // set response code
            http_response_code(401);
         
            // show error message
            echo json_encode(array("message" => "Unable to update vendor."));
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