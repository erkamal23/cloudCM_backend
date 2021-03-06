<?php
 
/**
 * @author Kamal Kumar
 
 */
 
class DB_Functions {
 
    private $conn;
 
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }
 
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $mobile, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
	$sql="INSERT INTO users(unique_id, name, email, mobile, encrypted_password, salt, created_at, updated_at) VALUES('".$uuid."', '".$name."', '".$email."', '".$mobile."', '".$encrypted_password."', '".$salt."', NOW(), NOW())";
 	if (mysqli_query($this->conn, $sql)) {
            $sql="SELECT * FROM users WHERE email = '".$email."'";
            $result=mysqli_query($this->conn, $sql);
 	    if (mysqli_num_rows($result) > 0) {
		$user=mysqli_fetch_assoc($result);
            	return $user;
	    }else{
               return false;
	    }
        } else {
            return false;
        }
    }
 
    /**
     * Get user by mobile and password
     */
    public function getUserByMobileAndPassword($mobile, $password) {
 
        $sql="SELECT * FROM users WHERE mobile = '".$mobile."'";
        $result=mysqli_query($this->conn, $sql);
 	if (mysqli_num_rows($result) > 0) {
		$user=mysqli_fetch_assoc($result);
		$salt = $user['salt'];
            	$encrypted_password = $user['encrypted_password'];
            	$hash = $this->checkhashSSHA($salt, $password);
            	// check for password equality
            	if ($encrypted_password == $hash) {
                	// user authentication details are correct
                	return $user;
            	}else{
			return false;
		}
	}else{
               	return false;
	}
    }
 
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
 
        $sql="SELECT * FROM users WHERE email = '".$email."'";
        $result=mysqli_query($this->conn, $sql);
 	if (mysqli_num_rows($result) > 0) {
		$user=mysqli_fetch_assoc($result);
		$salt = $user['salt'];
            	$encrypted_password = $user['encrypted_password'];
            	$hash = $this->checkhashSSHA($salt, $password);
            	// check for password equality
            	if ($encrypted_password == $hash) {
                	// user authentication details are correct
                	return $user;
            	}else{
			return false;
		}
	}else{
               	return false;
	}
    }
 
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
 	$sql="SELECT * FROM users WHERE email = '".$email."'";
        $result=mysqli_query($this->conn, $sql);
 	if (mysqli_num_rows($result) > 0) {
		return true;
	}else{
               	return false;
	}
    }
 
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }
 
}
 
?>
