<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Dotenv\Dotenv;
use Firebase\JWT\Key;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\IddsiLevelRepository;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 
use Exception;

class AuthService
{
    //-------------------------code------------------------------//
    private $userRepository;
    private $categoryRepository;
    private $iddsiLevelRepository;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->iddsiLevelRepository = new IddsiLevelRepository();
        $this->logger = new Logger('UserLogger', __DIR__ . '/../../logs/user.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * saveUser
     *
     * @param  User $user 
     * @return bool
     */
    public function saveUser($user): bool|array {
        try {
            return $this->userRepository->createUser($user);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'User not register');
        }
       
    }

    /**
     * saveUser
     *
     * @param  array $payload 
     * @return string 
     */
    function generateJwt($payload): string|array {
        try{
            $dotenv = Dotenv::createImmutable(__DIR__. '/../../');
            $dotenv->load();
            $secretKey = $_ENV['SECRET_KEY'];
            
            // Encode the array to a JWT string
            $jwt = JWT::encode($payload, $secretKey, 'HS256');
            return $jwt;
        } catch (Exception $e){
            return $this->exceptionHandler->handle($e, 'Token generation failed');
        } 
       
    }

    /**
     * getUserIdByToken
     *
     * @param  string $token 
     * @return User 
     */
    function getUserIdByToken($token): array {
        $dotenv = Dotenv::createImmutable(__DIR__. '/../../');
        $dotenv->load();
        $secretKey = $_ENV['SECRET_KEY'];
        try {
            // Decode the JWT
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256')); 
            return (array) $decoded; // Return as array
        } catch (ExpiredException $e) {
            return $this->exceptionHandler->handle($e, 'Token has expired');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Token is invalid');
        }
        return null;
    }
    
    /**
     * login
     *
     * @param  string $username 
     * @param  string $password 
     * @return array 
     */
    public function login($username, $password): array { 
        // getting user data
        $userData = $this->userRepository->findUserByUsername($username);
        //Check empty user data and verify password
        if ($userData && password_verify(trim($password), $userData->getPassword())) {
           // Payload for generation token
            $payload = [
                'id' => $userData->getId(),
                'username' => $userData->getUsername(),
                'email' => $userData->getEmail(),
                'exp' => time() + 36000000 // 1 hour expiration
            ];
            $token = $this->generateJwt($payload);
            if($token) {
                //getting category and Iddsilevels
                $categoryData = $this->categoryRepository->fetchCategorylist();
                $iddsiLevelsData = $this->iddsiLevelRepository->fetchIddsiLevels();
                //Managing response
                $response =  array("status" => "success");
                $response['user']['token'] = $token;
                $response['user']['name'] = $userData->getUsername();
                $response['user']['iddsilevel'] = $iddsiLevelsData;
                $response['user']['category'] = $categoryData;
                return $response;
            } else {
                return array("status" => "failed", "msg" => "login Failed");
            }
        } else {
            return array("status" => "failed","msg" => "Invalid credentials.");
        }
    }

}