<?php
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:POST');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,application/json,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-with');

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Riders.php';
include_once '../../config/core.php';
require '../../vendor/autoload.php';

use \Firebase\JWT\JWT;

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$rider = new Rider($db);

// check email existence here

// get posted data
$data = json_decode(file_get_contents("php://input"));
$rider->ref_code = $data->ref_code;

$rider_id = $rider->getRiderIdWithRef($rider->ref_code);

if($rider_id){
    $token = array(
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "iss" => $issuer,
        "data" => array(
            "id" => $rider->id
        ));
     // set response code
     http_response_code(200);
 
     // generate jwt
     $jwt = JWT::encode($token, $key,'HS256');
     echo json_encode(
             array(
                 "message" => "Move ahead to change password",
                 "jwt" => $jwt
             )
         );
}else{
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "Wrong code"));
}