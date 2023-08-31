<?php

namespace src\Router;

use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private Dispatcher $dispatcher;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(Dispatcher $dispatcher, ResponseFactoryInterface $responseFactory)
    {
        $this->dispatcher = $dispatcher;
        $this->responseFactory = $responseFactory;
    }
    public function handleRequest(ServerRequestInterface $request): void
    {
        $uri = $request->getUri()->getPath();
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $uri);

        switch ($routeInfo[0]) {
            case $this->dispatcher::NOT_FOUND:
                $response = $this->responseFactory->createResponse(404);
                $response->getBody()->write('Not Found');
                break;
            case $this->dispatcher::METHOD_NOT_ALLOWED:
                $response = $this->responseFactory->createResponse(405);
                $response->getBody()->write('Method Not Allowed');
                break;
            case $this->dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                list($controllerClass, $method) = explode('#', $handler);
                $controller = new $controllerClass();
                if ($method === 'getUsers'){
                    $response = $controller->$method();
                    $this->sendResponse($response);
                    break;
                }
                $response = $controller->$method($request);
                $this->sendResponse($response);
                break;
        }
    }
    private function sendResponse(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            header(sprintf('%s: %s', $name, implode(', ', $values)));
        }

        echo $response->getBody();
    }
}
