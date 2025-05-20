<?php

declare(strict_types=1);

namespace Loom\RouterComponent\Tests\Controller;

use Loom\HttpComponent\Response;
use Loom\HttpComponent\StreamBuilder;
use Psr\Http\Message\ResponseInterface;

class PageNotFoundController
{
    public function pageNotFound(): ResponseInterface
    {
        return new Response(
            statusCode: 404,
            reasonPhrase: 'Not Found',
            headers: ['Content-Type' => 'text/html'],
            body: StreamBuilder::build('This is not the page you are looking for.')
        );
    }
}