<?php

namespace App\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

trait LoggerTrait
{
    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function hasLogger(): bool
    {
        return !empty($this->logger);
    }

    protected function log(string $level, string $message, array $context = [], array $params = [])
    {
        if ($this->hasLogger()) {
            $finalMessage = '[' . get_class($this) . '] ';

            if (!empty($params)) {
                foreach ($params as $key => $param) {
                    $finalMessage .= sprintf('[%s: %s] ', $key, $param);
                }
            }

            $finalMessage = $finalMessage . $message;

            $this->getLogger()->log($level, $finalMessage, $context);
        }
    }
}
