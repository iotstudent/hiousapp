<?php
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:POST');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,application/json,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-with');

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Users.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$user = new User($db);

//get posted data
$data = json_decode(file_get_contents("php://input"));
$user->name = $data->name;
$user->email = $data->email;
$user->phone_number = $data->phone_number;
$user->password = $data->password;
$user->confirm_password = $data->confirm_password;

$emailExist = $user->emailExists();

if(!$emailExist){
    if($user->password == $user->confirm_password){

        // create the user
        if(
            !empty($user->name) &&
            !empty($user->email) &&
            !empty($user->phone_number) &&
            !empty($user->password) &&
            $user->create()
        ){
        
            // set response code
            http_response_code(200);
        
            // display message: user was created
            echo json_encode(array("message" => "User was created."));
        }
        
        // message if unable to create user
        else{
        
            // set response code
            http_response_code(400);
        
            // display message: unable to create user
            echo json_encode(array("message" => "Unable to create user."));
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
    echo json_encode(array("message" => "User with same Email already exist."));
}


