<?php

declare(strict_types=1);

namespace Loom\RouterComponent\Tests\Controller;

use Loom\HttpComponent\Response;
use Loom\HttpComponent\StreamBuilder;
use Psr\Http\Message\ResponseInterface;

class IndexController
{
    public function index(): ResponseInterface
    {
        return new Response(
            statusCode: 200,
            reasonPhrase: 'OK',
            headers: ['Content-Type' => 'text/html'],
            body: StreamBuilder::build('Hello, World!')
        );
    }
}