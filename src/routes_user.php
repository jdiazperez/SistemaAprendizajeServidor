<?php
/**
 * PHP version 7.2
 * src\routes_user.php
 */

use Slim\Http\Request;
use Slim\Http\Response;

/** Obtener Cuestiones*/

$app->get(
    $_ENV['RUTA_API'] . '/cuestiones',
    function (Request $request, Response $response): Response {

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
            $req_data['propuestaPorAlumno'], $req_data['idCuestion'])) {

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
        $idUsuario = $entityManager->find(\TDW18\Usuarios\Entity\Usuario::class, $this->jwt->user_id);

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
            $req_data['idSolucion'])) {

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

        /** @var \TDW18\Usuarios\Entity\Solucion $idSolucion */
        $idSolucion = $entityManager->find(\TDW18\Usuarios\Entity\Solucion::class, $req_data['idSolucion']);

        /** @var \TDW18\Usuarios\Entity\Usuario $idUsuario */
        $idUsuario = $entityManager->find(\TDW18\Usuarios\Entity\Usuario::class, $this->jwt->user_id);

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

/**
 * Crear una Cuestion
 */

$app->post($_ENV['RUTA_API'] . '/cuestiones',
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
                        'message' => 'Se necesitan permisos de Maestro'
                    ],
                    403
                );
        } else {
            $req_data = $request->getParsedBody();

            if (!isset($req_data['enunciado'], $req_data['disponible'])) {

                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 422]
                );

                return $response
                    ->withJson(
                        [
                            'code' => 422,
                            'message' => 'Faltan datos de la cuestión'
                        ],
                        422
                    );
            }

            $entityManager = getEntityManager();

            /** @var \TDW18\Usuarios\Entity\Usuario $idUsuario */
            $idUsuario = $entityManager->find(\TDW18\Usuarios\Entity\Usuario::class, $this->jwt->user_id);

            $cuestion = new \TDW18\Usuarios\Entity\Cuestion(
                $req_data['enunciado'],
                $req_data['disponible'],
                $idUsuario
            );

            $entityManager->persist($cuestion);
            $entityManager->flush();

            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' => 201]
            );

            return $response->withJson(['idCuestion' => $cuestion->getId()])->withStatus(201);
        }

    }
)->setName('tdw_post_cuestiones');