#!/usr/bin/env php
<?php

// use Swoole\Http\Request;
// use Swoole\Http\Response;
// use Swoole\Http\Server;

// $http = new Server("0.0.0.0", 9502);

// $http->on(
//     "start",
//     function (Server $http) {
//         echo "Swoole HTTP server is started.\n";
//     }
// );
// $http->on(
//     "request",
//     function (Request $request, Response $response) {
//         $response->end("Hello, Worlds!\n");
//     }
// );

// $http->start();

require_once 'vendor/autoload.php';

use Siler\Swoole;
use Siler\Route;

$restHandler = function (array $routeParams){
    Swoole\emit('Hi , here is the siller');
    
};
$restHandler2 = function (array $routeParams){
    Swoole\emit('Hello world , others');
    
};
$handler = [$restHandler, $restHandler2];

$server = function() use ($handler){
    Route\get('/', $handler[0]);
    Route\get('/other', $handler[1]);
    Swoole\emit('Not found', 404);
};

Swoole\http($server)->start();
