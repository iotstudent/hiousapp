<?php

class Product {

    // db stuff
    private $conn;
    private $table = 'product';


    public $product_id;
    public $product_name;
    public $product_price;
    public $product_pic;
    public $product_category;
    public $vendor_id;



public function __construct($db){

     $this->conn = $db;
}

public function create(){

            // insert query
    $query = " INSERT INTO " . $this->table. "
            SET
                product_pic = :product_pic,
                product_name = :product_name,
                product_price = :product_price,
                vendor_id = :vendor_id,
                product_category = :product_category";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->product_name=htmlspecialchars(strip_tags($this->product_name));
    $this->product_price=htmlspecialchars(strip_tags($this->product_price));
    $this->product_category=htmlspecialchars(strip_tags($this->product_category));
    $this->vendor_id=htmlspecialchars(strip_tags($this->vendor_id));

    // bind the values
    $stmt->bindParam(':product_pic', $this->product_pic);
    $stmt->bindParam(':product_name', $this->product_name);
    $stmt->bindParam(':product_price', $this->product_price);
    $stmt->bindParam(':product_category', $this->product_category);
    $stmt->bindParam(':vendor_id', $this->vendor_id);

    
    if($stmt->execute()){
            return true;
        }

    print($stmt->error);
    return false;

}




public function update(){

    // insert query
$query = " UPDATE " . $this->table. "
    SET
        product_name = :product_name,
        product_price = :product_price,
        product_category = :product_category,
        product_pic = :product_pic
        WHERE product_id = :product_id";

// prepare the query
$stmt = $this->conn->prepare($query);

// sanitize
$this->product_name=htmlspecialchars(strip_tags($this->product_name));
$this->product_price=htmlspecialchars(strip_tags($this->product_price));
$this->product_category=htmlspecialchars(strip_tags($this->product_category));



// bind the values
$stmt->bindParam(':product_name', $this->product_name);
$stmt->bindParam(':product_price', $this->product_price);
$stmt->bindParam(':product_category', $this->product_category);
$stmt->bindParam(':product_pic', $this->product_pic);
$stmt->bindParam(':product_id', $this->product_id);



if($stmt->execute()){
    return true;
}

print($stmt->error);
return false;

}



public function delete($id){

    // insert query
    $query = " DELETE FROM " . $this->table. " WHERE product_id = :product_id ";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $id=htmlspecialchars(strip_tags($id));

    // bind the values
    $stmt->bindParam(':product_id',$id);

    if($stmt->execute()){
        return true;
    }

    print($stmt->error);
    return false;

}


public function getAllProducts(){
 
    // query to check if email exists
    $query = "SELECT *
            FROM " . $this->table . " WHERE vendor_id = :vendor_id ";
 
    // prepare the query
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(':vendor_id', $this->vendor_id);

    // execute the query   
   if( $stmt->execute()){
    return $stmt;
   }
    // return false if email does not exist in the database
    return false;
}

public function insert($field,$value){

    // insert query
    $query = " UPDATE " . $this->table. "
    SET
        ".$field ." = :field   
    WHERE product_id = :id";

// prepare the query
$stmt = $this->conn->prepare($query);
// bind the values
$stmt->bindParam(':field', $value);
$stmt->bindParam(':id', $this->product_id);


if($stmt->execute()){
return true;
}

print($stmt->error);
return false;

}



}