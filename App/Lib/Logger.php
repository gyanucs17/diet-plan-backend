<?php
namespace App\Lib;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Exception;

class Logger
{
    private MonologLogger $logger;

    public function __construct(string $name, string $path) {
        $this->logger = new MonologLogger($name);
        $this->logger->pushHandler(new StreamHandler($path, MonologLogger::DEBUG));
    }

    public function logException(Exception $e): void {
        $this->logger->error($e->getMessage(), [
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
