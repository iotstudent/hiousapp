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
require "../../vendor/autoload.php";

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$rider = new Rider($db);

//get posted data
$data = json_decode(file_get_contents("php://input"));
$rider->name = $data->name;
$rider->email = $data->email;
$rider->phone_number = $data->phone_number;
$rider->password = $data->password;
$rider->confirm_password = $data->confirm_password;

$emailExist = $rider->emailExists();

if(!$emailExist){
    if($rider->password == $rider->confirm_password){

        // create the user
        if(
            !empty($rider->name) &&
            !empty($rider->email) &&
            !empty($rider->phone_number) &&
            !empty($rider->password) &&
            $rider->create()
        ){


            // generate verification code 
            $code = $rider->randString(5);

            //insert into db
            $rider->insertWithMail('verify_code',$code,$rider->email);

            //send verification mail
            sendVerificationEmail($rider->email,$code);
        
            // set response code
            http_response_code(200);
        
            // display message: user was created
            echo json_encode(array("message" => "Account Created Successfully and Verifcation Code Sent To Mail."));


        }
        
        // message if unable to create user
        else{
        
            // set response code
            http_response_code(400);
        
            // display message: unable to create user
            echo json_encode(array("message" => "Unable To Create Account Some fields are missing."));
        }

    } else{
         // set response code
        http_response_code(400);
    
        // display message: unable to create user
        echo json_encode(array("message" => " Password does not match."));
    }

}else{
     
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Email already exist."));
}


