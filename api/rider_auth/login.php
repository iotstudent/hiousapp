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
require "../../vendor/autoload.php";


use \Firebase\JWT\JWT;

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$rider = new Rider($db);

// check email existence here

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$rider->email = $data->email;
$email_exists = $rider->emailExists();
 
// generate json web token

// check if email exists and if password is correct
if($email_exists && password_verify($data->password, $rider->password)){
 
    $token = array(
       "iat" => $issued_at,
       "exp" => $expiration_time,
       "iss" => $issuer,
       "data" => array(
           "id" => $rider->id,
           "name" => $rider->name,
           "email" => $rider->email
       )
    );
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, $key,'HS256');
    echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt
            )
        );
 
    } else{
 
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
 