<?php

namespace App\Libraries;

use CodeIgniter\Log\Handlers\FileHandler;
use Config\Services;

class CustomLogHandler extends FileHandler
{
    protected function handleError($level, $message): bool
    {
        try {
            $request = Services::request();
            
            if ($request) {
                $message = sprintf(
                    '[IP: %s] [Method: %s] [URI: %s] [UA: %s] %s',
                    $request->getIPAddress(),
                    $request->getMethod(),
                    $request->getUri(),
                    $request->getUserAgent()->__toString(),
                    $message
                );
            }

            $filepath = $this->path . 'log-' . date('Y-m-d') . '.' . $this->fileExtension;
            
            if (!is_dir($this->path)) {
                mkdir($this->path, 0755, true);
            }

            return file_put_contents($filepath, $this->format([
                'level' => $level,
                'date' => date('Y-m-d H:i:s'),
                'message' => $message,
            ]) . "\n", FILE_APPEND);

        } catch (\Exception $e) {
            error_log('CustomLogHandler Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function format(array $data): string 
    {
        return sprintf(
            '%s --> %s - %s',
            $data['date'],
            strtoupper($data['level']),
            $data['message']
        );
    }
}
