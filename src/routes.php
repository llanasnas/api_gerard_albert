<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/',function (Request $request, Response $response, array $args){
    $args["code"] = "200";
    $args["msg"] = "LSNote API v0.1";
    $response= $response->withJson($args,200);
    return $response;
});
// Routes
$app->get('/',function (Request $request, Response $response, array $args){

    return $response;
});


