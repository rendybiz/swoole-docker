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
use Siler\GraphQL;

$schema = include __DIR__.'/schemas/schema.php';

$typeDefs = file_get_contents(__DIR__.'/schemas/schema.graphql');


$restHandler = function (array $routeParams){
    Swoole\json(["data"=>'Hi , here is the siller']);
    
};
$restHandler2 = function (array $routeParams){
    Swoole\emit('Hello world , others');
    
};
$gqlHandler = function (array $routeParams) use ($schema){
    GraphQL\init($schema);
    Swoole\json(["data"=>'Hi , Graphql is here'], 401);
};
$handler = [$restHandler, $restHandler2];
$handlerFunc = ['gql'=>$gqlHandler];
$server = function() use ($handler, $handlerFunc){
    Route\get('/', $handler[0]);
    Route\get('/other', $handler[1]);
    Route\post('/gql', $handlerFunc['gql']);
    Swoole\emit('Not found', 404);
};

Swoole\http($server)->start();
