<?php

require_once __DIR__ . "/vendor/autoload.php";

use Dotenv\Dotenv;
use Nyholm\Psr7\Factory\Psr17Factory;
use src\Exception\RouterException;
use src\Router\Router;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $route) {
    $route->addRoute('GET', 'api/v1/users', 'UserController#getUsers');
    $route->addRoute('GET', 'api/v1/users/{login:\w+}', 'UserController#showByLogin');
    $route->addRoute('POST', 'api/v1/users/create', 'UserController#store');
    $route->addRoute('PUT', 'api/v1/users/{login:\w+}', 'UserController#update');
    $route->addRoute('PATCH', 'api/v1/users/{login:\w+}', 'UserController#partialUpdate');
    $route->addRoute('DELETE', 'api/v1/users/{login:\w+}', 'UserController#destroy');
});
$psr17Factory = new Psr17Factory();
$router = new Router($dispatcher, $psr17Factory);
$serverRequest = $psr17Factory->createServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
try {
    $router->handleRequest($serverRequest);
} catch (ReflectionException $e) {
    throw new RouterException("Router failed:", 0, $e);
}