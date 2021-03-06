<?php

use Slim\Http\Request;
use Slim\Http\Response;

/** Redireccion maestroGestionCuestiones */

$app->get(
    '/maestroGestionCuestiones',
    function (Request $request, Response $response): Response {
        return $response
            ->withRedirect('/maestroGestionCuestiones.html');
    }
);

/** Redireccion maestroGestionUsuarios */

$app->get(
    '/maestroGestionUsuarios',
    function (Request $request, Response $response): Response {
        return $response
            ->withRedirect('/maestroGestionUsuarios.html');
    }
);