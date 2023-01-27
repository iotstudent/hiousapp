<?php
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:POST');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,application/json,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-with');

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Vendors.php';
include_once '../../config/core.php';
require '../../vendor/autoload.php';

use \Firebase\JWT\JWT;

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$vendor = new Vendor($db);

// check email existence here

// get posted data
$data = json_decode(file_get_contents("php://input"));
$vendor->verify_code = $data->verify_code;

$vendor_id = $vendor->getVendorIdVer($vendor->verify_code);

if($vendor_id){
     // set response code
     http_response_code(200);
 
     // tell the user login failed
     echo json_encode(array("message" => "verification successful"));
}else{
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "Wrong code"));
}