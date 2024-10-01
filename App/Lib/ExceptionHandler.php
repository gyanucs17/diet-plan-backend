<?php
namespace App\Lib;

use Exception;
use App\Lib\Logger;

class ExceptionHandler
{
    private Logger $logger;
    private Helper $helper;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
        $this->helper = new Helper();
    }

    public function handle(Exception $e, string $defaultMessage): array {
        $this->logger->logException($e);
        return $this->helper->formatErrorResponse($defaultMessage);
    }
}
