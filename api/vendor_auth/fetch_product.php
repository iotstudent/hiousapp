<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:GET');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,application/json,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-with');

include_once '../../config/core.php';
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Product.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$product = new Product($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";

// if jwt is not empty
if($jwt){

    try {
 
        // decode jw
        $decoded = JWT::decode($jwt,new key ($key,'HS256'));

        // set vendor property values here
        $product->vendor_id = $decoded->data->id;
        $result=$product->getAllProducts();

        $num =$result->rowCount();
        
        // update user with data gotten
        if($num > 0){

            //user array
            $product_arr['data'] = array();
            // set response code
            http_response_code(200);

            //fetch data as associtive array
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                //extract variable turns the array object value and saves it in a vairable of same name as object
                $product_item = array(
                        "product_id" => $product_id,
                        "product_name" => $product_name,
                        "product_price" =>$product_price,
                        "product_category" =>$product_category,
                        "product_pic" =>$product_pic
                );

                //push data
                array_push($product_arr['data'],$product_item);
            }
            // turn to json & output
            echo json_encode($product_arr);

        }else{
            // set response code
            http_response_code(401);

            // show error message
            echo json_encode(array("message" => "Unable To Fetch Employee Data."));
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
