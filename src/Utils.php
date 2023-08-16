<?php

namespace App;

use Psr\Http\Message\ResponseInterface;

class Utils
{
    public static function jsonResponse(array $tasks, ResponseInterface $response, int $statusCode = 200): ResponseInterface
    {
        $payload = json_encode($tasks);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
