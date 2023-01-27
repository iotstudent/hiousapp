<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:DELETE');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,application/json,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-with');

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Product.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new vendor
$product = new Product($db);

//get posted data
$data = json_decode(file_get_contents("php://input"));
$product->product_id = $data->product_id;

if($product->product_id){
    try{
        if($product->delete($product->product_id))
        {
            echo json_encode(array("message" => "Product deleted successfully","status" => true));	
        }
    }catch (Exception $e){
 
        // set response code
        http_response_code(401);
        // show error message
        echo json_encode(array(
            "error" => $e->getMessage()
        ));
    }
}else{
     // set response code
     http_response_code(401);
                
     // show error message
     echo json_encode(array(
         "message" => "No Product selected"
     ));
}