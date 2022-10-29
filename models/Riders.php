<?php

class Rider {

    // db stuff
private $conn;
private $table = 'riders';

//
public $id;
public $name;
// public $address;
public $password;
public $confirm_password;
public $phone_number;
public $email;
public $gender;
public $dob;
public $photo;
public $verify_code;
public $ref_code;
public $wallet;
public $bank_name;
public $bank_code;
public $account_number;


    //construct with DB
public function __construct($db){

    $this->conn = $db;
}



// function insert with mail

public function insertWithMail($field,$value,$email){

    // insert query
    $query = " UPDATE " . $this->table. "
    SET
        ".$field ." = :field   
    WHERE email = :email ";

// prepare the query
$stmt = $this->conn->prepare($query);
// bind the values
$stmt->bindParam(':field', $value);
$stmt->bindParam(':email', $email);

if($stmt->execute()){
return true;
}

print($stmt->error);
return false;

}

// function insert

public function insert($field,$value){

    // insert query
    $query = " UPDATE " . $this->table. "
    SET
        ".$field ." = :field   
    WHERE id = :id";

// prepare the query
$stmt = $this->conn->prepare($query);
// bind the values
$stmt->bindParam(':field', $value);
$stmt->bindParam(':id', $this->id);


if($stmt->execute()){
return true;
}

print($stmt->error);
return false;

}

 //function create

public function create(){

        // insert query
        $query = " INSERT INTO " . $this->table. "
        SET
            name = :name,
            email = :email,
            password = :password,
            phone_number = :phone_number";

// prepare the query
$stmt = $this->conn->prepare($query);

// sanitize
$this->name=htmlspecialchars(strip_tags($this->name));
$this->email=htmlspecialchars(strip_tags($this->email));
$this->password=htmlspecialchars(strip_tags($this->password));
$this->phone_number=htmlspecialchars(strip_tags($this->phone_number));

 // bind the values
 $stmt->bindParam(':name', $this->name);
 $stmt->bindParam(':email', $this->email);
 $stmt->bindParam(':phone_number', $this->phone_number);

  // hash the password before saving to database
  $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
  $stmt->bindParam(':password', $password_hash);

  if($stmt->execute()){
    return true;
}

print($stmt->error);
return false;

}


// check if given email exist in the database
public function emailExists(){
 
    // query to check if email exists
    $query = "SELECT id,name,password
            FROM " . $this->table . "
            WHERE email = ?
            LIMIT 0,1";
 
    // prepare the query
    $stmt = $this->conn->prepare( $query );
 
    // sanitize
    $this->email=htmlspecialchars(strip_tags($this->email));
 
    // bind given email value
    $stmt->bindParam(1, $this->email);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $num = $stmt->rowCount();
 
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
        // assign values to object properties
        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->password = $row['password'];
 
        // return true because email exists in the database
        return true;
    }
 
    // return false if email does not exist in the database
    return false;
}


// search for id with verify code

public function getRiderId($verify_code){
 
    $query = "SELECT id
            FROM " . $this->table . "
            WHERE verify_code = ?
            LIMIT 0,1";
 
    // prepare the query
    $stmt = $this->conn->prepare( $query );
 
    // sanitize
    $this->id=htmlspecialchars(strip_tags($verify_code));
 
    // bind given email value
    $stmt->bindParam(1, $verify_code);
 
    // execute the query
   
   if( $stmt->execute()){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // assign values to object properties
        $this->id = $row['id'];
    return $this->id;
   }
 
    // return false if verify code does not exist in the database
    return false;
}



public function getRiderIdWithRef($ref_code){
 
    $query = "SELECT id
            FROM " . $this->table . "
            WHERE ref_code = ?
            LIMIT 0,1";
 
    // prepare the query
    $stmt = $this->conn->prepare( $query );
 
    // sanitize
    $this->id=htmlspecialchars(strip_tags($ref_code));
 
    // bind given email value
    $stmt->bindParam(1, $ref_code);
 
    // execute the query
   
   if( $stmt->execute()){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // assign values to object properties
        $this->id = $row['id'];
    return $this->id;
   }
 
    // return false if verify code does not exist in the database
    return false;
}




// update a user record
public function update(){
 
    // if password needs to be updated
    $password_set=!empty($this->password) ? ", password = :password" : "";
 
    // if no posted password, do not update the password
    $query = "UPDATE " . $this->table . "
            SET
                name = :name,
                email = :email,
                dob = :dob,
                phone_number = :phone_number,
                gender = :gender
                {$password_set}
            WHERE id = :id";
 
    // prepare the query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->dob=htmlspecialchars(strip_tags($this->dob));
    $this->gender=htmlspecialchars(strip_tags($this->gender));
    $this->phone_number=htmlspecialchars(strip_tags($this->phone_number));
 
    // bind the values from the form
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':dob', $this->dob);
    $stmt->bindParam(':gender', $this->gender);
    $stmt->bindParam(':phone_number', $this->phone_number);
 
    // hash the password before saving to database
    if(!empty($this->password)){
        $this->password=htmlspecialchars(strip_tags($this->password));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    }
 
    // unique ID of record to be edited
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}

public function updatePassword(){
 
    // if no posted password, do not update the password
    $query = "UPDATE " . $this->table . "
            SET 
                password = :password
            WHERE id = :id";
 
    // prepare the query
    $stmt = $this->conn->prepare($query);
   
    // hash the password before saving to database

        $this->password=htmlspecialchars(strip_tags($this->password));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
   
    // unique ID of record to be edited
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}



public function randString($length) {
    $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $char = str_shuffle($char);
    for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
        $rand .= $char[mt_rand(0, $l)];
    }
    return $rand;
}

public function updateAccountDetails(){
 
 
    // if no posted password, do not update the password
    $query = "UPDATE " . $this->table . "
            SET
                account_number = :account_number,
                bank_name = :bank_name,
                bank_code = :bank_code
            WHERE id = :id";
 
    // prepare the query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->account_number=htmlspecialchars(strip_tags($this->account_number));
    $this->bank_name=htmlspecialchars(strip_tags($this->bank_name));
    $this->bank_code=htmlspecialchars(strip_tags($this->bank_code));

 
    // bind the values from the form
    $stmt->bindParam(':account_number', $this->account_number);
    $stmt->bindParam(':bank_name', $this->bank_name);
    $stmt->bindParam(':bank_code', $this->bank_code);
   
    // unique ID of record to be edited
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}


public function getRiderDetails($id){
 
    // query to check if email exists
    $query = "SELECT *
            FROM " . $this->table . "
            WHERE id = :id";
 
    // prepare the query
    $stmt = $this->conn->prepare( $query );
 
    // sanitize
    $this->id=htmlspecialchars(strip_tags($id));
 
    // bind given email value
    $stmt->bindParam(":id", $id);
 
    // execute the query
   
   if( $stmt->execute()){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // assign values to object properties
        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->email = $row['email'];
        $this->photo = $row['photo'];
        $this->dob = $row['dob'];
        $this->gender = $row['gender'];
        $this->phone_number = $row['phone_number'];
        $this->wallet = $row['wallet'];
        $this->account_number = $row['account_number'];
        $this->bank_name = $row['bank_name'];
        $this->bank_code = $row['bank_code'];
    return true;
   }
 
    // return false if email does not exist in the database
    return false;
}


public function updateWallet($amount,$wallet){

    $wallet = $wallet - $amount;
    $query= " UPDATE ".$this->table. " SET wallet = :wallet WHERE id = :id ";
     // prepare the query
     $stmt = $this->conn->prepare($query);  
     // bind the values from the form
     $stmt->bindParam(':wallet', $wallet);
    
     // unique ID of record to be edited
     $stmt->bindParam(':id', $this->id);
  
     // execute the query
     if($stmt->execute()){
         return true;
     }
  
     return false;
    
}


public function riderTransaction($amount,$reference,$time,$description,$type,$id){

    $query= " INSERT INTO transaction_rider SET description = :description, type = :type, amount = :amount, time = :time, rider_id = :rider_id, reference = :reference ";
     // prepare the query
     $stmt = $this->conn->prepare($query);  
     // sanitize
     $this->id=htmlspecialchars(strip_tags($id));
     $time=htmlspecialchars(strip_tags($time));
     $reference=htmlspecialchars(strip_tags($reference));
     $amount=htmlspecialchars(strip_tags($amount));
     $description=htmlspecialchars(strip_tags($description));
     $type=htmlspecialchars(strip_tags($type));


     // bind the values from the form
     $stmt->bindParam(':reference', $reference);
     $stmt->bindParam(':time', $time);
     $stmt->bindParam(':amount', $amount);
     $stmt->bindParam(':description', $description);
     $stmt->bindParam(':type', $type);
    
     // unique ID of record to be edited
     $stmt->bindParam(':rider_id', $this->id);
  
     // execute the query
     if($stmt->execute()){
         return true;
     }
  
     return false;
    
}



}

