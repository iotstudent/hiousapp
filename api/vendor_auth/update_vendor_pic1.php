<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");

include_once '../../config/core.php';
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Vendors.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new vendor
$vendor = new vendor($db);

// get jwt
$jwt =$_POST['jwt'];

//get file content 

$fileName  =  $_FILES['image']['name'];
$tempPath  =  $_FILES['image']['tmp_name'];
$fileSize  =  $_FILES['image']['size'];


if($jwt){	

		 // if decode succeed, show vendor details
		try {
 
			// decode jw
			$decoded = JWT::decode($jwt,new key ($key,'HS256'));
	
			// set vendor property values here
			$vendor->id = $decoded->data->id;

			if(empty($fileName))
			{
				$errorMSG = json_encode(array("message" => "please select image", "status" => false));	
				echo $errorMSG;
			}
			else
			{
				$upload_path = 'pic1/'; // set upload folder path 
				
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
					
				}
				else
				{		
					// set response code
					http_response_code(401);
					$errorMSG = json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed", "status" => false));	
					echo $errorMSG;		
				}
			}
					
			// if no error caused, continue ....
			if(!isset($errorMSG))
			{
				$vendor->insert("pic_1",$upload_path . $fileName);
				echo json_encode(array("message" => "Image Uploaded Successfully","status" => true));	
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
?>