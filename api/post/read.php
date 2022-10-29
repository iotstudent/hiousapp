<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// include DB class and Post model
include_once '../../config/Database.php';
include_once '../../models/Post.php';


//instantiate DB and Connect

$database = new Database();
$db = $database->connect();

// instantiate blog post object
$post = new Post($db);

//blog post query
$result = $post->read();

// get row count
$num = $result->rowCount();

//check if any posts
if($num > 0) {
    //post array
    $post_arr = array();
    $post_arr['data'] = array();

    //fetch data as associtive array
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        //extract variable turns the array object value and saves it in a vairable of same name as object

        $post_item = array(
            'id' =>$id,
            'title' =>$title,
            'body' =>html_entity_decode($body),
            'author' =>$author,
            'category_id' =>$category_id,
            'category_name' =>$category_name
        );

        //push data
        array_push($post_arr['data'],$post_item);
    }

    // turn to json & output
    echo json_encode($post_arr);
} else{
    //no posts
    echo json_encode(
        array('message'=>'NO post found')
    );
}