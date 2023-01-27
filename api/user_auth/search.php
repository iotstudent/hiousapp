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
include_once '../../models/Users.php';

//instantiate db
$database = new Database();
$db = $database->connect();

//instantiate new user
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

$search = $data->search;

if ($search){
    try
    {
    $result=$user->searchForVendor($search);
    if($result->rowCount()>0){

          //vendpr array
          $vendor_arr['data'] = array();
          // set response code
          http_response_code(200);

          //fetch data as associtive array
          while($row = $result->fetch(PDO::FETCH_ASSOC)) {
              extract($row);
              //extract variable turns the array object value and saves it in a vairable of same name as object
              $vendor_item = array(
                      "vendor_id" => $vendor_id,
                      "vendor_name" => $name,
                      "vendor_location" =>$location,
                      "vendor_pic" =>$pic_1
              );

              //push data
              array_push($vendor_arr['data'],$vendor_item);
          }
          // turn to json & output
          echo json_encode($vendor_arr);
    }
    // http_response_code(200);
    // echo json_encode(array(
    //     "query"=>$user->search,
    //     "queryname"=>$user->searchname,
    // ));

    }
    catch (Exception $e){
 
        // set response code
        http_response_code(401);
     
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}


