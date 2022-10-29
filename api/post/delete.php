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
include_once '../../models/Post.php';


//instantiate DB and Connect

$database = new Database();
$db = $database->connect();


$post = new Post($db);

//get raw posted data

$data = json_decode(file_get_contents("php://input"));

//set ID to delete
$post->id = $data->id;


//update post
if($post->delete()){
    echo json_encode(array('message'=>'Post deleted'));
} else{
    echo json_encode(array('message'=>'Post Not deleted'));
}