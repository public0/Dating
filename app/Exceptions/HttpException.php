<?php
namespace App\Exceptions;


class HttpException extends \Exception
{
    public function __construct(string $message, int $statusCode = 500, \Exception $previous = null)
    {
        http_response_code($statusCode);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => $statusCode,
            'error' => $message,
        ]);

        // Stop further execution
        exit;
    }
}