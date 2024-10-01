<?php 

namespace App\Controller;

use App\Model\User;
use App\Service\AuthService;
use App\Lib\Helper;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 

class AuthController
{
    private AuthService $authService;
    private Helper $helper;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->authService = new AuthService();
        $this->helper = new Helper();
        $this->logger = new Logger('UserLogger', __DIR__ . '/../../logs/user.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * processLogin - Process the login request.
     *
     * @param  array $req 
     * @return array Response array 
     */
    public function processLogin(array $req): array {
        try {
            $data = $this->helper->validateLoginParams($req); // Validation for required params
            
            if (isset($data['status']) && $data['status'] === 'failed') {
                return $data;
            }

            // Pass data to service for login and return
            $data = $this->authService->login($data['username'], $data['password']);
            return $this->helper->respondList($data, 'Login Failed: Invalid username or password.');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Login Failed');
        }
    }

    /**
     * verifyToken - Verify the provided token.
     *
     * @param  string $token 
     * @return int|array User ID or error message  
     */
    public function verifyToken(string $token): int|array {
        try {
            $userId = $this->authService->getUserIdByToken($token)['id'];
            return $userId;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Unauthorized');
        }
    }

    /**
     * register - Register a new user.
     *
     * @param  array $req 
     * @return array Response array 
     */
    public function register(array $req): array {
        try {
            $data = $this->helper->validateLoginParams($req); // Validation for required params
            
            if (isset($data['status']) && $data['status'] === 'failed') {
                return $data;
            }

            $user = new User($data);
            $resp = $this->authService->saveUser($user);
            return $this->helper->respondList($resp, 'User not registered.');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'User not registered.');
        }
    }
}
