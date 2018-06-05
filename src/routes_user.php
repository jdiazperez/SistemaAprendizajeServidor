<?php
/**
 * PHP version 7.2
 * src\routes_user.php
 */

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\Usuarios\Entity\Usuario;
use TDW18\Usuarios\Messages;

/**
 * Summary: Returns all users
 * Notes: Returns all users from the system that the user has access to.
 *
 * @SWG\Get(
 *     method      = "GET",
 *     path        = "/users",
 *     tags        = { "Users" },
 *     summary     = "Returns all users",
 *     description = "Returns all users from the system that the user has access to.",
 *     operationId = "tdw_cget_users",
 *     security    = {
 *          { "ResultsSecurity": {} }
 *     },
 *     @SWG\Response(
 *          response    = 200,
 *          description = "User array response",
 *          schema      = { "$ref": "#/definitions/UsersArray" }
 *     ),
 *     @SWG\Response(
 *          response    = 401,
 *          ref         = "#/responses/401_Standard_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 403,
 *          ref         = "#/responses/403_Forbidden_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          ref         = "#/responses/404_Resource_Not_Found_Response"
 *     )
 * )
 * @var \Slim\App $app
 */

$app->get(
    $_ENV['RUTA_API'] . '/users',
    function (Request $request, Response $response): Response {
        if (!$this->jwt->isAdmin) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => Messages::MESSAGES['tdw_cget_users_403']
                    ],
                    403
                );
        }

        $usuarios = getEntityManager()
            ->getRepository(Usuario::class)
            ->findAll();
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => $usuarios ? 200 : 404]
        );

        return empty($usuarios)
            ? $response
                ->withJson(
                    [
                        'code' => 404,
                        'message' => Messages::MESSAGES['tdw_cget_users_404']
                    ],
                    404
                )
            : $response
                ->withJson(
                    [
                        'Usuario' => $usuarios
                    ],
                    200
                );
    }
)->setName('tdw_cget_users');

/**
 * Summary: Returns a user based on a single ID
 * Notes: Returns the user identified by &#x60;userId&#x60;.
 *
 * @SWG\Get(
 *     method      = "GET",
 *     path        = "/users/{userId}",
 *     tags        = { "Users" },
 *     summary     = "Returns a user based on a single ID",
 *     description = "Returns the user identified by `userId`.",
 *     operationId = "tdw_get_users",
 *     parameters  = {
 *          { "$ref" = "#/parameters/userId" }
 *     },
 *     security    = {
 *          { "ResultsSecurity": {} }
 *     },
 *     @SWG\Response(
 *          response    = 200,
 *          description = "User",
 *          schema      = { "$ref": "#/definitions/User" }
 *     ),
 *     @SWG\Response(
 *          response    = 401,
 *          ref         = "#/responses/401_Standard_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 403,
 *          ref         = "#/responses/403_Forbidden_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          ref         = "#/responses/404_Resource_Not_Found_Response"
 *     )
 * )
 */
/** @noinspection PhpUnusedParameterInspection */
$app->get(
    $_ENV['RUTA_API'] . '/users/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        if (!$this->jwt->isAdmin && ($this->jwt->user_id !== $args['id'])) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response->withJson(
                [
                    'code' => 403,
                    'message' => Messages::MESSAGES['tdw_get_users_403']
                ],
                403
            );
        } else {
            /** @var Usuario $usuario */
            $usuario = getEntityManager()
                ->getRepository(Usuario::class)
                ->findOneBy(['id' => $args['id']]);

            if ($usuario === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => Messages::MESSAGES['tdw_get_users_404']
                    ],
                    404
                );
            } else {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 200]
                );
                return $response->withJson($usuario, 200);
            }
        }
    }
)->setName('tdw_get_users');

/**
 * Summary: Deletes a user
 * Notes: Deletes the user identified by &#x60;userId&#x60;.
 *
 * @SWG\Delete(
 *     method      = "DELETE",
 *     path        = "/users/{userId}",
 *     tags        = { "Users" },
 *     summary     = "Deletes a user",
 *     description = "Deletes the user identified by `userId`.",
 *     operationId = "tdw_delete_users",
 *     parameters={
 *          { "$ref" = "#/parameters/userId" }
 *     },
 *     security    = {
 *          { "ResultsSecurity": {} }
 *     },
 *     @SWG\Response(
 *          response    = 204,
 *          description = "User deleted <Response body is empty>"
 *     ),
 *     @SWG\Response(
 *          response    = 401,
 *          ref         = "#/responses/401_Standard_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 403,
 *          ref         = "#/responses/403_Forbidden_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          ref         = "#/responses/404_Resource_Not_Found_Response"
 *     )
 * )
 */
/** @noinspection PhpUnusedParameterInspection */
$app->delete(
    $_ENV['RUTA_API'] . '/users/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        if (!$this->jwt->isAdmin && ($this->jwt->user_id !== $args['id'])) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => Messages::MESSAGES['tdw_delete_users_403']
                    ],
                    403
                );
        } else {
            $entityManager = getEntityManager();
            /** @var Usuario $usuario */
            $usuario = $entityManager
                ->getRepository(Usuario::class)
                ->findOneBy(['id' => $args['id']]);

            if ($usuario === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => Messages::MESSAGES['tdw_delete_users_404']
                    ],
                    404
                );
            } else {
                $entityManager->remove($usuario);
                $entityManager->flush();
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 204]
                );

                $response->getBody()->write("User deleted");
                return $response->withStatus(204);
            }
        }
    }
)->setName('tdw_delete_users');

/**
 * Summary: Provides the list of HTTP supported methods
 * Notes: Return a &#x60;Allow&#x60; header with a list of HTTP supported methods.
 *
 * @SWG\Options(
 *     method      = "OPTIONS",
 *     path        = "/users",
 *     tags        = { "Users" },
 *     summary     = "Provides the list of HTTP supported methods",
 *     description = "Return a `Allow` header with a list of HTTP supported methods.",
 *     operationId = "tdw_options_users",
 *     @SWG\Response(
 *          response    = 200,
 *          description = "`Allow` header <Response body is empty>",
 *     )
 * )
 *
 * @SWG\Options(
 *     method      = "OPTIONS",
 *     path        = "/users/{userId}",
 *     tags        = { "Users" },
 *     summary     = "Provides the list of HTTP supported methods",
 *     description = "Return a `Allow` header with a list of HTTP supported methods.",
 *     operationId = "tdw_options_users_id",
 *     parameters={
 *          { "$ref" = "#/parameters/userId" },
 *     },
 *     @SWG\Response(
 *          response    = 200,
 *          description = "`Allow` header <Response body is empty>",
 *     )
 * )
 */
/** @noinspection PhpUnusedParameterInspection */
$app->options(
    $_ENV['RUTA_API'] . '/users[/{id:[0-9]+}]',
    function (Request $request, Response $response, array $args): Response {
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath()
        );

        $methods = isset($args['id'])
            ? ['GET', 'PUT', 'DELETE']
            : ['GET', 'POST'];

        return $response
            ->withAddedHeader(
                'Allow',
                implode(', ', $methods)
            );
    }
)->setName('tdw_options_users');

/**
 * Summary: Creates a new user
 * Notes: Creates a new user
 *
 * @SWG\Post(
 *     method      = "POST",
 *     path        = "/users",
 *     tags        = { "Users" },
 *     summary     = "Creates a new user",
 *     description = "Creates a new user",
 *     operationId = "tdw_post_users",
 *     parameters  = {
 *          {
 *          "name":        "data",
 *          "in":          "body",
 *          "description": "`User` properties to add to the system",
 *          "required":    true,
 *          "schema":      { "$ref": "#/definitions/UserData" }
 *          }
 *     },
 *     security    = {
 *          { "ResultsSecurity": {} }
 *     },
 *     @SWG\Response(
 *          response    = 201,
 *          description = "`Created` User created",
 *          schema      = { "$ref": "#/definitions/User" }
 *     ),
 *     @SWG\Response(
 *          response    = 400,
 *          description = "`Bad Request` Username or email already exists.",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     ),
 *     @SWG\Response(
 *          response    = 401,
 *          ref         = "#/responses/401_Standard_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 403,
 *          ref         = "#/responses/403_Forbidden_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 422,
 *          description = "`Unprocessable entity` Username, e-mail or password is left out",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->post('/api/users',
    function (Request $request, Response $response): Response {

        if (!isset($_POST['nombreUsuario'], $_POST['contrasenia'],
            $_POST['nombre'], $_POST['apellidos'], $_POST['correo'], $_POST['telefono'])) { // 422 - Faltan datos

            return $response
                ->withJson(
                    [
                        'code' => 422,
                        'message' => Messages::MESSAGES['tdw_post_users_422']
                    ],
                    422
                );
        }

        // hay datos -> procesarlos
        $entityManager = getEntityManager();
        $usuarios = $entityManager
            ->getRepository(Usuario::class)
            ->findAll();
        /** @var Usuario $usuario */
        /*        foreach ($usuarios as $usuario) {
                    if ($usuario->getUsername() === $req_data['username'] || $usuario->getEmail() === $req_data['email']) {
                        $this->logger->info(
                            $request->getMethod() . ' ' . $request->getUri()->getPath(),
                            ['uid' => $this->jwt->user_id, 'status' => 400]
                        );
                        return $response
                            ->withJson(
                                [
                                    'code' => 400,
                                    'message' => Messages::MESSAGES['tdw_post_users_400']
                                ],
                                400
                            );
                    }
                }*/

        $usuario = new Usuario(
            $_POST['nombreUsuario'],
            $_POST['contrasenia'],
            $_POST['nombre'],
            $_POST['apellidos'],
            $_POST['correo'],
            $_POST['telefono']);

        $entityManager->persist($usuario);
        $entityManager->flush();

        return $response
            ->withRedirect('./index.html', 204);
    }
)->setName('tdw_post_users');

/**
 * Summary: Updates a user
 * Notes: Updates the user identified by &#x60;userId&#x60;.
 *
 * @SWG\Put(
 *     method      = "PUT",
 *     path        = "/users/{userId}",
 *     tags        = { "Users" },
 *     summary     = "Updates a user",
 *     description = "Updates the user identified by `userId`.",
 *     operationId = "tdw_put_users",
 *     parameters={
 *          { "$ref" = "#/parameters/userId" },
 *          {
 *          "name":        "data",
 *          "in":          "body",
 *          "description": "`User` data to update",
 *          "required":    true,
 *          "schema":      { "$ref": "#/definitions/UserData" }
 *          }
 *     },
 *     security    = {
 *          { "ResultsSecurity": {} }
 *     },
 *     @SWG\Response(
 *          response    = 209,
 *          description = "`Content Returned` User previously existed and is now updated",
 *          schema      = { "$ref": "#/definitions/User" }
 *     ),
 *     @SWG\Response(
 *          response    = 400,
 *          description = "`Bad Request` User name or e-mail already exists",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     ),
 *     @SWG\Response(
 *          response    = 401,
 *          ref         = "#/responses/401_Standard_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 403,
 *          ref         = "#/responses/403_Forbidden_Response"
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          ref         = "#/responses/404_Resource_Not_Found_Response"
 *     )
 * )
 */
$app->put(
    $_ENV['RUTA_API'] . '/users/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {

        if (!$this->jwt->isAdmin && ($this->jwt->user_id !== $args['id'])) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => Messages::MESSAGES['tdw_put_users_403']
                    ],
                    403
                );
        } else {
            $req_data
                = $request->getParsedBody()
                ?? json_decode($request->getBody(), true);

            $entityManager = getEntityManager();
            /** @var Usuario $usuario */
            $usuario = $entityManager
                ->getRepository(Usuario::class)
                ->findOneBy(['id' => $args['id']]);

            if ($usuario === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => Messages::MESSAGES['tdw_put_users_404']
                    ],
                    404
                );
            } else {
                $usuarios = $entityManager
                    ->getRepository(Usuario::class)
                    ->findAll();
                /** @var Usuario $usuarioAux */
                foreach ($usuarios as $usuarioAux) {
                    if ($usuarioAux->getId() !== $args['id'] && ($usuarioAux->getUsername() === $req_data['username'] || $usuarioAux->getEmail() === $req_data['email'])) {
                        $this->logger->info(
                            $request->getMethod() . ' ' . $request->getUri()->getPath(),
                            ['uid' => $this->jwt->user_id, 'status' => 400]
                        );
                        return $response
                            ->withJson(
                                [
                                    'code' => 400,
                                    'message' => Messages::MESSAGES['tdw_put_users_400']
                                ],
                                400
                            );
                    }
                }
                $usuario->setUsername($req_data['username']);
                $usuario->setEmail($req_data['email']);
                $usuario->setPassword($req_data['password']);
                $usuario->setEnabled($req_data['enabled']);
                $usuario->setMaestro($req_data['isMaestro']);
                $usuario->setAdmin($req_data['isAdmin']);
                $entityManager->flush();

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 209]
                );

                return $response->withJson($usuario, 209);

            }
        }
    }
)->setName('tdw_put_users');

/** Obtener Cuestiones*/

$app->get(
    $_ENV['RUTA_API'] . '/cuestiones',
    function (Request $request, Response $response): Response {
        if (!$this->jwt->isMaestro) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => 'Se requieren permisos de Maestro'
                    ],
                    403
                );
        }
        $cuestiones = getEntityManager()
            ->getRepository(\TDW18\Usuarios\Entity\Cuestion::class)
            ->findAll();

        if ($cuestiones === null) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 404]
            );
            return $response->withJson([
                'code' => 404,
                'message' => 'Error al acceder a la base de datos'
            ],
                404);
        } else {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 200]
            );
            return $response
                ->withJson($cuestiones, 200);
        }
    }
)->setName('tdw_get_cuestiones');


/** Eliminar una cuestión dado su id */

$app->delete(
    $_ENV['RUTA_API'] . '/cuestiones/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        if (!$this->jwt->isMaestro) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );
            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => 'Se requieren permisos de Maestro'
                    ],
                    403
                );
        } else {
            $entityManager = getEntityManager();
            /** @var \TDW18\Usuarios\Entity\Cuestion $cuestion */
            $cuestion = $entityManager
                ->getRepository(\TDW18\Usuarios\Entity\Cuestion::class)
                ->findOneBy(['id' => $args['id']]);

            if ($cuestion === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => 'No existe esa cuestión'
                    ],
                    404
                );
            } else {
                $entityManager->remove($cuestion);
                $entityManager->flush();

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 204]
                );
                return $response->withStatus(204);
            }
        }
    }
)->setName('tdw_delete_cuestiones');


/** Obtener soluciones dado el id de la cuestión a la que pertenecen */

$app->get(
    $_ENV['RUTA_API'] . '/soluciones/{idCuestion:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        $soluciones = getEntityManager()
            ->getRepository(\TDW18\Usuarios\Entity\Solucion::class)
            ->findBy(['idCuestion' => $args['idCuestion']]);

        if ($soluciones === null) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 404]
            );
            return $response->withJson([
                'code' => 404,
                'message' => 'Error al acceder a la base de datos'
            ],
                404);
        } else {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 200]
            );
            return $response
                ->withJson($soluciones, 200);
        }
    }
)->setName('tdw_get_soluciones');

/** Obtener razonamientos dado el id de la solución a la que pertenecen */

$app->get(
    $_ENV['RUTA_API'] . '/razonamientos/{idSolucion:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        $razonamientos = getEntityManager()
            ->getRepository(\TDW18\Usuarios\Entity\Razonamiento::class)
            ->findBy(['idSolucion' => $args['idSolucion']]);

        if ($razonamientos === null) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 404]
            );
            return $response->withJson([
                'code' => 404,
                'message' => 'Error al acceder a la base de datos'
            ],
                404);
        } else {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 200]
            );

            return $response
                ->withJson($razonamientos, 200);
        }
    }
)->setName('tdw_get_razonamientos');

/** Eliminar una solución dado su id */

$app->delete(
    $_ENV['RUTA_API'] . '/soluciones/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        if (!$this->jwt->isMaestro) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );
            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => 'Se requieren permisos de Maestro'
                    ],
                    403
                );
        } else {
            $entityManager = getEntityManager();
            /** @var \TDW18\Usuarios\Entity\Solucion $solucion */
            $solucion = $entityManager
                ->getRepository(\TDW18\Usuarios\Entity\Solucion::class)
                ->findOneBy(['id' => $args['id']]);

            if ($solucion === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => 'No existe esa solución'
                    ],
                    404
                );
            } else {
                $entityManager->remove($solucion);
                $entityManager->flush();

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 204]
                );
                return $response->withStatus(204);
            }
        }
    }
)->setName('tdw_delete_soluciones');


/** Eliminar un razonamiento dado su id */

$app->delete(
    $_ENV['RUTA_API'] . '/razonamientos/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        if (!$this->jwt->isMaestro) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );
            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => 'Se requieren permisos de Maestro'
                    ],
                    403
                );
        } else {
            $entityManager = getEntityManager();
            /** @var \TDW18\Usuarios\Entity\Razonamiento $razonamiento */
            $razonamiento = $entityManager
                ->getRepository(\TDW18\Usuarios\Entity\Razonamiento::class)
                ->findOneBy(['id' => $args['id']]);

            if ($razonamiento === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => 'No existe ese razonamiento'
                    ],
                    404
                );
            } else {
                $entityManager->remove($razonamiento);
                $entityManager->flush();

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 204]
                );
                return $response->withStatus(204);
            }
        }
    }
)->setName('tdw_delete_razonamientos');


/** Actualizar Cuestion */

$app->put(
    $_ENV['RUTA_API'] . '/cuestiones/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        if (!$this->jwt->isMaestro) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => 'Se necesitan permisos de Maestro'
                    ],
                    403
                );
        } else {
            $req_data = $request->getParsedBody();

            $entityManager = getEntityManager();
            /** @var \TDW18\Usuarios\Entity\Cuestion $cuestion */
            $cuestion = $entityManager
                ->find(\TDW18\Usuarios\Entity\Cuestion::class, $args['id']);

            if ($cuestion === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => 'Error'
                    ],
                    404
                );
            } else {
                $cuestion->setEnunciado($req_data['enunciado']);
                $cuestion->setDisponible($req_data['disponible']);

                $entityManager->flush();

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 201]
                );
                return $response->withStatus(201);
            }
        }
    }
)->setName('tdw_put_cuestiones');

/** Actualizar Solucion */

$app->put(
    $_ENV['RUTA_API'] . '/soluciones/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        if (!$this->jwt->isMaestro) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => 'Se necesitan permisos de Maestro'
                    ],
                    403
                );
        } else {
            $req_data = $request->getParsedBody();

            $entityManager = getEntityManager();
            /** @var \TDW18\Usuarios\Entity\Solucion $solucion */
            $solucion = $entityManager
                ->find(\TDW18\Usuarios\Entity\Solucion::class, $args['id']);

            if ($solucion === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => 'Error'
                    ],
                    404
                );
            } else {
                $solucion->setRespuesta($req_data['respuesta']);
                $solucion->setCorrecta($req_data['correcta']);
                $solucion->setPropuestaPorAlumno($req_data['propuestaPorAlumno']);

                $entityManager->flush();

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 201]
                );
                return $response->withStatus(201);
            }
        }
    }
)->setName('tdw_put_soluciones');

/**
 * Crear una Solución
 */

$app->post($_ENV['RUTA_API'] . '/soluciones',
    function (Request $request, Response $response): Response {

        $req_data = $request->getParsedBody();

        if (!isset($req_data['respuesta'], $req_data['correcta'],
            $req_data['propuestaPorAlumno'], $req_data['idCuestion'], $req_data['idUsuario'])) {

            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 422]
            );

            return $response
                ->withJson(
                    [
                        'code' => 422,
                        'message' => 'Faltan datos de la solución'
                    ],
                    422
                );
        }

        $entityManager = getEntityManager();

        /** @var \TDW18\Usuarios\Entity\Cuestion $idCuestion */
        $idCuestion = $entityManager->find(\TDW18\Usuarios\Entity\Cuestion::class, $req_data['idCuestion']);

        /** @var \TDW18\Usuarios\Entity\Usuario $idUsuario */
        $idUsuario = $entityManager->find(\TDW18\Usuarios\Entity\Usuario::class, $req_data['idUsuario']);

        $solucion = new \TDW18\Usuarios\Entity\Solucion(
            $req_data['respuesta'],
            $req_data['correcta'],
            $req_data['propuestaPorAlumno'],
            $idCuestion,
            $idUsuario
        );

        $entityManager->persist($solucion);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => 201]
        );

        return $response->withJson(['idSolucion' => $solucion->getId()])->withStatus(201);
    }
)->setName('tdw_post_soluciones');

/** Actualizar Razonamiento */

$app->put(
    $_ENV['RUTA_API'] . '/razonamientos/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        if (!$this->jwt->isMaestro) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 403]
            );

            return $response
                ->withJson(
                    [
                        'code' => 403,
                        'message' => 'Se necesitan permisos de Maestro'
                    ],
                    403
                );
        } else {
            $req_data = $request->getParsedBody();

            $entityManager = getEntityManager();
            /** @var \TDW18\Usuarios\Entity\Razonamiento $razonamiento */
            $razonamiento = $entityManager
                ->find(\TDW18\Usuarios\Entity\Razonamiento::class, $args['id']);

            if ($razonamiento === null) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 404]
                );
                return $response->withJson(
                    [
                        'code' => 404,
                        'message' => 'Error'
                    ],
                    404
                );
            } else {
                $razonamiento->setTexto($req_data['texto']);
                $razonamiento->setJustificado($req_data['justificado']);
                $razonamiento->setError($req_data['error']);
                $razonamiento->setPropuestoPorAlumno($req_data['propuestoPorAlumno']);

                $entityManager->flush();

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 201]
                );
                return $response->withStatus(201);
            }
        }
    }
)->setName('tdw_put_razonamientos');

/**
 * Crear un Razonamiento
 */

$app->post($_ENV['RUTA_API'] . '/razonamientos',
    function (Request $request, Response $response): Response {

        $req_data = $request->getParsedBody();

        if (!isset($req_data['texto'], $req_data['justificado'],
            $req_data['error'], $req_data['propuestoPorAlumno'],
            $req_data['idSolucion'], $req_data['idUsuario'])) {

            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 422]
            );

            return $response
                ->withJson(
                    [
                        'code' => 422,
                        'message' => 'Faltan datos del razonamiento'
                    ],
                    422
                );
        }

        $entityManager = getEntityManager();

        $this->logger->addInfo('idSolucion: ' . $req_data['idSolucion']);

        /** @var \TDW18\Usuarios\Entity\Solucion $idSolucion */
        $idSolucion = $entityManager->find(\TDW18\Usuarios\Entity\Solucion::class, $req_data['idSolucion']);

        /** @var \TDW18\Usuarios\Entity\Usuario $idUsuario */
        $idUsuario = $entityManager->find(\TDW18\Usuarios\Entity\Usuario::class, $req_data['idUsuario']);

        $razonamiento = new \TDW18\Usuarios\Entity\Razonamiento(
            $req_data['texto'],
            $req_data['justificado'],
            $req_data['error'],
            $req_data['propuestoPorAlumno'],
            $idSolucion,
            $idUsuario
        );

        $entityManager->persist($razonamiento);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => 201]
        );

        return $response->withStatus(201);
    }
)->setName('tdw_post_razonamientos');