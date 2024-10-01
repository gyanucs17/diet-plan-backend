<?php
namespace App\Model;

class User
{
    private $id;
    private $username;
    private $password;
    private $email;
	
	public function __construct($data) {
        if(isset($data['id']))
		    $this->id = $data['id'] ? $data['id'] : "";
        $this->username = $data['username']? $data['username'] : "";
        if(isset($data['id']))
            $this->password= $data['password']? $data['password'] : "";
        else
            $this->setPassword($data['password']? $data['password'] : "");
        $this->email = $data['email']? $data['email'] : "";
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT); // Hash password
    }
}



?>