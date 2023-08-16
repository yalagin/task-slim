<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Throwable;

class CustomErrorHandler
{
    public static function handle(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) {
        $payload = ['error' => $exception->getMessage()];

        $response = new Response();
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE)
        );

        return $response;
    }
}
