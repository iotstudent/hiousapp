<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Mehtods:POST');
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../config/core.php';
include_once '../../models/Product.php';
require "../../vendor/autoload.php";

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

//instantiate db
$database = new Database();
$db = $database->connect();


$product = new Product($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

$product->product_name = $_POST['product_name'];
$product->product_price = $_POST['product_price'];
$product->product_category= $_POST['product_category'];

// product image
$fileName  =  $_FILES['image']['name'];
$tempPath  =  $_FILES['image']['tmp_name'];
$fileSize  =  $_FILES['image']['size'];

// get jwt
$jwt =$_POST['jwt'];

if($jwt ){	

    if($product->product_name && $product->product_price && $product->product_category){
             // if decode succeed, show vendor details
            try {

                // decode jw
                $decoded = JWT::decode($jwt,new key ($key,'HS256'));

                // set vendor property values here
                $product->vendor_id = $decoded->data->id;

                // image upload
                $upload_path = 'product_pic/'; // set upload folder path 
				$base_path  = 'hiousapp.com/api/vendor_auth/' ; // set base path
				
                if($fileName){

                    $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension
					
                    // valid image extensions
                    $valid_extensions = array('jpeg', 'jpg', 'png'); 
                
                
                    // allow valid image file formats
                    if(in_array($fileExt, $valid_extensions))
                    {				
                        
                            // check file size '5MB'
                            if($fileSize < 5000000){
                                move_uploaded_file($tempPath, $upload_path . $fileName); // move file from system temporary path to our upload folder path 
                            }
                            else{	
                                // set response code
                                http_response_code(401);
                                $errorMSG = json_encode(array("message" => "Sorry, your file is too large, please upload 5 MB size", "status" => false));	
                                echo $errorMSG;
                            }
                            
                            $product->product_pic = $base_path . $upload_path . $fileName;
                    }
                    else
                    {		
                        // set response code
                        http_response_code(401);
                        $errorMSG = json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed"));	
                        echo $errorMSG;		
                    }

                }
                // if no error caused, continue ....
                if(!isset($errorMSG))
                {
                    $product->create();
                    echo json_encode(array("message" => "Product uploaded successfully","status" => true));	
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
    }else{
        // set response code
        http_response_code(401);
                
        // show error message
        echo json_encode(array(
            "message" => "product name, product price and category fields are important"
        ));
    }
}
?>




