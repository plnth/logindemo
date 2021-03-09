<?php 

require_once("dbclass.php");

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database("localhost", "root", "", "LoginProject");
    }
    
    public function register($username, $password, $confirmPassword, $email) {
        
        $errors = array();
        $valid = true;
        
        if (strlen($username) < 6 || strlen($username) > 32) {
            $valid = false;
            array_push($errors, "Username must contain from 6 to 32 characters.");
        }
        
        else {
            $whereFields = array("username", "=", $username);
            $this->db->select("users", $whereFields);
            $count = $this->db->count();
            
            if($count > 0) {
                $valid = false;
                array_push($errors, "Username already exists.");
            }
        }
        
        if (strlen($password) < 6 || strlen($password) > 32) {
            $valid = false;
            array_push($errors, "Password must contain from 6 to 32 characters.");
        }
        
        else {
            if ($password != $confirmPassword) {
                $valid = false;
                array_push($errors, "Passwords do not match.");
            }
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valid = false;
            array_push($errors, "Incorrect email address.");
        }
        
        else {
            if(strlen($email) > 100) {
                $valid = false;
                array_push($errors, "Email is too long.");
            }
        }
        
        if ($valid) {
            
            $password = password_hash($password, PASSWORD_DEFAULT);
            
            $fields = array(
            "username" => array(":username" => $username),
            "password" => array(":password" => $password),
            "email" => array(":email" => $email)
            );
            
            $this->db->insert("users", $fields);
            return true;
        }
        
        else {
            return $errors;
        }
    }
}

?>