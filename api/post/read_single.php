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

//GET ID
$post->id = isset($_GET['id']) ? $_GET['id'] : die();

//get post

$post->readSingle();

//create array

$post_arr = array(
    'id' => $post->id,
    'title' => $post->title,
    'body' => $post->body,
    'author' => $post->author,
    'category_id' => $post->category_id,
    'category_name' => $post->category_name
);

//make json
print_r(json_encode($post_arr));