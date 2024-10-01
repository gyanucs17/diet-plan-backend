<?php

namespace App\Lib;

use Dotenv\Dotenv;

class Config {
    private static $settings = [];

    public static function load() {
        LoadEnv::load(); 
        
        self::$settings = [
            'db_host' => $_ENV['DB_HOST'] ?? null,
            'db_name' => $_ENV['DB_NAME'] ?? null,
            'db_user' => $_ENV['DB_USER'] ?? null,
            'db_pass' => $_ENV['DB_PASS'] ?? null,
            'response_codes' => [
                'success' => 200,
                'created' => 201,
                'conflict' => 409,
                'bad_request' => 400,
                'unauthorized' => 401,
                'forbidden' => 403,
                'not_found' => 404,
                'internal_server_error' => 500,
            ],
            'app_env' => getenv('APP_ENV') ?: 'production', // Default to 'production' if not set
            'app_debug' => getenv('APP_DEBUG') === 'true',
            'log_file' => getenv('LOG_FILE_PATH') ?: __DIR__ . '/../logs/app.log', // Default log file path
            'jwt' => [
                'secret' => getenv('JWT_SECRET') ?: 'your_default_jwt_secret', // Default JWT secret
                'expiration' => getenv('JWT_EXPIRATION') ?: 3600, // Default expiration time in seconds
            ],
        ];
    }

    // Method to retrieve a setting
    public static function get($key) {
        return self::$settings[$key] ?? null;
    }
}