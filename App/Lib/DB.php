<?php

namespace App\Lib;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DB {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    private function connect() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        
        $host = $_ENV['DB_HOST'] ?? 'localhost'; // Default to localhost
        $dbname = $_ENV['DB_NAME'] ?? 'your_database'; // Default database name
        $username = $_ENV['DB_USER'] ?? 'root'; // Default user
        $password = $_ENV['DB_PASSWORD'] ?? ''; // Default empty password
        $charset = 'utf8mb4';
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        try {
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Database connection error: " . $e->getMessage());
        }
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId(); // Call the PDO method
    }
}