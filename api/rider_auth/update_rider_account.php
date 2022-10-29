
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

//instantiate new rider
$rider = new rider($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$rider->account_number = $data->account_number;
$rider->bank_name = $data->bank_name;
$rider->bank_code = $data->bank_code;



// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show rider details
    try {
 
        // decode jw
        $decoded = JWT::decode($jwt,new key ($key,'HS256'));

        // get rider  from jwt token
        $rider->id = $decoded->data->id;
        
  
        // update rider with data gotten
        if($rider->updateAccountDetails()){
        
             // set response code
             http_response_code(200);
          
             // json response
             echo json_encode(
                     array(
                         "message" => "Account Updated",
                         "jwt" => $jwt
                     )
                 );
        }
         
        // message if unable to update rider
        else{
            // set response code
            http_response_code(401);
         
            // show error message
            echo json_encode(array("message" => "Unable to update Account."));
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