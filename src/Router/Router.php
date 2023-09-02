<?php

namespace src\Router;

use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use ReflectionNamedType;

class Router
{
    private Dispatcher $dispatcher;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(Dispatcher $dispatcher, ResponseFactoryInterface $responseFactory)
    {
        $this->dispatcher = $dispatcher;
        $this->responseFactory = $responseFactory;
    }
    /**
     * @throws \ReflectionException
     */
    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $uri);
        $response = $this->responseFactory->createResponse(200);
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
                $response = $this->invokeControllerMethod($controllerClass, $method, $vars, $request);
                break;
        }
        $this->sendResponse($response);
        return $response;
    }
    /**
     * @throws \ReflectionException
     */
    private function invokeControllerMethod(string $controllerClass, string $method, array $vars,
                                            ServerRequestInterface $request): ResponseInterface
    {
        $controller = new $controllerClass();
        $reflectionMethod = new ReflectionMethod($controller, $method);
        $methodParameters = $reflectionMethod->getParameters();
        $methodArguments = [];
        foreach ($methodParameters as $parameter) {
            $parameterType = $parameter->getType();
            if (
                $parameterType instanceof ReflectionNamedType &&
                !$parameterType->isBuiltin() &&
                $parameterType->getName() === ServerRequestInterface::class
            ) {
                $methodArguments[] = $request;
                continue;
            }
            $parameterName = $parameter->getName();
            if (isset($vars[$parameterName])){
                $methodArguments[] = $vars[$parameterName];
            }
        }
        return $reflectionMethod->invokeArgs($controller, $methodArguments);
    }
    private function sendResponse(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());
        echo $response->getBody();
    }
}