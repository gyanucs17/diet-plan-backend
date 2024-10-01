<?php
namespace App\Repository;


use App\Lib\DB;
use App\Model\User;
use PDO;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 

class UserRepository
{
    //------------------------------code---------------------------------//
    private $conn = NULL;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;
	
	public function __construct() {
		$this->conn = DB::getInstance(); 
        $this->logger = new Logger('UserLogger', __DIR__ . '/../../logs/user.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * createUser
     *
     * @param  User $iddsiLevel
     * @return bool
     */
    public function createUser($user): bool|array { // Inserting User
        try{
            $username = $user->getUsername();
            $email = $user->getEmail();
            $password = $user->getPassword();

            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->conn->prepare($query);
            if (!($this->findUserByEmail($email) || $this->findUserByUsername($username))) {
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
            
                if ($stmt->execute()) {
                    return array("status"=>"success", "msg" => "User saved successfully"); // Return the last inserted ID
                } else {
                    // Debug: Get error information
                    $errorInfo = $stmt->errorInfo();
                    return array("status"=>"failed", "msg" => "Error executing statement: " . $errorInfo[2]);
                    
                }
            } else {
                return array("status"=>"Not Acceptable", "msg" => "User already exists."); 
            }
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'User not register.');
        }
        
       

        return false; // Return false on failure
    }

    /**
     * findUserByUsername
     *
     * @param  string $username
     * @return User
     */
    public function findUserByUsername($username) {
        try{
            $stmt = $this->conn->prepare("SELECT id, username, email, password FROM users WHERE username = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if($data){
                return new User($data);
            }
            return $data;   

        } catch (Exception $e) {
            return false;
        }
        
    }

    /**
     * findUserByEmail
     *
     * @param  string $email
     * @return User
     */
    public function findUserByEmail($email) {
        try{
            $stmt = $this->conn->prepare("SELECT id, username, email, password FROM users WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if($data){
                return new User($data);
            }
            return $data;
        } catch (Exception $e) {
            return false;
        }
    }

    //-------------------------------code-------------------------------//
}

?>