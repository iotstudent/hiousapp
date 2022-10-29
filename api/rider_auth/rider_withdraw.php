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
include_once '../../config/wallet.php';
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
$amount = $data->amount;

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
        
  
        // get rider details
        if($rider->getRiderDetails($rider->id)){

            if($amount>$rider->wallet){
                http_response_code(401);
                // show error message
                echo json_encode(array("message" => "Insufficient Funds."));

                die();
            }else{

                PaymentGateway($amount,$rider->bank_code,$rider->account_number,"rider_withdraw.php");

                if($httpcode != 200){
                    $dataobj = json_decode($response,true);
                    echo $dataobj["status"]; 
                    die();   
                  }else {
                    $dataobj = json_decode($response,true);
                    var_dump($dataobj);
                    $rider->updateWallet($amount,$rider->wallet);
                    $type="Withdrawal";
                    $description=".'$amount'. Naira withdrawn into your bank account";
                    $reference = $dataobj["data"]["reference"]; 
                    $created_at = date("h:i:s") ;
                   $rider->riderTransaction($amount,$reference,$created_at,$description,$type,$rider->id);
                                //  set response code
                        http_response_code(200);
                    
                        // json response
                        echo json_encode(
                                array(
                                    "message" => " Money Transferred Successfully",
                                    "created_at"=> $created_at,
                                    "reference"=> $reference
                                )
                            );

    
                }  
              

            }       
           
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