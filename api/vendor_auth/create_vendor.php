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
include_once '../../models/Vendors.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new vendor
$vendor = new Vendor($db);

//get posted data
$data = json_decode(file_get_contents("php://input"));
$vendor->name = $data->name;
$vendor->email = $data->email;
$vendor->phone_number = $data->phone_number;
$vendor->password = $data->password;
$vendor->confirm_password = $data->confirm_password;

$emailExist = $vendor->emailExists();

if(!$emailExist){
    if($vendor->password == $vendor->confirm_password){

        // create the vendor
        if(
            !empty($vendor->name) &&
            !empty($vendor->email) &&
            !empty($vendor->phone_number) &&
            !empty($vendor->password) &&
            $vendor->create()
        ){
        
            // set response code
            http_response_code(200);
        
            // display message: vendor was created
            echo json_encode(array("message" => "Vendor was created."));
        }
        
        // message if unable to create vendor
        else{
        
            // set response code
            http_response_code(400);
        
            // display message: unable to create vendor
            echo json_encode(array("message" => "Unable to create vendor."));
        }

    } else{
         // set response code
        http_response_code(400);
    
        // display message: unable to create vendor
        echo json_encode(array("message" => " Password does not match."));
    }

}else{
     
    // set response code
    http_response_code(400);
 
    // display message: unable to create vendor
    echo json_encode(array("message" => "vendor with same Email already exist."));
}


