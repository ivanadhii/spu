<?php

namespace App\Libraries;
use CodeIgniter\Log\Logger;
use Psr\Log\LogLevel;

class CustomLogger extends Logger
{
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        try {
            $ignoredMessages = [
                'Session: Class initialized using',
            ];

            foreach ($ignoredMessages as $ignoredMessage) {
                if (strpos((string) $message, $ignoredMessage) !== false) {
                    return;
                }
            }

            parent::log($level, $message, $context);
        } catch (\Exception $e) {
            error_log("Logger Error: " . $e->getMessage());
        }
    }
}
