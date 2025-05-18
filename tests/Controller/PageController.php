<?php

declare(strict_types=1);

namespace Loom\RouterComponent\Tests\Controller;

use Loom\HttpComponent\Response;
use Loom\HttpComponent\StreamBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PageController
{
    public function show(RequestInterface $request, string $page): ResponseInterface
    {
        return new Response(
            200,
            'OK',
            ['Content-Type' => 'text/html'],
            StreamBuilder::build("Page: $page")
        );
    }

    public function edit(RequestInterface $request, string $page): ResponseInterface
    {
        return new Response(
            200,
            'OK',
            ['Content-Type' => 'text/html'],
            StreamBuilder::build("Editing Page: $page")
        );
    }
}