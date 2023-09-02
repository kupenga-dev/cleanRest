<?php

require_once __DIR__ . "/vendor/autoload.php";

use Dotenv\Dotenv;
use Nyholm\Psr7\Factory\Psr17Factory;
use src\Exception\RouterException;
use src\Router\Router;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $route) {
    $route->addRoute('GET', '/api/v1/users', '\src\Controller\UserController#getUsers');
    $route->addRoute('GET', '/api/v1/users/{login:\w+}', '\src\Controller\UserController#show');
    $route->addRoute('POST', '/api/v1/users/create', '\src\Controller\UserController#store');
    $route->addRoute('PUT', '/api/v1/users/update', '\src\Controller\UserController#update');
    $route->addRoute('PATCH', '/api/v1/users/update', '\src\Controller\UserController#partialUpdate');
    $route->addRoute('DELETE', '/api/v1/users/{login:\w+}', '\src\Controller\UserController#destroy');
});
$psr17Factory = new Psr17Factory();
$router = new Router($dispatcher, $psr17Factory);
$headers = getallheaders();
$postData = $_POST;
$getParams = $_GET;
$body = file_get_contents('php://input');
$serverRequest = $psr17Factory->createServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
$serverRequest = $serverRequest->withBody($psr17Factory->createStream($body));
$serverRequest = $serverRequest->withQueryParams($getParams);
try {
    $router->handleRequest($serverRequest);
} catch (ReflectionException $e) {
    throw new RouterException("Router failed:", 0, $e);
}